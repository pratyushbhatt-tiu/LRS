<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Audit Logs') }}
            </h2>
            <span class="text-sm text-gray-500">{{ $logs->total() }} total entries</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filters --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <form method="GET" action="{{ route('audit-logs.index') }}"
                    class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    {{-- Action --}}
                    <div>
                        <label
                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Action</label>
                        <select name="action"
                            class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">All Actions</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                    {{ config("constants.audit_events.{$action}", $action) }}
                                </option>
                            @endforeach
                            {{-- Login/Logout not in audit_events config, add manually --}}
                            <option value="LOGIN" {{ request('action') === 'LOGIN' ? 'selected' : '' }}>Login</option>
                            <option value="LOGOUT" {{ request('action') === 'LOGOUT' ? 'selected' : '' }}>Logout</option>
                            <option value="FILE_DELETED" {{ request('action') === 'FILE_DELETED' ? 'selected' : '' }}>File
                                Deleted</option>
                        </select>
                    </div>

                    {{-- User --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">User</label>
                        <select name="user_id"
                            class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date From --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    {{-- Date To --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="px-4 py-2 bg-gray-800 text-white rounded-2xl hover:bg-gray-700 transition duration-200 shadow-md text-sm">
                            Filter
                        </button>
                        <a href="{{ route('audit-logs.index') }}"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-xl font-bold text-gray-900 hover:bg-gray-50 transition-all shadow-sm text-sm">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Logs Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @if($logs->isEmpty())
                    <div class="p-12 text-center text-gray-400 italic">
                        No audit entries match your filters.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr
                                    class="text-xs text-gray-400 uppercase font-bold tracking-wider border-b border-gray-100 bg-gray-50/60">
                                    <th class="px-6 py-4 text-left w-40">Timestamp</th>
                                    <th class="px-6 py-4 text-left w-32">User</th>
                                    <th class="px-6 py-4 text-left w-36">Action</th>
                                    <th class="px-6 py-4 text-left">Subject</th>
                                    <th class="px-6 py-4 text-left">Changes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($logs as $log)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        {{-- Timestamp --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-xs">
                                            <div>{{ $log->created_at->format('M d, Y') }}</div>
                                            <div class="text-gray-400">{{ $log->created_at->format('H:i:s') }}</div>
                                        </td>

                                        {{-- User --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($log->user)
                                                <div class="font-medium text-gray-900">{{ $log->user->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $log->user->email }}</div>
                                            @else
                                                <span class="text-gray-400 italic">System</span>
                                            @endif
                                        </td>

                                        {{-- Action Badge --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $colours = [
                                                    'LOGIN' => 'bg-green-100 text-green-800',
                                                    'LOGOUT' => 'bg-gray-100 text-gray-700',
                                                    'FILE_CREATED' => 'bg-blue-100 text-blue-800',
                                                    'FILE_UPDATED' => 'bg-yellow-100 text-yellow-800',
                                                    'FILE_DELETED' => 'bg-red-100 text-red-800',
                                                    'STATUS_CHANGED' => 'bg-purple-100 text-purple-800',
                                                ];
                                                $cls = $colours[$log->action] ?? 'bg-indigo-100 text-indigo-800';
                                            @endphp
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-bold {{ $cls }}">
                                                {{ $log->action }}
                                            </span>
                                        </td>

                                        {{-- Subject --}}
                                        <td class="px-6 py-4 text-gray-600">
                                            @if($log->auditable_type)
                                                <span class="font-medium">{{ class_basename($log->auditable_type) }}</span>
                                                <span class="text-gray-400">#{{ $log->auditable_id }}</span>
                                            @else
                                                <span class="text-gray-400 italic">—</span>
                                            @endif
                                        </td>

                                        {{-- Changes Diff --}}
                                        <td class="px-6 py-4">
                                            @if($log->old_values || $log->new_values)
                                                <details class="group">
                                                    <summary
                                                        class="cursor-pointer text-xs text-indigo-600 hover:text-indigo-800 font-medium select-none">
                                                        View diff
                                                        <span class="group-open:hidden">▸</span>
                                                        <span class="hidden group-open:inline">▾</span>
                                                    </summary>
                                                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                                                        @if($log->old_values)
                                                            <div class="bg-red-50 rounded-lg p-3">
                                                                <div class="font-bold text-red-700 mb-1 uppercase tracking-wider">Before
                                                                </div>
                                                                @foreach($log->old_values as $key => $value)
                                                                    <div class="flex gap-1">
                                                                        <span class="text-red-500 font-medium">{{ $key }}:</span>
                                                                        <span
                                                                            class="text-red-800 break-all">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                        @if($log->new_values)
                                                            <div class="bg-green-50 rounded-lg p-3">
                                                                <div class="font-bold text-green-700 mb-1 uppercase tracking-wider">
                                                                    After</div>
                                                                @foreach($log->new_values as $key => $value)
                                                                    <div class="flex gap-1">
                                                                        <span class="text-green-600 font-medium">{{ $key }}:</span>
                                                                        <span
                                                                            class="text-green-900 break-all">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </details>
                                            @else
                                                <span class="text-gray-400 text-xs italic">No change data</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($logs->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100">
                            {{ $logs->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>