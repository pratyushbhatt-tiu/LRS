<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
                {{ __('Returns Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-10">
                <!-- Awaiting Return -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-xl bg-orange-50 text-orange-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ __('Awaiting Final Return') }}</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ $stats['pending'] }}</h3>
                </div>

                <!-- Finished Today -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-xl bg-emerald-900 text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ __('Files Closed Today') }}</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ $stats['closed_today'] }}</h3>
                </div>
            </div>

            <!-- Returns Queue -->
            <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden mb-10">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-black text-gray-900 tracking-tight">{{ __('Document Return Queue') }}</h3>
                        @if($pendingFiles->count() > 0)
                            <span class="flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-orange-100 text-orange-700">
                                {{ $pendingFiles->count() }} {{ __('Pending') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-widest text-gray-400 bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-4 font-black">{{ __('File Information') }}</th>
                                <th class="px-6 py-4 font-black">{{ __('Recorded By') }}</th>
                                <th class="px-6 py-4 font-black">{{ __('Legal Info') }}</th>
                                <th class="px-6 py-4 font-black text-right">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($pendingFiles as $file)
                                <tr class="hover:bg-gray-50/25 transition-colors group">
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-black text-gray-900">{{ $file->file_no }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">{{ $file->client->name }}</div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-xs font-bold text-gray-600">{{ $file->county->name }}, {{ $file->state->code }}</div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-xs font-black text-gray-900">Inst: {{ $file->instrument_no ?? 'N/A' }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">Date: {{ $file->recorded_at?->format('d-m-Y') ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <a href="{{ route('returns.show', $file) }}" class="inline-flex items-center px-5 py-2.5 bg-gray-900 border border-transparent rounded-xl font-black text-[10px] text-white uppercase tracking-widest hover:bg-black active:scale-95 transition-all shadow-md">
                                            {{ __('Process Return') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center">
                                        <div class="text-5xl mb-4">🏠</div>
                                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">{{ __('No Returns Pending') }}</h3>
                                        <p class="text-xs text-gray-400 mt-2 font-medium">{{ __('All recorded files have been sent back to the partner.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
