<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <style>
        /* Style untuk tabel */
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
        }

        .custom-table th,
        .custom-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .custom-table th {
            background-color: #f7fafc;
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
            font-size: 0.875rem;
        }

        .custom-table tbody tr:hover {
            background-color: #f0f4f8;
        }

        /* style untuk pagination */
        .pagination-info {
            font-size: 0.875rem;
            color: #4a5568;
        }

        .pagination-select {
            padding: 6px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-size: 0.875rem;
        }
    </style>

    {{-- Per-halaman --}}
    <div class="mb-4 flex items-center justify-between text-gray-700 dark:text-gray-200">
        <span class="pagination-info">
            Menampilkan {{ $subscriptions->firstItem() ?? 0 }}–{{ $subscriptions->lastItem() ?? 0 }} dari {{ $subscriptions->total() }} warga
        </span>
        <div class="flex items-center gap-2">
            <label class="pagination-info">Per halaman:</label>
            <select wire:model="perPage" class="pagination-select bg-white dark:bg-zinc-700 text-gray-700 dark:text-white">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-zinc-800 rounded-lg shadow relative">
        <!-- Tabel untuk Data civilians -->
        <table class="min-w-full">
            <thead class="bg-gray-100 dark:bg-zinc-800 text-gray-800 dark:text-white">
                <th class="px-4 py-2 text-center">Jenis Iuran</th>
                <th class="px-4 py-2 text-center">Nama Warga</th>
                <th class="px-4 py-2 text-center">Total Dibayar</th>
                <th class="px-4 py-2 text-center">Aksi</th>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-zinc-800">
                @foreach ($subscriptions as $subscription)
                    @php
                        $subs = $subscription['model'];
                        $availableMonths = $subscription['availableMonths'];
                    @endphp
                    <tr wire:key="sub-{{ $subs->id }}"
                        class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-2 border text-left">
                            {{ $subs->subscription->name }} -
                            {{ number_format($subs->subscription->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2 border text-left">{{ $subs->civilian->full_name }}</td>
                        <td class="px-4 py-2 border text-left" text-green-400">
                            Rp {{ number_format($subs->debit, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 m-auto whitespace-nowrap dark:text-gray-300" x-data="{ open: false }">
                            <!-- Trigger Button -->
                            <button @click="open = !open" type="button"
                                class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                                Pilih Bulan
                                <span class="ml-2 bg-gray-100 rounded-full px-2 py-0.5 text-xs">
                                    {{ count($subs->paid_months ?? []) }} terpilih
                                </span>
                                <svg class="-mr-1 ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="origin-top-right relative left-4 mt-2 w-56 z-50 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                <div class="py-1 max-h-60 overflow-y-auto relative">
                                    <!-- Month List -->
                                    @foreach ($availableMonths as $monthKey => $monthLabel)
                                        <label
                                            class="flex items-center px-4 py-2 text-sm cursor-pointer hover:bg-gray-50">
                                            <input type="checkbox"
                                                {{ in_array($monthKey, $subs->paid_months ?? []) ? 'checked' : '' }}
                                                wire:change="togglePayment({{ $subs->id }}, '{{ $monthKey }}')"
                                                class="rounded text-green-600 focus:ring-green-500 pr-3">
                                            <span class="text-gray-700 mx-2">{{ $monthLabel }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 text-gray-700 dark:text-gray-200">
        {{ $subscriptions->links() }}
    </div>


</div>
