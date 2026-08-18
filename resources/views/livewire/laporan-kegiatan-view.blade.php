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

    {{-- FILTER --}}
    <div class="p-4 mb-6 bg-white dark:bg-zinc-800 rounded-lg shadow">
        <div class="flex flex-wrap gap-4 items-end">

            {{-- Select Kategori --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white">Kategori</label>
                <select wire:model.defer="selectedCategory"
                    class="appearance-none mt-1 block w-full rounded-md border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 shadow-sm text-gray-600 dark:text-white">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Input Nama Warga --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white">Nama Warga</label>
                <input type="text" wire:model.defer="searchName"
                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-700 dark:text-white placeholder-gray-400 dark:placeholder-gray-300 shadow-sm"
                    placeholder="Cari nama warga..." />
            </div>

            {{-- Tombol Filter --}}
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
        </div>
    </div>

    {{-- TABEL DATA --}}
    <!-- {{-- <div class="overflow-x-auto bg-zinc-600 rounded-lg shadow">
        <table class="min-w-full border-zinc-700">
            <thead class="bg-zinc-800">
                <th class="px-4 py-2 border">No.</th>
                <th class="px-4 py-2 border">Nama Warga</th>
                <th class="px-4 py-2 border">Kategori Warga</th>
                <th class="px-4 py-2 border">Nama Kegiatan</th>
                <th class="px-4 py-2 border">Input Kegiatan</th>
                <th class="px-4 py-2 border">Target</th>
                <th class="px-4 py-2 border">Keterangan</th>
            </thead>
            <tbody>
                @foreach ($data as $idx => $row)
                    @php $count = count($row['activities']); @endphp

                    @foreach ($row['activities'] as $i => $activity)
                    <tr>
                        @if ($i === 0)
                            <td rowspan="{{ $count }}" class="px-4 py-2 text-center">{{ $idx+1 }}.</td>
                            <td rowspan="{{ $count }}" class="px-4 py-2 text-center">{{ $row['full_name'] }}</td>
                            <td rowspan="{{ $count }}" class="px-4 py-2 text-center">{{ $row['category'] }}</td>
                        @endif

            

                        <td class="px-4 py-2 text-center">{{ $activity['activity_name'] }}</td>
                        <td class="px-4 py-2 text-center">{{ $activity['progress'] }}</td>
                        <td class="px-4 py-2 text-center">{{ $activity['target'] }}</td>

                        @php
                            $keterangan = $activity['keterangan'];
                        @endphp

                        <td class="px-4 py-2 border text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $keterangan['color'] }}">
                                {{ $keterangan['label'] }}
                            </span>
                        </td>

                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div> --}} -->

    <div class="overflow-x-auto bg-gray-100 dark:bg-zinc-600 rounded-lg shadow">
        <table class="min-w-full border-zinc-700">
            <thead class="bg-gray-200 dark:bg-zinc-800 text-gray-800 dark:text-white">
                <tr>
                    <th class="px-4 py-2">No.</th>
                    <th class="px-4 py-2">Nama Warga</th>
                    <th class="px-4 py-2">Kategori Warga</th>
                    <th class="px-4 py-2">Nama Kegiatan</th>
                    <th class="px-4 py-2">Input Kegiatan</th>
                    <th class="px-4 py-2">Target</th>
                    <th class="px-4 py-2">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $civIndex => $civ)
                    @php
                        // Hitung total baris untuk warga ini
                        $rowspanW = collect($civ['categories'])->sum(fn($c) => count($c['activities']));
                        $displayWarga = true;
                    @endphp

                    @foreach ($civ['categories'] as $cat)
                        @php
                            $acts = $cat['activities'];
                            $displayKategori = true;
                        @endphp

                        @foreach ($acts as $act)
                            <tr class="hover:bg-gray-200 dark:hover:bg-zinc-500 text-gray-800 dark:text-white">
                                {{-- No. & Nama Warga --}}
                                @if ($displayWarga)
                                    <td rowspan="{{ $rowspanW }}" class="px-4 py-2 text-center">
                                        {{ $civIndex + 1 }}.
                                    </td>
                                    <td rowspan="{{ $rowspanW }}" class="px-4 py-2 text-center">
                                        <x-highlight :text="$civ['full_name']" :query="$searchName" />
                                    </td>
                                    @php $displayWarga = false; @endphp
                                @endif

                                {{-- Kategori --}}
                                @if ($displayKategori)
                                    <td rowspan="{{ count($acts) }}" class="px-4 py-2 text-center">
                                        {{ $cat['name'] }}
                                    </td>
                                    @php $displayKategori = false; @endphp
                                @endif

                                {{-- Data Kegiatan --}}
                                <td class="px-4 py-2 text-center">{{ $act['name'] }}</td>
                                <td class="px-4 py-2 text-center">{{ $act['progress'] }}</td>
                                <td class="px-4 py-2 text-center">{{ $act['target'] }}</td>
                                <td class="px-4 py-2 text-center">
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-semibold {{ $act['keterangan']['color'] }}">
                                        {{ $act['keterangan']['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>


    {{-- Opsi jumlah data per halaman --}}
    <div class="mt-4 px-4 flex items-center gap-2 text-sm text-gray-600 dark:text-gray-200">
        <label>Tampilkan</label>
        <select wire:model.live="perPage"
            class="rounded-md border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-700 dark:text-white px-2 py-1">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
        <span>data per halaman</span>
    </div>

    {{-- pagination links --}}
    <div class="mt-4 px-4">
        {{ $data->links() }}
    </div>


</div>
