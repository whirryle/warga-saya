<div>

    <!-- Notifikasi Error -->
    <div x-data="{ showError: false, errorMessage: '' }"
        x-on:notify.window="showError = true; errorMessage = $event.detail.message; setTimeout(() => showError = false, 5000)"
        x-show="showError" x-transition class="fixed inset-x-0 top-4 flex justify-center z-50">
        <div class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center">
            <span x-text="errorMessage" class="font-medium mx-4"></span>
            <button @click="showError = false" class="ml-4 text-white hover:text-gray-200">
                ✕
            </button>
        </div>
    </div>

    {{-- FILTER dan BUTTON --}}
    <div class="p-4 mb-6 bg-white dark:bg-zinc-800 rounded-lg shadow">
        <div class="flex flex-wrap gap-4">

            {{-- BAGIAN FILTER --}}
            <div class="flex flex-wrap gap-4 items-end">
                <!-- Selectbox untuk filter iuran -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Nama Iuran</label>
                    <select wire:model="selectedSubscription"
                        class="appearance-none mt-1 block w-full rounded-md border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 shadow-sm text-gray-700 dark:text-white">
                        @foreach ($subscriptions as $sub)
                            <option value="{{ $sub->id }}">
                                {{ $sub->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol Filter --}}
                <div class="inline-block bg-blue-500 text-white rounded hover:bg-gray-600">
                    <button wire:click="applyFilter" wire:loading.attr="disabled" wire:target="applyFilter"
                        class="inline-block px-4 py-2">
                        {{-- Normal --}}
                        <span wire:loading.remove>Terapkan Filter</span>

                        {{-- Saat loading --}}
                        <span wire:loading class="flex items-center">
                            <div class="flex items-center">
                                <svg class="animate-spin w-[0.8rem] mr-2" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                Terapkan Filter
                            </div>
                        </span>
                    </button>
                </div>
            </div>

            <div class="flex flex-wrap items-end">
                <!-- Tombol Trigger -->
                <div
                    class="inline-block h-8 border relative bottom-1 border-x-slate-500 bg-blue-600 text-white rounded hover:bg-blue-700">

                </div>
            </div>

            {{-- BAGIAN BUTTON KEUANGAN --}}
            <div class="flex flex-wrap gap-4 items-end">

                <!-- Form Tambah Transaksi -->
                <div x-data="{ showForm: false }" @click.outside="showForm = false" class="relative">

                    <!-- Tombol Trigger -->
                    <div class="inline-block bg-blue-600 text-white rounded hover:bg-blue-700">
                        <button @click="showForm = !showForm" class="inline-block px-4 py-2">
                            <span>+ Tambah Transaksi</span>
                        </button>
                    </div>

                    <!-- Dropdown Form -->
                    <div x-cloak x-show="showForm" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute z-10 mt-2 w-90 md:w-96 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700">
                        <div class="p-4">

                            <div class="space-y-4">
                                <!-- Input Jumlah -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah
                                        (Rp)</label>
                                    <input type="number" wire:model="transactionAmount"
                                        class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                        placeholder="0">
                                    @error('transactionAmount')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Input Keterangan -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan</label>
                                    <input type="text" wire:model="transactionDescription"
                                        class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                        placeholder="Contoh: Iuran bulanan">
                                    @error('transactionDescription')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-4 flex items-center justify-end gap-2">

                                <div class="inline-block bg-red-500 text-white text-sm rounded-md hover:bg-red-600">
                                    <button @click="showForm = false" class="inline-block px-4 py-2">
                                        Batal
                                    </button>
                                </div>

                                <div class="inline-block bg-blue-500 text-white text-sm rounded-md hover:bg-blue-600">
                                    <button wire:click="saveTransaction" wire:loading.attr="disabled"
                                        class="inline-block px-4 py-2">
                                        <span wire:loading wire:target="saveTransaction"
                                            class="animate-spin mr-2">↻</span>
                                        Simpan
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                @if (!$hasInitialBalance || $hasInitialBalance == 0)
                    {{-- Form Tambah Saldo Awal --}}
                    <div x-data="{ open: false }" @click.outside="open = false" class="relative">

                        {{-- Tombol Trigger --}}
                        <div class="inline-block bg-green-500 text-white rounded hover:bg-green-600">
                            <button @click="open = !open" class="inline-block px-4 py-2">
                                + Tambah Saldo Awal
                            </button>
                        </div>

                        {{-- Dropdown Form --}}
                        <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1" @click.outside="showForm = false"
                            class="absolute mt-2 bg-white border rounded shadow p-4 w-64">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah
                                (Rp)</label>
                            <input type="number" wire:model="initialBalance"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                placeholder="0">

                            @error('initialBalance')
                                <span class="text-blue-500 text-xs">{{ $message }}</span>
                            @enderror

                            <div class="flex justify-end mt-2 space-x-2">

                                <div class="inline-block bg-red-500 text-white rounded hover:bg-red-600">
                                    <button @click="open = false" class="inline-block px-4 py-2">
                                        Batal
                                    </button>
                                </div>

                                <div class="inline-block bg-blue-500 text-white rounded hover:bg-blue-600">
                                    <button wire:click="saveInitialBalance" class="inline-block px-4 py-2">
                                        Simpan
                                    </button>
                                </div>


                            </div>
                        </div>

                    </div>
                @else
                    {{-- Tombol Trigger --}}
                    <div class="inline-block bg-green-500 opacity-40 text-white rounded cursor-progress">
                        <button disabled class="inline-block px-4 py-2 cursor-not-allowed">
                            + Tambah Saldo Awal
                        </button>
                    </div>
                @endif
            </div>


        </div>
    </div>


    <!-- TABEL DATA -->
    <div class="overflow-x-auto bg-white dark:bg-zinc-800 rounded-lg shadow">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-100 dark:bg-zinc-900 text-gray-800 dark:text-white">
                    <th class="px-4 py-2 text-left">Tanggal</th>
                    <th class="px-4 py-2 text-left">Keterangan</th>
                    <th class="px-4 py-2 text-right">Incoming</th>
                    <th class="px-4 py-2 text-right">Outgoing</th>
                    <th class="px-4 py-2 text-right">Balance</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-zinc-700">
                @foreach ($transactions as $trx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700 dark:text-gray-100">
                        <td class="px-4 py-2 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($trx['created_at'])->format('d M Y') }}
                        </td>
                        <td class="px-4 py-2">
                            {{ $trx['description'] }}
                            @if ($trx['type'] === 'income')
                                <span
                                    class="ml-2 text-xs bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 px-2 py-1 rounded-full">
                                    Pemasukan
                                </span>
                            @else
                                <span
                                    class="ml-2 text-xs bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300 px-2 py-1 rounded-full">
                                    Pengeluaran
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right">
                            @if ($trx['incoming'] > 0)
                                Rp{{ number_format($trx['incoming'], 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right">
                            @if ($trx['outgoing'] > 0)
                                Rp{{ number_format($trx['outgoing'], 0, ',', '.') }}
                            @endif
                        </td>
                        <td
                            class="px-4 py-2 text-right font-medium @if ($trx['balance'] >= 0) text-green-600 dark:text-green-400 @else text-red-600 dark:text-red-400 @endif">
                            Rp{{ number_format($trx['balance'], 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


</div>

{{-- works --}}
