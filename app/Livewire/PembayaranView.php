<?php

namespace App\Livewire;

use App\Models\Expense;
use Livewire\Component;
use App\Models\Civilian;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\CivilianPivotSubscription;

class PembayaranView extends Component
{
    public $paymentMonths = [];

    /**
     * Generate bulan mulai dari tanggal registrasi hingga Desember tahun ini
     */
    private function generateMonthsFromRegistrationDate($registrationDate)
    {
        $months = [];
        $startDate = Carbon::parse($registrationDate);
        $endDate = Carbon::now()->endOfYear(); // Sampai akhir tahun ini

        $currentDate = $startDate->copy()->startOfMonth();

        while ($currentDate <= $endDate) {
            $key = $currentDate->format('Y-m');
            $label = $currentDate->translatedFormat('F Y'); // Format: "Maret 2023"
            $months[$key] = $label;
            $currentDate->addMonth();
        }

        return $months;
    }

    public function togglePayment($pivotId, $monthKey)
{
    $pivot = CivilianPivotSubscription::with(['subscription', 'civilian'])->findOrFail($pivotId);
    $amount = (float) preg_replace('/[^0-9]/', '', $pivot->subscription->amount);

    DB::transaction(function () use ($pivot, $monthKey, $amount) {
        $currentMonths = $pivot->paid_months ?? [];
        
        if (in_array($monthKey, $currentMonths)) {
            // Jika bulan dicentang ulang (batalkan pembayaran)
            $currentMonths = array_diff($currentMonths, [$monthKey]);
            $pivot->debit -= $amount;
            
            // Hapus record pemasukan terkait
            $data = Expense::where([
                'civilian_pivot_subscription_id' => $pivot->id,
                // 'expense_date' => Carbon::parse($monthKey)->startOfMonth()
            ])->delete();
            // dd($data);
        } else {
            // Jika bulan baru dicentang (catat pembayaran)
            $currentMonths = array_merge($currentMonths, [$monthKey]);
            $pivot->debit += $amount;
            
            // Buat record pemasukan
            $data = Expense::create([
                'expense_name' => 'iuran ' . $pivot->civilian->full_name,
                'amount' => $amount,
                'is_income' => true, // Tambahkan kolom ini di migration
                'expense_date' => Carbon::parse($monthKey)->startOfMonth(),
                'subscription_id' => $pivot->subscription_id,
                'civilian_pivot_subscription_id' => $pivot->id,
            ]);

            // dd($data);
        }

        // dd($pivot);
        $pivot->paid_months = array_values(array_unique($currentMonths));
        $pivot->save();
    });

    $status = in_array($monthKey, $pivot->paid_months ?? []) ? 'dibayar' : 'dibatalkan';
    $monthLabel = Carbon::parse($monthKey)->translatedFormat('F Y');

    activity('Pembayaran Iuran')
        ->causedBy(auth()->user())
        ->performedOn($pivot)
        ->event($status === 'dibayar' ? 'Pembayaran' : 'PembayaranDibatalkan')
        ->tap(function ($activity) {
            $activity->role = auth()->user()?->role;
            $activity->ip_address = request()->ip();
            $activity->user_agent = substr((string) request()->userAgent(), 0, 500);
            $activity->properties = null;
        })
        ->log("Pembayaran iuran {$pivot->civilian->full_name} bulan {$monthLabel} ditandai {$status}");
}

    public function render()
    {
        $subscriptions = CivilianPivotSubscription::with(['subscription', 'civilian'])
            ->get()
            ->map(function ($subscription) {
                // Hitung ulang total_paid jika null / potential error
                if (is_null($subscription->debit)) {
                    $monthlyAmount = (float) preg_replace('/[^0-9]/', '', $subscription->subscription->amount);
                    $subscription->debit = count($subscription->paid_months ?? []) * $monthlyAmount;
                    $subscription->save();
                }
                
                // Return array bukan model Eloquent
                return [
                    'model' => $subscription,
                    'availableMonths' => $this->generateMonthsFromRegistrationDate(
                        $subscription->civilian->created_at
                    )
                ];
            });
    
        return view('livewire.pembayaran-view', [
            'subscriptions' => $subscriptions
        ]);
    }
}
