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
    <div class="p-4 mb-6 bg-white text-black dark:bg-zinc-800 dark:text-white rounded-lg shadow">
        <div class="flex flex-wrap gap-4 items-end ">

            <!-- Selectbox untuk filter kategori -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white">Kategori</label>
                <select wire:model.defer="selectedCategory"
                    class="appearance-none mt-1 block w-full rounded-md border border-gray-300 dark:border-zinc-600 shadow-sm bg-white dark:bg-zinc-700 text-gray-600 dark:text-white">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" class="text-gray-600 dark:text-gray-200">{{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Input Pencarian Nama -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white">Nama Warga</label>
                <input type="text" wire:model.debounce.500ms="searchName"
                    class="appearance-none mt-1 block w-full rounded-md border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-600 dark:text-white shadow-sm placeholder-gray-400 dark:placeholder-gray-300"
                    placeholder="Cari nama warga...">
                {{-- <button 
                    wire:click="$set('searchName', '')"
                    class="absolute right-2 top-2 text-gray-400 hover:text-gray-600"
                    style="{{ empty($searchName) ? 'display:none' : '' }}"
                >
                ✕
                </button> --}}
            </div>

            <!-- Tombol Filter -->
            <div class="inline-block bg-blue-600 text-white rounded hover:bg-blue-700">
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

                    {{-- <span wire:loading wire:target="applyFilter" class="animate-spin">⏳</span> --}}
                </button>
            </div>

        </div>



        <!-- Tombol Reset -->
        {{-- <button 
            wire:click="resetFilters"
            class="text-gray-600 hover:text-gray-800 text-sm"
        >
            Reset
        </button> --}}
    </div>

    <!-- TABEL DATA -->
    <div class="overflow-x-auto bg-white dark:bg-zinc-800 rounded-lg shadow">
        <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-zinc-900">
                <tr>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Kategori</th>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Nama lengkap</th>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Umur</th>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Jenis kelamin</th>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">No. HP</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-600 divide-y divide-gray-200 dark:divide-zinc-500">
                @foreach ($civilians as $civilian)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="px-4 py-2">
                            <div class="flex flex-wrap gap-1 text-blue-600 dark:text-blue-400 text-center">
                                @foreach ($civilian->categories as $category)
                                    {{ $category->name }}
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-2 text-center text-gray-800 dark:text-white">
                            <x-highlight :text="$civilian->full_name" :query="$appliedSearch" />
                        </td>
                        <td class="px-4 py-2 text-center text-gray-800 dark:text-white">
                            {{ \Carbon\Carbon::parse($civilian->born_date)->age }} tahun</td>
                        <td class="px-4 py-2 text-center text-gray-800 dark:text-white">
                            @if ($civilian->gender)
                                Wanita
                            @else
                                Pria
                            @endif
                        </td>
                        <td class="px-4 py-2 text-center text-gray-800 dark:text-white">{{ $civilian->phone_number }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>



    <!-- Pagination -->
    <div class="mt-4 text-gray-700 dark:text-gray-200">
        {{ $civilians->links() }}
    </div>
</div>
