<?php

namespace App\Livewire;

use App\Models\Expense;
use Livewire\Component;
use App\Models\Category;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use App\Models\CivilianPivotSubscription;

class KeuanganView extends Component
{
    public $selectedSubscription = ''; // Ubah nama variabel
    public $subscriptions; // Simpan daftar iuran
    public $transactions = [];
    public $isLoading = false;
    public $showExpenseForm = false;
    public $expenseName = '';
    public $expenseAmount = '';
    public $expenses = []; // Tambah variabel untuk menyimpan data pengeluaran
    public $activeRowId; // Tambahkan ini untuk menyimpan ID baris yang aktif

    // Tambahkan property untuk form
    public $transactionAmount;
    public $transactionDescription;

    public $initialBalance;        // nilai input user
    public $hasInitialBalance;     // flag sudah di-set?

    public function mount()
    {
        // Load semua iuran
        $this->subscriptions = Subscription::with('category')->get(); 

        // Set default ke air A atau item pertama
        $this->selectedSubscription = $this->subscriptions->firstWhere('name', 'air A')?->id
            ?? $this->subscriptions->first()->id;

        // load initial balance
        $this->updateInitialBalance($this->selectedSubscription);

        $this->applyFilter();
        $this->loadExpenses();
    }

    private function updateInitialBalance($subscriptionId)
    {
        $sub = Subscription::find($subscriptionId);
        $this->initialBalance = $sub->initial_balance;
        $this->hasInitialBalance = ! is_null($sub->initial_balance);
    }

    public function updatedSelectedSubscription($value)
    {
        $this->updateInitialBalance($this->selectedSubscription);
        $this->applyFilter();
        $this->loadExpenses();
    }


    public function applyFilter()
    {
        $this->isLoading = true;

        $query = Expense::query()
            ->when($this->selectedSubscription, fn($q) => $q->where('subscription_id', $this->selectedSubscription))
            ->orderBy('created_at');

        // saldo awal diwakili oleh record Expense "Saldo Awal" (income),
        // jadi perhitungan dimulai dari 0 untuk mencegah double-counting.
        $balance = 0;

        $this->transactions = $query->get()
            ->map(function ($expense) use (&$balance) {
                $description = $expense->is_income
                    ? "iuran " . ($expense->civilianPivotSubscription->civilian->full_name ?? 'N/A')
                    : $expense->expense_name;
                if ($expense->is_income) {
                    if (str_contains(strtolower($expense->expense_name), 'saldo') ) {
                        $description = $expense->expense_name;
                    }  
                    $description = "iuran " . ($expense->civilianPivotSubscription->civilian->full_name ?? 'N/A');
                }

                $description = $expense->expense_name;

                $balance += $expense->is_income ? $expense->amount : -$expense->amount;

                return [
                    'created_at' => $expense->created_at, // Sertakan created_at
                    'description' => $description,
                    'incoming' => $expense->is_income ? $expense->amount : 0,
                    'outgoing' => !$expense->is_income ? $expense->amount : 0,
                    'balance' => $balance,
                    'type' => $expense->is_income ? 'income' : 'expense'
                ];
            })
            ->toArray();

        $this->isLoading = false;
    }

    // Method untuk load data pengeluaran
    public function loadExpenses()
    {
        $query = Expense::query()
            ->when($this->selectedSubscription, fn($q) => $q->where('subscription_id', $this->selectedSubscription));

        $this->expenses = $query->get();
    }


    private function calculateCurrentBalance()
    {
        return Expense::where('subscription_id', $this->selectedSubscription)
            ->get()
            ->reduce(function ($balance, $expense) {
                return $expense->is_income 
                    ? $balance + $expense->amount 
                    : $balance - $expense->amount;
            }, 0);
    }

    public function saveInitialBalance()
    {
        // Bersihkan pemisah ribuan (mis. "10.000" -> 10000)
        $this->initialBalance = (float) preg_replace('/[^0-9]/', '', (string) $this->initialBalance);

        $this->validate([
            'initialBalance' => 'required|numeric',
        ]);


        
        // Pastikan belum pernah di-set
        $sub = Subscription::find($this->selectedSubscription);
        $sub->update(['initial_balance' => $this->initialBalance]);
        
        
        Expense::create([
            'expense_name' => 'Saldo Awal',
            'amount' => $this->initialBalance,
            'is_income' => true,
            'subscription_id' => $this->selectedSubscription,
        ]);
        
        // Tandai bahwa saldo awal sudah diatur
        $this->hasInitialBalance = true;

        // // Reset form (opsional)
        $this->initialBalance = null;

        // Refresh data transaksi
        $this->applyFilter(); 
    }



    // Method untuk menyimpan transaksi
    public function saveTransaction()
    {
        // Bersihkan pemisah ribuan (mis. "10.000" -> 10000)
        $this->transactionAmount = (float) preg_replace('/[^0-9]/', '', (string) $this->transactionAmount);

        $this->validate([
            'transactionAmount' => 'required|numeric',
            'transactionDescription' => 'required',
        ]);

        // Auto-detect jenis transaksi
        $isIncome = str_contains(strtolower($this->transactionDescription), 'iuran');
        
        // Validasi khusus pengeluaran
        if (!$isIncome) {
            $currentBalance = $this->calculateCurrentBalance();
            
            if ($this->transactionAmount > $currentBalance) {
                $this->dispatch('notify', 
                    type: 'error',
                    message: 'Saldo tidak mencukupi! Saldo tersedia: Rp'.number_format($currentBalance, 0, ',', '.')
                );
                return;
            }
        }

        Expense::create([
            'expense_name' => $this->transactionDescription,
            'amount' => $this->transactionAmount,
            'is_income' => $isIncome,
            'subscription_id' => $this->selectedSubscription,
        ]);

        $this->reset(['transactionAmount', 'transactionDescription']);
        $this->applyFilter();
    }

    public function render()
    {
        return view('livewire.keuangan-view');
    }
}


// $description = $expense->is_income
//                     ? "iuran " . ($expense->civilianPivotSubscription->civilian->full_name ?? 'N/A')
//                     : $expense->expense_name;
//                 if ($expense->is_income) {
//                     if (str_contains(strtolower($expense->expense_name), 'saldo') ) {
//                         $description = $expense->expense_name;
//                     }  
//                     $description = "iuran " . ($expense->civilianPivotSubscription->civilian->full_name ?? 'N/A');
//                 }