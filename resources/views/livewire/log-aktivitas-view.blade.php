<div>
    <style>
        .log-table { width: 100%; border-collapse: collapse; background-color: #ffffff; border: 1px solid #e2e8f0; }
        .log-table th, .log-table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #e2e8f0; font-size: 0.875rem; }
        .log-table th { background-color: #f7fafc; font-weight: 600; color: #4a5568; text-transform: uppercase; font-size: 0.75rem; }
        .log-table tbody tr:hover { background-color: #f0f4f8; }
        .pagination-info { font-size: 0.875rem; color: #4a5568; }
        .pagination-select { padding: 6px 12px; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 0.875rem; }

        .dark .log-table { background-color: #27272a; border-color: #52525b; }
        .dark .log-table th, .dark .log-table td { border-bottom-color: #52525b; }
        .dark .log-table th { background-color: #18181b; color: #e4e4e7; }
        .dark .log-table tbody tr:hover { background-color: #3f3f46; }
        .dark .pagination-info { color: #e4e4e7; }
        .dark .pagination-select { background-color: #3f3f46; color: #e4e4e7; border-color: #52525b; }
    </style>

    {{-- FILTER --}}
    <div class="p-4 mb-6 bg-white text-black dark:bg-zinc-800 dark:text-white rounded-lg shadow">
        <div class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white">Pengguna</label>
                <select wire:model.defer="selectedUser"
                    class="appearance-none mt-1 block w-full rounded-md border border-gray-300 dark:border-zinc-600 shadow-sm bg-white dark:bg-zinc-700 text-gray-600 dark:text-white">
                    <option value="">Semua Pengguna</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" class="text-gray-600 dark:text-gray-200">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white">Role</label>
                <select wire:model.defer="selectedRole"
                    class="appearance-none mt-1 block w-full rounded-md border border-gray-300 dark:border-zinc-600 shadow-sm bg-white dark:bg-zinc-700 text-gray-600 dark:text-white">
                    <option value="">Semua Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" class="text-gray-600 dark:text-gray-200">{{ $role }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white">Modul</label>
                <select wire:model.defer="selectedLog"
                    class="appearance-none mt-1 block w-full rounded-md border border-gray-300 dark:border-zinc-600 shadow-sm bg-white dark:bg-zinc-700 text-gray-600 dark:text-white">
                    <option value="">Semua Modul</option>
                    @foreach ($logNames as $name)
                        <option value="{{ $name }}" class="text-gray-600 dark:text-gray-200">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white">Event</label>
                <select wire:model.defer="selectedEvent"
                    class="appearance-none mt-1 block w-full rounded-md border border-gray-300 dark:border-zinc-600 shadow-sm bg-white dark:bg-zinc-700 text-gray-600 dark:text-white">
                    <option value="">Semua Event</option>
                    @foreach ($events as $event)
                        <option value="{{ $event }}" class="text-gray-600 dark:text-gray-200">{{ $event }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white">Dari Tanggal</label>
                <input type="date" wire:model.defer="dateFrom"
                    class="appearance-none mt-1 block w-full rounded-md border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-600 dark:text-white shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white">Sampai Tanggal</label>
                <input type="date" wire:model.defer="dateTo"
                    class="appearance-none mt-1 block w-full rounded-md border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-gray-600 dark:text-white shadow-sm">
            </div>

            <div class="inline-block bg-blue-600 text-white rounded hover:bg-blue-700">
                <button wire:click="applyFilter" wire:loading.attr="disabled" wire:target="applyFilter"
                    class="inline-block px-4 py-2">
                    <span wire:loading.remove>Terapkan Filter</span>
                    <span wire:loading class="flex items-center">
                        <svg class="animate-spin w-[0.8rem] mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        Terapkan Filter
                    </span>
                </button>
            </div>

            <button wire:click="resetFilters"
                class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white border border-gray-300 dark:border-zinc-600 rounded hover:bg-gray-100 dark:hover:bg-zinc-700">
                Reset
            </button>
        </div>
    </div>

    {{-- Per-halaman --}}
    <div class="mb-4 flex items-center justify-between text-gray-700 dark:text-gray-200">
        <span class="pagination-info">
            Menampilkan {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} log
        </span>
        <div class="flex items-center gap-2">
            <label class="pagination-info">Per halaman:</label>
            <select wire:model="perPage" class="pagination-select bg-white dark:bg-zinc-700 text-gray-700 dark:text-white">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    {{-- TABEL LOG --}}
    <div class="overflow-x-auto bg-white dark:bg-zinc-800 rounded-lg shadow">
        <table class="log-table">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Waktu</th>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Pengguna</th>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Role</th>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Modul</th>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Aksi</th>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Deskripsi</th>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">IP</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-600 divide-y divide-gray-200 dark:divide-zinc-500">
                @forelse ($logs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="px-4 py-2 text-gray-800 dark:text-white">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-4 py-2 text-gray-800 dark:text-white">
                            {{ $log->causer?->name ?? ($log->properties['email'] ?? '-') }}
                        </td>
                        <td class="px-4 py-2 text-gray-800 dark:text-white">
                            <span class="inline-block px-2 py-0.5 rounded text-xs {{ $log->role === 'super_admin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200' : ($log->role === 'activity_admin' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-600 dark:bg-zinc-700 dark:text-gray-300') }}">
                                {{ $log->role ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-gray-800 dark:text-white">{{ $log->log_name }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-white">{{ $log->event ?? '-' }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-white">{{ $log->description }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-white">{{ $log->ip_address ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-300">
                            Belum ada log aktivitas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 text-gray-700 dark:text-gray-200">
        {{ $logs->links() }}
    </div>
</div>