<div class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Institutional Audit Trail & Event Logs</h1>
                <p class="text-xs text-zinc-500 mt-1 dark:text-zinc-400">Complete immutable record of all academic screenings, admissions, score inputs, and financial actions.</p>
            </div>
            
            <div class="w-full sm:w-72">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Filter audit actions, description, IP..." 
                    class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs shadow-2xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                >
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-left text-xs dark:divide-zinc-800">
                    <thead class="bg-zinc-50 font-semibold text-zinc-600 dark:bg-zinc-800/60 dark:text-zinc-300">
                        <tr>
                            <th class="px-4 py-3">Timestamp</th>
                            <th class="px-4 py-3">Actor / User</th>
                            <th class="px-4 py-3">Action Event</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($logs as $log)
                            <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition">
                                <td class="px-4 py-3 font-mono text-[11px] text-zinc-500">
                                    {{ $log->created_at->format('d M Y, H:i:s') }}
                                </td>
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">
                                    {{ $log->user?->name ?? 'Automated System' }}
                                </td>
                                <td class="px-4 py-3 font-mono font-semibold text-sky-700 dark:text-sky-300">
                                    {{ $log->action }}
                                </td>
                                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                    {{ $log->description ?? 'Record updated' }}
                                </td>
                                <td class="px-4 py-3 font-mono text-[11px] text-zinc-400">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-xs text-zinc-500">
                                    No audit logs recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $logs->links() }}
            </div>
        </div>

    </div>
</div>
