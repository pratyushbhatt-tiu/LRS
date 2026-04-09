<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('accounting.index') }}" class="mr-4 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
                    {{ __('Files Pending Billing') }}
                </h2>
            </div>
            <div class="inline-flex items-center px-4 py-2 bg-purple-50 text-purple-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-purple-100 shadow-sm">
                <span class="relative flex h-2 w-2 mr-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-purple-500"></span>
                </span>
                {{ $files->total() }} {{ __('Files Awaiting Review') }}
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Search & Filter Card -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 mb-10 mt-4">
                <form method="GET" action="{{ route('accounting.pending') }}" class="flex flex-col lg:flex-row items-end gap-6 overflow-x-auto">
                    <div class="flex-grow w-full">
                        <label for="search" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">{{ __('Search Accounting Records') }}</label>
                        <div class="relative">
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                class="block w-full px-6 py-4 bg-white border border-gray-200 rounded-2xl text-sm font-medium text-gray-700 focus:ring-1 focus:ring-purple-300 focus:border-purple-400 placeholder-gray-400 transition-all shadow-sm" 
                                placeholder="{{ __('Search by File No, Reference No, Client...') }}">
                        </div>
                    </div>

                    <div class="flex gap-3 pb-1">
                        <button type="submit" class="inline-flex items-center px-8 py-2 bg-gray-900 border border-transparent rounded-2xl font-black text-[11px] text-white hover:bg-black transition-all active:scale-95 shadow-lg shadow-gray-200">
                            {{ __('Filter') }}
                        </button>
                        <a href="{{ route('accounting.pending') }}" class="inline-flex items-center px-8 py-2 bg-white border border-gray-200 rounded-2xl font-black text-[11px] text-gray-500 hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                            {{ __('Clear') }}
                        </a>
                    </div>
                </form>
            </div>

            <!-- Content Area -->
            <div class="bg-white shadow-2xl rounded-3xl border border-gray-100 overflow-hidden min-h-[400px]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-widest text-gray-400 border-b border-gray-50 bg-gray-50/50">
                                <th class="px-8 py-5 font-bold">{{ __('File Details') }}</th>
                                <th class="px-8 py-5 font-bold">{{ __('Client') }}</th>
                                <th class="px-8 py-5 font-bold">{{ __('Location') }}</th>
                                <th class="px-8 py-5 font-bold">{{ __('Total Fees') }}</th>
                                <th class="px-8 py-5 font-bold text-right">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($files as $file)
                                <tr class="hover:bg-gray-50/70 transition-all group">
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-gray-900 group-hover:text-purple-600 transition-colors">{{ $file->file_no }}</span>
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight mt-1">{{ $file->partner_ref_no ?: 'No Ref' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-gray-700">{{ $file->client->name }}</span>
                                            <span class="text-[10px] text-gray-400 mt-0.5">{{ $file->docType->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-gray-600 uppercase tracking-tighter">{{ $file->state->code }} - {{ $file->county->name }}</span>
                                            <span class="text-[10px] text-gray-400 mt-0.5 capitalize">{{ __('Received') }}: {{ $file->received_date->format('d-m-Y') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="inline-flex px-3 py-1.5 bg-gray-50 text-gray-900 rounded-lg text-xs font-black border border-gray-100 group-hover:bg-purple-50 group-hover:text-purple-700 group-hover:border-purple-100 transition-all">
                                            ${{ number_format($file->fee_lines_sum_total_amount ?? 0, 2) }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <a href="{{ route('accounting.show', $file) }}" class="inline-flex items-center px-6 py-2.5 bg-gray-900 border border-transparent rounded-xl font-bold text-[10px] text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-1 transition-all duration-200 shadow-md hover:shadow-lg active:scale-95 group/btn">
                                            {{ __('Process Billing') }}
                                            <svg class="w-3.5 h-3.5 ml-2 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-32 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="p-6 bg-gray-50 rounded-full mb-4 text-gray-200">
                                                <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-black text-gray-900 uppercase tracking-tighter">{{ __('All Caught Up!') }}</h3>
                                            <p class="text-gray-400 text-sm mt-2 font-medium">{{ __('No files are currently awaiting accounting review matching your criteria.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($files->hasPages())
                    <div class="px-8 py-6 border-t border-gray-100 bg-gray-50/30">
                        {{ $files->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
