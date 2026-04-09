<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
                    {{ __('Operational Intelligence') }}
                </h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-[10px] font-black uppercase tracking-widest text-indigo-600">Active Period:</span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $stats['selected_label'] }}</span>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <form action="{{ route('reports.index') }}" method="GET" class="flex items-center gap-2" x-data="{}" x-ref="filterForm">
                    <select name="month" onchange="this.form.submit()" 
                            class="bg-white border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-700 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm pr-10">
                        <option value="">{{ __('Lifetime (All Time)') }}</option>
                        @foreach($availableMonths as $month)
                            <option value="{{ $month }}" {{ $stats['current_month'] == $month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </form>
                
                <a href="{{ route('reports.export', ['month' => $stats['current_month']]) }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-black text-[10px] text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-lg shadow-indigo-100 gap-2">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    {{ __('Export CSV') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Daily Heartbeat (Today's Performance) -->
            <div class="mb-10 bg-gray-900 rounded-3xl p-8 flex flex-wrap items-center justify-between gap-8 shadow-2xl shadow-gray-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-white font-black text-sm uppercase tracking-tight">{{ __('Daily Heartbeat') }}</h4>
                        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">{{ __('Throughput for today') }}</p>
                    </div>
                </div>
                
                <div class="flex gap-12">
                    <div class="text-center">
                        <p class="text-gray-500 text-[9px] font-black uppercase tracking-widest mb-1">{{ __('Checked In') }}</p>
                        <span class="text-2xl font-black text-white">{{ $dailyStats['received'] }}</span>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-500 text-[9px] font-black uppercase tracking-widest mb-1">{{ __('Shipped') }}</p>
                        <span class="text-2xl font-black text-white">{{ $dailyStats['shipped'] }}</span>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-500 text-[9px] font-black uppercase tracking-widest mb-1">{{ __('Recorded') }}</p>
                        <span class="text-2xl font-black text-white">{{ $dailyStats['recorded'] }}</span>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-500 text-[9px] font-black uppercase tracking-widest mb-1">{{ __('Returned') }}</p>
                        <span class="text-2xl font-black text-white">{{ $dailyStats['returned'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Top Level Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ __('Total Inventory') }}</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ number_format($stats['total_files']) }}</h3>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ __('WIP Items') }}</p>
                    <h3 class="text-3xl font-black text-indigo-600">{{ number_format($stats['open_files']) }}</h3>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ __('Service Fees') }}</p>
                    <h3 class="text-3xl font-black text-emerald-600">${{ number_format($stats['total_service_fees'], 2) }}</h3>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm border-l-4 border-l-pink-500">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ __('Recording Fees Paid') }}</p>
                    <h3 class="text-3xl font-black text-gray-900">${{ number_format($stats['total_recording_fees'], 2) }}</h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
                <!-- Lifecycle Distribution -->
                <div class="lg:col-span-1 space-y-8">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100">
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ __('Lifecycle Distribution') }}</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @foreach(config('constants.status_config') as $status => $config)
                                @php $count = $statusCounts[$status] ?? 0; @endphp
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-xs font-bold text-gray-600">{{ $config['label'] }}</span>
                                        <span class="text-xs font-black text-gray-900">{{ $count }}</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-full {{ $config['bg_class'] }} transition-all" style="width: {{ $stats['total_files'] > 0 ? ($count / $stats['total_files'] * 100) : 0 }}%; background-color: currentColor; opacity: 0.8;"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Aging Analysis (Bottlenecks) -->
                    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-pink-50/50 border-b border-pink-100">
                            <h3 class="text-sm font-black text-pink-600 uppercase tracking-tight flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('Aging Bottlenecks') }}
                            </h3>
                            <p class="text-[9px] font-black text-pink-400 uppercase tracking-widest mt-0.5">{{ __('Inactive for > 48 Hours') }}</p>
                        </div>
                        <div class="divide-y divide-gray-50">
                            @forelse($staleFiles as $file)
                                <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="text-xs font-black text-gray-900">{{ $file->file_no }}</span>
                                        <span class="text-[10px] font-black text-pink-600 bg-pink-50 px-1.5 rounded">{{ $file->updated_at->diffForHumans(['parts' => 1]) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">{{ $file->client->name }}</span>
                                        <span class="text-[10px] font-black text-gray-600 uppercase tracking-widest">{{ str_replace('_', ' ', $file->current_status) }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('No Bottlenecks Detected') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Client Performance -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" x-data="{ expanded: false }">
                    <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ __('Client Performance Analysis') }}</h3>
                        <span class="px-2 py-0.5 bg-gray-200 text-gray-500 rounded text-[8px] font-black uppercase tracking-widest">
                            {{ __('Top 25 Volume') }}
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] uppercase tracking-widest text-gray-400 bg-gray-50/50 border-b border-gray-100">
                                    <th class="px-6 py-4 font-black">{{ __('Client Name') }}</th>
                                    <th class="px-6 py-4 font-black text-right">{{ __('File Count') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($clientData as $index => $client)
                                    <tr class="hover:bg-gray-50 transition-colors" 
                                        x-show="expanded || {{ $index }} < 7"
                                        style="{{ $index >= 7 ? 'display: none !important;' : '' }}"
                                        :style="expanded || {{ $index }} < 7 ? 'display: table-row !important;' : 'display: none !important;'">
                                        <td class="px-6 py-4 text-sm font-bold text-gray-900 uppercase tracking-tight">{{ $client->name }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="px-3 py-1 bg-gray-100 rounded-lg text-xs font-black text-gray-900 border border-gray-200">
                                                {{ $client->files_count }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-6 py-8 text-center text-gray-400 font-medium">No client data available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(count($clientData) > 7)
                        <div class="px-6 py-3 bg-gray-50/30 border-t border-gray-50 text-center">
                            <button @click="expanded = !expanded" 
                                    class="text-[10px] font-black uppercase tracking-widest text-indigo-600 hover:text-indigo-800 transition-colors flex items-center justify-center gap-2 mx-auto">
                                <span x-text="expanded ? '{{ __('Show Less') }}' : '{{ __('See More Clients') }}'"></span>
                                <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ __('Global File Activity') }}</h3>
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ __('Last 5 Actions') }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-widest text-gray-400 bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-4 font-black">{{ __('File #') }}</th>
                                <th class="px-6 py-4 font-black">{{ __('Client') }}</th>
                                <th class="px-6 py-4 font-black text-center">{{ __('Current Stage') }}</th>
                                <th class="px-6 py-4 font-black text-right">{{ __('Last Updated') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentFiles as $file)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-black text-gray-900">{{ $file->file_no }}</td>
                                    <td class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-tight">{{ $file->client->name }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <x-status-badge :status="$file->current_status" />
                                    </td>
                                    <td class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        {{ $file->updated_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 text-center text-xs text-gray-400 font-bold uppercase tracking-widest">
                    {{ __('Reports generated in real-time based on active LRS lifecycle data') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>