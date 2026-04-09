<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
                {{ __('Accounting Dashboard') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('accounting.pending') }}" class="inline-flex items-center px-6 py-3 bg-gray-900 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-black active:scale-95 transition-all shadow-lg shadow-gray-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    {{ __('View Pending Billing') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
                <!-- Pending Billing Card -->
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 rounded-lg bg-purple-50 text-purple-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        @if($stats['pending'] > 0)
                            <a href="{{ route('accounting.pending') }}" class="text-[9px] font-black text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full uppercase tracking-widest hover:bg-purple-100 transition-colors border border-purple-100">
                                {{ __('Review') }} →
                            </a>
                        @endif
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('Pending Billing') }}</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ $stats['pending'] }}</h3>
                    @if($stats['pending'] > 0)
                        <p class="text-[10px] font-bold text-purple-500 mt-1">${{ number_format($stats['pending_fees_total'], 2) }} {{ __('total fees') }}</p>
                    @endif
                </div>

                <!-- Approved Today Card -->
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 rounded-lg bg-emerald-50 text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('Approved Today') }}</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ $stats['approved_today'] }}</h3>
                </div>

                <!-- Revenue Today Card -->
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 rounded-lg bg-indigo-50 text-indigo-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('Revenue Today') }}</p>
                    <h3 class="text-3xl font-black text-gray-900">${{ number_format($stats['revenue_today'], 2) }}</h3>
                </div>



            <!-- Pending Files Quick View -->
            @if($pendingFiles->count() > 0)
                <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden mb-10">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <h3 class="text-lg font-bold text-gray-800">{{ __('Files Awaiting Billing') }}</h3>
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-purple-500"></span>
                            </span>
                        </div>
                        <a href="{{ route('accounting.pending') }}" class="text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:underline transition-colors">
                            {{ __('View All') }} →
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[10px] uppercase tracking-widest text-gray-400 bg-gray-50/50 border-b border-gray-100">
                                    <th class="px-6 py-3.5 font-bold">{{ __('File') }}</th>
                                    <th class="px-6 py-3.5 font-bold">{{ __('Client') }}</th>
                                    <th class="px-6 py-3.5 font-bold">{{ __('Doc Type') }}</th>
                                    <th class="px-6 py-3.5 font-bold">{{ __('Location') }}</th>
                                    <th class="px-6 py-3.5 font-bold text-right">{{ __('Calculated Fees') }}</th>
                                    <th class="px-6 py-3.5 font-bold text-right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($pendingFiles->take(5) as $file)
                                    <tr class="hover:bg-gray-50/50 transition-colors group text-sm">
                                        <td class="px-6 py-4">
                                            <span class="font-black text-gray-900 group-hover:text-purple-600 transition-colors">{{ $file->file_no }}</span>
                                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $file->partner_ref_no }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-xs font-bold text-gray-700">{{ $file->client->name }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded text-[9px] font-black uppercase tracking-widest border border-indigo-100">
                                                {{ $file->docType->code }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-xs font-bold text-gray-500">{{ $file->state->code }} — {{ $file->county->name }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="font-black text-gray-900">${{ number_format($file->fee_lines_sum_total_amount ?? 0, 2) }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('accounting.show', $file) }}" class="inline-flex items-center px-4 py-1.5 bg-gray-900 text-white rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-purple-700 transition-all active:scale-95 shadow-sm">
                                                {{ __('Audit') }}
                                                <svg class="w-3 h-3 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($pendingFiles->count() > 5)
                        <div class="px-6 py-3 border-t border-gray-100 bg-gray-50/30 text-center">
                            <a href="{{ route('accounting.pending') }}" class="text-xs font-bold text-purple-600 hover:underline">
                                {{ __('+ :count more files pending', ['count' => $pendingFiles->count() - 5]) }}
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <div class="mt-8">
                <!-- Best Practices Card (Full Width) -->
                <div class="bg-gray-900 rounded-2xl shadow-xl overflow-hidden text-white relative flex flex-col">
                    <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
                        <svg class="w-32 h-32" fill="white" viewBox="0 0 24 24">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                        </svg>
                    </div>
                    <div class="p-8 relative z-10 flex-grow">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                            <div>
                                <h3 class="text-2xl font-black mb-6 uppercase tracking-tight">{{ __('Accounting Best Practices') }}</h3>
                                <ul class="space-y-5">
                                    <li class="flex items-start">
                                        <span class="bg-gray-800 p-1.5 rounded-full mr-4 mt-0.5 text-gray-400 border border-gray-700 shadow-inner">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </span>
                                        <div class="flex flex-col">
                                            <p class="font-black text-xs uppercase tracking-widest text-indigo-400 mb-1 leading-none">{{ __('Verify Fee Rules') }}</p>
                                            <p class="text-[11px] text-gray-400 leading-tight">Ensure system-calculated fees align with document requirements.</p>
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="bg-gray-800 p-1.5 rounded-full mr-4 mt-0.5 text-gray-400 border border-gray-700 shadow-inner">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </span>
                                        <div class="flex flex-col">
                                            <p class="font-black text-xs uppercase tracking-widest text-indigo-400 mb-1 leading-none">{{ __('Check Overrides') }}</p>
                                            <p class="text-[11px] text-gray-400 leading-tight">Verify justification for manual fee overrides.</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <ul class="space-y-5">
                                    <li class="flex items-start">
                                        <span class="bg-gray-800 p-1.5 rounded-full mr-4 mt-0.5 text-gray-400 border border-gray-700 shadow-inner">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </span>
                                        <div class="flex flex-col">
                                            <p class="font-black text-xs uppercase tracking-widest text-indigo-400 mb-1 leading-none">{{ __('Audit Compliance') }}</p>
                                            <p class="text-[11px] text-gray-400 leading-tight">Resolve discrepancies before financial finalization.</p>
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="bg-gray-800 p-1.5 rounded-full mr-4 mt-0.5 text-gray-400 border border-gray-700 shadow-inner">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </span>
                                        <div class="flex flex-col">
                                            <p class="font-black text-xs uppercase tracking-widest text-indigo-400 mb-1 leading-none">{{ __('Recalculate When Needed') }}</p>
                                            <p class="text-[11px] text-gray-400 leading-tight">Use the recalculate button if fee rules have been updated since file creation.</p>
                                        </div>
                                    </li>
                                </ul>
                                <div class="mt-8">
                                    <a href="{{ route('accounting.pending') }}" class="block w-full text-center bg-white text-gray-900 font-black py-4 rounded-xl hover:bg-gray-50 transition-all shadow-xl active:scale-95 uppercase tracking-widest text-xs">
                                        {{ __('Start Billing Run') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>