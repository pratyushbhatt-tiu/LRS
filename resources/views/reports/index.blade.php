<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
                    {{ __('Operational Insights') }}
                </h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-[10px] font-black uppercase tracking-widest text-indigo-600">Period:</span>
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
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest shadow-lg shadow-gray-200">
                    LRS Intelligence v1.0
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Top Level Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ __('Total Files') }}</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ number_format($stats['total_files']) }}</h3>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ __('Active Inventory') }}</p>
                    <h3 class="text-3xl font-black text-indigo-600">{{ number_format($stats['open_files']) }}</h3>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ __('Service Fees (LTD)') }}</p>
                    <h3 class="text-3xl font-black text-emerald-600">${{ number_format($stats['total_service_fees'], 2) }}</h3>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm border-l-4 border-l-pink-500">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ __('County Fees Paid') }}</p>
                    <h3 class="text-3xl font-black text-gray-900">${{ number_format($stats['total_recording_fees'], 2) }}</h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
                <!-- Status Distribution -->
                <div class="lg:col-span-1 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
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

                <!-- Client Performance -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" x-data="{ expanded: false }">
                    <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ __('Client Performance Analysis') }}</h3>
                        <span class="px-2 py-0.5 bg-gray-200 text-gray-500 rounded text-[8px] font-black uppercase tracking-widest">
                            {{ __('Top 25 Active') }}
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] uppercase tracking-widest text-gray-400 bg-gray-50/50 border-b border-gray-100">
                                    <th class="px-6 py-4 font-black">{{ __('Client Name') }}</th>
                                    <th class="px-6 py-4 font-black">{{ __('Revenue contribution') }}</th>
                                    <th class="px-6 py-4 font-black text-right">{{ __('File Count') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($clientData as $index => $client)
                                    <tr class="hover:bg-gray-50 transition-colors" 
                                        x-show="expanded || {{ $index }} < 5"
                                        style="{{ $index >= 5 ? 'display: none !important;' : '' }}"
                                        :style="expanded || {{ $index }} < 5 ? 'display: table-row !important;' : 'display: none !important;'">
                                        <td class="px-6 py-4 text-sm font-bold text-gray-900 uppercase tracking-tight">{{ $client->name }}</td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs font-bold text-gray-500">
                                                ${{ number_format($client->files()->with('feeLines')->get()->sum(fn($f) => $f->feeLines->sum('total_amount')), 2) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="px-3 py-1 bg-gray-100 rounded-lg text-xs font-black text-gray-900 border border-gray-200">
                                                {{ $client->files_count }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-gray-400 font-medium">No client data available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(count($clientData) > 5)
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
                <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ __('Global File Activity') }}</h3>
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