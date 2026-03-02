<x-app-layout>
    <x-slot name="header">
        <style>
            .audit-table th,
            .audit-table td {
                text-align: left !important;
                vertical-align: top !important;
                padding-left: 1.5rem !important;
                /* px-6 */
                padding-right: 1.5rem !important;
                /* px-6 */
            }

            .audit-table th {
                background-color: #f9fafb;
                /* bg-gray-50 */
                color: #9ca3af;
                /* text-gray-400 */
                font-weight: 700;
                font-size: 0.75rem;
                /* text-xs */
                text-transform: uppercase;
                letter-spacing: 0.05em;
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }
        </style>
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
                {{ __('Audit Logs') }}
            </h2>
            <span class="text-sm text-gray-500">{{ $logs->total() }} total entries</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filters --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-5">Filters</h3>
                <form method="GET" action="{{ route('audit-logs.index') }}">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        {{-- Action --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Action</label>
                            <select name="action"
                                class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-2.5">
                                <option value="">All Actions</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                        {{ config("constants.audit_events.{$action}", $action) }}
                                    </option>
                                @endforeach
                                <option value="LOGIN" {{ request('action') === 'LOGIN' ? 'selected' : '' }}>Login</option>
                                <option value="LOGOUT" {{ request('action') === 'LOGOUT' ? 'selected' : '' }}>Logout
                                </option>
                                <option value="FILE_DELETED" {{ request('action') === 'FILE_DELETED' ? 'selected' : '' }}>
                                    File Deleted</option>
                            </select>
                        </div>

                        {{-- User --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">User</label>
                            <select name="user_id"
                                class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-2.5">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date From --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">From
                                Date</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-2.5">
                        </div>

                        {{-- Date To --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">To
                                Date</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-2.5">
                        </div>
                    </div>
                    <br>
                    {{-- Buttons --}}
                    <div class="flex items-center justify-center gap-3 mt-5 pt-5 border-t border-gray-100">
                        <button type="submit"
                            class="px-5 py-2 bg-gray-800 text-white rounded-xl hover:bg-gray-700 transition duration-200 shadow-sm text-sm font-semibold">
                            Apply Filters
                        </button>
                        <a href="{{ route('audit-logs.index') }}"
                            class="px-5 py-2 bg-white border border-gray-300 rounded-xl font-semibold text-gray-700 hover:bg-gray-50 transition-all shadow-sm text-sm">
                            Reset
                        </a>
                        @if(request()->hasAny(['action', 'user_id', 'date_from', 'date_to']))
                            <span class="text-xs text-indigo-600 font-medium">
                                Filters applied — {{ $logs->total() }} result(s)
                            </span>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Logs Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @if($logs->isEmpty())
                    <div class="py-16 text-center">
                        <div class="text-gray-300 text-5xl mb-3">📋</div>
                        <p class="text-gray-400 italic text-sm">No audit entries match your filters.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full audit-table border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="w-44">Timestamp</th>
                                    <th class="w-52">User</th>
                                    <th class="w-44">Action</th>
                                    <th class="w-36">Subject</th>
                                    <th>Changes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    <tr class="border-b border-gray-50 hover:bg-gray-50/60 transition-colors duration-150">

                                        {{-- Timestamp --}}
                                        <td class="py-5">
                                            <div class="text-sm font-medium text-gray-700">
                                                {{ $log->created_at->format('M d, Y') }}
                                            </div>
                                            <div class="text-xs text-gray-400 mt-0.5">
                                                {{ $log->created_at->format('H:i:s') }}
                                            </div>
                                        </td>

                                        {{-- User --}}
                                        <td class="py-5">
                                            @if($log->user)
                                                <div class="text-sm font-semibold text-gray-900">{{ $log->user->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $log->user->email }}</div>
                                            @else
                                                <span class="text-sm text-gray-400 italic">System</span>
                                            @endif
                                        </td>

                                        {{-- Action Badge --}}
                                        <td class="py-5">
                                            @php
                                                $colours = [
                                                    'LOGIN' => 'bg-green-100 text-green-800',
                                                    'LOGOUT' => 'bg-gray-100 text-gray-700',
                                                    'FILE_CREATED' => 'bg-blue-100 text-blue-800',
                                                    'FILE_UPDATED' => 'bg-yellow-100 text-yellow-800',
                                                    'FILE_DELETED' => 'bg-red-100 text-red-800',
                                                    'STATUS_CHANGED' => 'bg-purple-100 text-purple-800',
                                                ][$log->action] ?? 'bg-indigo-100 text-indigo-800';
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $colours }}">
                                                {{ $log->action }}
                                            </span>
                                        </td>

                                        {{-- Subject --}}
                                        <td class="py-5">
                                            @if($log->auditable_type)
                                                <div class="text-sm text-gray-700 font-medium">
                                                    {{ class_basename($log->auditable_type) }}
                                                </div>
                                                <div class="text-xs text-gray-400 mt-0.5">#{{ $log->auditable_id }}</div>
                                            @else
                                                <span class="text-sm text-gray-400">—</span>
                                            @endif
                                        </td>

                                        {{-- Changes Diff --}}
                                        <td class="py-5">
                                            @if($log->old_values || $log->new_values)
                                                <details class="group">
                                                    <summary
                                                        class="cursor-pointer inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 select-none list-none">
                                                        <svg class="w-3.5 h-3.5 transition-transform group-open:rotate-90"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 5l7 7-7 7" />
                                                        </svg>
                                                        View diff
                                                    </summary>
                                                    <div class="mt-3 space-y-2 text-xs max-w-sm">
                                                        @if($log->old_values)
                                                            <div class="bg-red-50 border border-red-100 rounded-lg p-3">
                                                                <div class="font-bold text-red-600 uppercase tracking-wider mb-2">
                                                                    Before
                                                                </div>
                                                                <div class="space-y-1">
                                                                    @foreach($log->old_values as $key => $value)
                                                                        <div class="flex gap-2">
                                                                            <span
                                                                                class="text-red-500 font-semibold shrink-0">{{ $key }}:</span>
                                                                            <span
                                                                                class="text-red-800 break-all">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if($log->new_values)
                                                            <div class="bg-green-50 border border-green-100 rounded-lg p-3">
                                                                <div class="font-bold text-green-600 uppercase tracking-wider mb-2">
                                                                    After</div>
                                                                <div class="space-y-1">
                                                                    @foreach($log->new_values as $key => $value)
                                                                        <div class="flex gap-2">
                                                                            <span
                                                                                class="text-green-600 font-semibold shrink-0">{{ $key }}:</span>
                                                                            <span
                                                                                class="text-green-900 break-all">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </details>
                                            @else
                                                <span class="text-xs text-gray-400 italic">No data</span>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($logs->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/40">
                            {{ $logs->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>