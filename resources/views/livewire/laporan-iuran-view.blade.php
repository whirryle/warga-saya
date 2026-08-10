<div>
    <style>
        /* Style untuk tabel */
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
        }

        /* ... (keep your existing styles) ... */
    </style>

    <div class="p-4 mb-6 bg-white dark:bg-zinc-800 rounded-lg shadow">
        <h2 class="text-lg font-medium text-gray-800 dark:text-white mb-4">Laporan iuran untuk: {{ $subscriptionType }}
        </h2>

        <div class="w-full md:w-1/3">
            <label for="subscription-select" class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Pilih
                Jenis Iuran</label>
            <select id="subscription-select" wire:model.live="subscriptionId"
                class="appearance-none mt-1 block w-full rounded-md border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 shadow-sm text-gray-600 dark:text-white">
                <option value="">-- Pilih Jenis Iuran --</option>
                @foreach ($subscriptions as $subscription)
                    <option value="{{ $subscription->id }}">{{ $subscription->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($subscriptionId)
        <div class="overflow-x-auto bg-gray-50 dark:bg-zinc-700 rounded-lg shadow">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider bg-zinc-800">
                            Nama Warga</th>
                        @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $month)
                            <th
                                class="px-3 py-3 text-left text-xs font-medium text-white uppercase tracking-wider bg-zinc-800">
                                {{ $month }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-gray-50 dark:bg-zinc-700 divide-y divide-gray-200 dark:divide-zinc-600">
                    @forelse ($civilians as $civilian)
                        <tr>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $civilian['name'] }}

                            </td>
                            @foreach (range(1, 12) as $month)
                                @php
                                    $monthFormatted = date('Y-m', mktime(0, 0, 0, $month, 1, date('Y')));
                                    $isPaid =
                                        in_array($monthFormatted, $civilian['paid_months'] ?? []) ||
                                        in_array($month, $civilian['paid_months'] ?? []);
                                @endphp
                                <td
                                    class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">
                                    @if ($isPaid)
                                        <span class="text-green-500">✓</span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
                                Tidak ada data yang tersedia untuk iuran ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="p-4 bg-white dark:bg-zinc-800 rounded-lg shadow text-center text-gray-700 dark:text-white">
            Silakan pilih jenis iuran untuk melihat laporan
        </div>
    @endif
</div>
