<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
                {{ __('Shipping Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
                <!-- Ready to Ship -->
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 rounded-lg bg-indigo-50 text-indigo-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('Ready for Shipping') }}</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ $stats['ready'] }}</h3>
                </div>

                <!-- In Shipping -->
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 rounded-lg bg-blue-50 text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('In Processing') }}</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ $stats['in_shipping'] }}</h3>
                </div>

                <!-- Shipped Today -->
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 rounded-lg bg-emerald-50 text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('Shipped Today') }}</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ $stats['shipped_today'] }}</h3>
                </div>
            </div>

            <!-- Shipping Queue -->
            <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden mb-10">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-bold text-gray-800">{{ __('Shipping Queue') }}</h3>
                        @if($pendingFiles->count() > 0)
                            <span class="flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-100 text-gray-700">
                                {{ $pendingFiles->count() }} {{ __('Pending') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-widest text-gray-400 bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-3.5 font-bold">{{ __('File Number') }}</th>
                                <th class="px-6 py-3.5 font-bold">{{ __('Client') }}</th>
                                <th class="px-6 py-3.5 font-bold">{{ __('Location') }}</th>
                                <th class="px-6 py-3.5 font-bold">{{ __('Doc Type') }}</th>
                                <th class="px-6 py-3.5 font-bold">{{ __('Status') }}</th>
                                <th class="px-6 py-3.5 font-bold text-right">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($pendingFiles as $file)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-black text-gray-900">{{ $file->file_no }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">{{ $file->partner_ref_no ?? 'No Partner Ref' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-700">{{ $file->client->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-bold text-gray-600">{{ $file->county->name }}, {{ $file->state->code }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 uppercase tracking-tighter">
                                            {{ $file->docType->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php $config = $file->getStatusConfig(); @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $config['bg_class'] }} {{ $config['text_class'] }} border {{ $config['border_class'] }}">
                                            {{ $config['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('shipping.show', $file) }}" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-xl font-black text-[10px] text-white uppercase tracking-widest hover:bg-black active:scale-95 transition-all shadow-md">
                                            {{ __('Process Shipment') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="text-4xl mb-4">📦</div>
                                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest">{{ __('No Files Awaiting Shipping') }}</h3>
                                        <p class="text-xs text-gray-400 mt-1 font-medium">{{ __('All financially approved files have been processed.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Shipping Best Practices -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-4 flex items-center">
                        <span class="mr-2">📝</span> {{ __('Shipping Protocol') }}
                    </h3>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <span class="text-indigo-500 mt-0.5">●</span>
                            <span class="text-xs text-gray-500 leading-relaxed font-medium">Always verify document count matches physical package before sealing.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-indigo-500 mt-0.5">●</span>
                            <span class="text-xs text-gray-500 leading-relaxed font-medium">Hand-delivered documents must be logged as "Hand Delivered" in courier field.</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-gray-900 p-8 rounded-2xl shadow-xl shadow-gray-200 relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-sm font-black text-white uppercase tracking-widest mb-4 flex items-center">
                            <span class="mr-2">⚡</span> {{ __('Efficiency Tip') }}
                        </h3>
                        <p class="text-xs text-gray-400 leading-relaxed font-medium mb-6">
                            Group shipments by County to reduce individual shipping costs and improve carrier pickup speed.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
