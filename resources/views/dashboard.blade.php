<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <x-flash-message type="success">
                    {{ session('success') }}
                </x-flash-message>
            @endif

            @if (session('error'))
                <x-flash-message type="error">
                    {{ session('error') }}
                </x-flash-message>
            @endif

            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-2">Welcome, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-600">
                        You are logged in as
                        @foreach(Auth::user()->roles as $role)
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </p>
                </div>
            </div>

            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                @can('files.view')
                    <!-- Total Files -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Files</dt>
                                        <dd class="text-2xl font-semibold text-gray-900">{{ \App\Models\File::count() }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- In QC -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">In QC</dt>
                                        <dd class="text-2xl font-semibold text-gray-900">
                                            @php
                                                try {
                                                    echo \App\Models\File::where('current_status', 'QC')->count();
                                                } catch (\Exception $e) {
                                                    echo '0';
                                                }
                                            @endphp
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- In Accounting -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">In Accounting</dt>
                                        <dd class="text-2xl font-semibold text-gray-900">
                                            @php
                                                try {
                                                    echo \App\Models\File::where('current_status', 'ACCOUNTING')->count();
                                                } catch (\Exception $e) {
                                                    echo '0';
                                                }
                                            @endphp
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Closed Today -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Closed Today</dt>
                                        <dd class="text-2xl font-semibold text-gray-900">
                                            @php
                                                try {
                                                    echo \App\Models\File::where('current_status', 'CLOSED')->whereDate('updated_at', today())->count();
                                                } catch (\Exception $e) {
                                                    echo '0';
                                                }
                                            @endphp
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>

            <!-- Role-Based Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Quick Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            @can('files.create')
                                <a href="{{ route('files.create') }}"
                                    class="flex items-center p-3 hover:bg-indigo-100 rounded-lg transition">
                                    <svg class="h-5 w-5 text-indigo-600 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900">Create New File</span>
                                </a>
                            @endcan
                            <br>
                            @role('Operations')
                            <a href="{{ route('files.import') }}"
                                class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                                <svg class="h-5 w-5 text-blue-600 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Bulk Import CSV</span>
                            </a>
                            @endrole
                            <br>
                            @role('QC')
                            <a href="{{ route('qc.pending') }}"
                                class="flex items-center p-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition">
                                <svg class="h-5 w-5 text-yellow-600 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Pending QC Reviews</span>
                            </a>
                            @endrole

                            @can('reports.view')
                                <a href="{{ route('reports.index') }}"
                                    class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition">
                                    <svg class="h-5 w-5 text-green-600 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900">View Reports</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                            <!-- <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">   Top 5</span> -->
                        </div>
                        <br>
                        @php
                            try {
                                $recentLogs = \App\Models\AuditLog::with('user')->latest()->limit(5)->get();
                            } catch (\Exception $e) {
                                $recentLogs = collect();
                            }

                            $actionColours = [
                                'LOGIN' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'dot' => 'bg-green-500'],
                                'LOGOUT' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'dot' => 'bg-gray-400'],
                                'FILE_CREATED' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'dot' => 'bg-blue-500'],
                                'FILE_UPDATED' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'dot' => 'bg-yellow-500'],
                                'FILE_DELETED' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'dot' => 'bg-red-500'],
                                'STATUS_CHANGED' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'dot' => 'bg-purple-500'],
                            ];
                        @endphp

                        @forelse($recentLogs as $log)
                            @php
                                $colours = $actionColours[$log->action] ?? ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700', 'dot' => 'bg-indigo-500'];
                                $initials = strtoupper(substr($log->user->name ?? 'S', 0, 1) . substr(explode(' ', $log->user->name ?? 'SY')[1] ?? '', 0, 1));
                            @endphp
                            <div class="flex items-start gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-semibold text-gray-900 truncate">
                                            {{ $log->user->name ?? 'System' }}
                                        </span>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $colours['bg'] }} {{ $colours['text'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $colours['dot'] }} mr-1"></span>
                                            {{ $log->action }}
                                        </span>
                                    </div>
                                    @if($log->auditable_type)
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                                        </p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center">
                                <div class="text-gray-300 text-3xl mb-2">📋</div>
                                <p class="text-sm text-gray-400 italic">No recent activity yet</p>
                            </div>
                        @endforelse

                        @if($recentLogs->isNotEmpty())
                            @can('audit-logs.view')
                                <div class="mt-4 pt-4 border-t border-gray-50">
                                    <a href="{{ route('audit-logs.index') }}"
                                        class="flex items-center justify-center gap-2 w-full py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-xl transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M4 12h16M4 18h16" />
                                        </svg>
                                        Show All Activity
                                    </a>
                                </div>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>