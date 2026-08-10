<div>
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

    {{-- FILTER --}}
    <div class="p-4 mb-6 bg-white dark:bg-zinc-800 rounded-lg shadow">
        <div class="flex flex-wrap gap-4 items-end">

            <!-- Selectbox untuk filter pekerjaan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white">Pekerjaan</label>
                <select wire:model="selectedJob"
                    class="appearance-none mt-1 block w-full rounded-md border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 shadow-sm text-gray-700 dark:text-white">
                    <option value="">Semua Pekerjaan</option>
                    @foreach ($jobs as $job)
                        <option value="{{ $job->id }}">{{ $job->job_place }}</option>
                    @endforeach
                </select>
            </div>


            <!-- Input Nama Warga -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white">Nama Warga</label>
                <input type="text" wire:model.debounce.500ms="searchName"
                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-700 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 shadow-sm"
                    placeholder="Cari nama warga...">
            </div>

            <!-- Tombol Filter -->
            <div class="inline-block  bg-blue-600 text-white rounded hover:bg-blue-700">
                <button wire:click="applyFilter" wire:loading.attr="disabled" wire:target="applyFilter"
                    class="inline-block px-4 py-2">
                    {{-- Normal --}}
                    <span wire:loading.remove>Terapkan Filter</span>

                    {{-- Saat loading --}}
                    <span wire:loading class="flex items-center">
                        <div class="flex items-center">
                            <svg class="animate-spin w-[0.8rem] mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                </path>
                            </svg>
                            Terapkan Filter
                        </div>
                    </span>
                </button>
            </div>

            <!-- Tombol Reset -->
            {{-- <button 
                 wire:click="resetFilters"
                 class="text-gray-600 hover:text-gray-800 ml-2 text-sm border border-gray-300 px-3 py-2 rounded-lg"
             >
                 Reset
             </button> --}}
        </div>
    </div>

    <!-- TABEL DATA -->
    <div class="overflow-x-auto bg-gray-100 dark:bg-zinc-600 rounded-lg shadow">
        <table class="min-w-full border-zinc-700">
            <thead class="bg-gray-200 dark:bg-zinc-800 text-gray-800 dark:text-white">
                <th class="px-4 py-2">Tempat Kerja</th>
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">Tahun Masuk</th>
                <th class="px-4 py-2">Tahun Berlangsung</th>
            </thead>
            <tbody>
                @forelse ($civilians as $civilian)
                    <tr class="text-gray-800 dark:text-white">
                        <td class="px-4 py-2 text-center">
                            <div class="text-center text-blue-400">
                                @foreach ($civilian->civilian_jobs as $job)
                                    {{ $job->job_place }}
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-2 text-center">{{ $civilian->full_name }}</td>
                        <td class="px-4 py-2 text-center">
                            @foreach ($civilian->civilian_jobs as $job)
                                {{ $job->pivot->accepted_date }}
                                @if (!$loop->last)
                                    <br>
                                @endif
                            @endforeach
                        </td>
                        <td class="px-4 py-2 text-center">
                            @foreach ($civilian->civilian_jobs as $job)
                                {{ $job->pivot->retirement_date }}
                                @if (!$loop->last)
                                    <br>
                                @endif
                            @endforeach
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Tidak ada data yang ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>


    {{ $civilians->links() }}
</div>
