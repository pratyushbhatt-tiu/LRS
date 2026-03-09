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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl mb-6 border border-gray-100">
                <div class="p-8 bg-gradient-to-r from-white to-indigo-50/30">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-indigo-200">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 tracking-tight">Welcome back,
                                {{ explode(' ', Auth::user()->name)[0] }}!
                            </h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-sm font-medium text-gray-400">Authorized Session:</span>
                                @foreach(Auth::user()->roles as $role)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-indigo-100 text-indigo-700">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QUICK STATS CARDS: Aggregated metrics based on file statuses -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                @can('files.view')
                    <!-- Total recorded files in the system -->
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
                        <div class="flex flex-col gap-2">
                            @can('files.create')
                                <a href="{{ route('files.create') }}"
                                    class="group flex items-center p-3 hover:bg-indigo-50 rounded-xl transition-all duration-200 border border-transparent hover:border-indigo-100">
                                    <div
                                        class="w-10 h-10 flex items-center justify-center bg-indigo-100 rounded-lg text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all mr-4">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-sm font-bold text-gray-900 block">Create New File</span>
                                        <span class="text-[10px] text-gray-500 tracking-wider font-medium">Start a
                                            new recording entry</span>
                                    </div>
                                    <svg class="w-4 h-4 ml-auto text-gray-300 group-hover:text-indigo-600 transition-all opacity-0 group-hover:opacity-100"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @endcan

                            @role('Operations')
                            <a href="{{ route('files.import') }}"
                                class="group flex items-center p-3 hover:bg-blue-50 rounded-xl transition-all duration-200 border border-transparent hover:border-blue-100">
                                <div
                                    class="w-10 h-10 flex items-center justify-center bg-blue-100 rounded-lg text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all mr-4">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-bold text-gray-900 block">Bulk Import CSV</span>
                                    <span class="text-[10px] text-gray-500 tracking-wider font-medium">Upload
                                        multiple files at once</span>
                                </div>
                                <svg class="w-4 h-4 ml-auto text-gray-300 group-hover:text-blue-600 transition-all opacity-0 group-hover:opacity-100"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            @endrole

                            @role('QC')
                            <a href="{{ route('qc.pending') }}"
                                class="group flex items-center p-3 hover:bg-yellow-50 rounded-xl transition-all duration-200 border border-transparent hover:border-yellow-100">
                                <div
                                    class="w-10 h-10 flex items-center justify-center bg-yellow-100 rounded-lg text-yellow-600 group-hover:bg-yellow-600 group-hover:text-white transition-all mr-4">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-bold text-gray-900 block">Pending QC Reviews</span>
                                    <span class="text-[10px] text-gray-500 tracking-wider font-medium">Verify
                                        file quality and accuracy</span>
                                </div>
                                <svg class="w-4 h-4 ml-auto text-gray-300 group-hover:text-yellow-600 transition-all opacity-0 group-hover:opacity-100"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            @endrole

                            @can('reports.view')
                                <a href="{{ route('reports.index') }}"
                                    class="group flex items-center p-3 hover:bg-green-50 rounded-xl transition-all duration-200 border border-transparent hover:border-green-100">
                                    <div
                                        class="w-10 h-10 flex items-center justify-center bg-green-100 rounded-lg text-green-600 group-hover:bg-green-600 group-hover:text-white transition-all mr-4">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-sm font-bold text-gray-900 block">View Reports</span>
                                        <span class="text-[10px] text-gray-500  tracking-wider font-medium">Analyze
                                            recording performance</span>
                                    </div>
                                    <svg class="w-4 h-4 ml-auto text-gray-300 group-hover:text-green-600 transition-all opacity-0 group-hover:opacity-100"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                        </div>
                        <div class="space-y-4">
                            @php
                                try {
                                    // Fetch the latest 5 audit entries for recording the history of actions
                                    $recentLogs = \App\Models\AuditLog::with('user')->latest()->limit(2)->get();
                                } catch (\Exception $e) {
                                    $recentLogs = collect();
                                }

                                // Configuration for action badge colors for consistent visual cues
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
                                <div
                                    class="flex items-start gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
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