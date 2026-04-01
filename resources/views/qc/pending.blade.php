<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Files Pending QC') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('qc.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    {{ __('Back to Dashboard') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Search & Filter Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
                <form method="GET" action="{{ route('qc.pending') }}" class="flex flex-col lg:flex-row items-end gap-4 overflow-x-auto">
                    <div class="flex-grow w-full">
                        <label for="search" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">{{ __('Search Files') }}</label>
                        <div class="relative">
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                class="block w-full px-5 py-3 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 focus:ring-1 focus:ring-gray-300 focus:border-gray-400 placeholder-gray-400 transition-all shadow-sm" 
                                placeholder="{{ __('Search by File No, Reference No...') }}">
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-gray-900 border border-transparent rounded-xl font-bold text-[11px] text-white hover:bg-gray-400 focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition-all duration-200 active:scale-95 shadow-lg shadow-gray-200">
                            {{ __('Filter') }}
                        </button>
                        <a href="{{ route('qc.pending') }}" class="inline-flex items-center px-8 py-3 bg-white border border-gray-200 rounded-xl font-bold text-[11px] text-gray-500 hover:bg-gray-50 focus:ring-2 focus:ring-gray-200 focus:ring-offset-2 transition-all duration-200 active:scale-95 shadow-sm">
                            {{ __('Clear') }}
                        </a>
                    </div>
                </form>
            </div>

            <!-- Files Table Card -->
            <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs uppercase text-gray-500 bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-4 font-bold tracking-wider">{{ __('File Details') }}</th>
                                <th class="px-6 py-4 font-bold tracking-wider">{{ __('Client') }}</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-center">{{ __('Location') }}</th>
                                <th class="px-6 py-4 font-bold tracking-wider">{{ __('Received') }}</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-right">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($files as $file)
                                <tr class="hover:bg-gray-50/30 transition-all duration-200 group">
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-700 group-hover:scale-105 transition-transform duration-200 origin-left">{{ $file->file_no }}</span>
                                            <span class="text-xs text-gray-400 mt-1 font-mono uppercase tracking-tighter">{{ $file->partner_ref_no }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-gray-700">{{ $file->client->name }}</span>
                                            <span class="text-xs text-gray-400 mt-0.5">{{ $file->docType->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                         <div class="inline-flex items-center px-3 py-1 rounded-lg bg-gray-50 text-gray-700 border border-gray-100 text-xs font-bold uppercase tracking-tight group-hover:bg-indigo-50 group-hover:text-indigo-700 transition-colors">
                                             {{ $file->county->name }}, {{ $file->state->code }}
                                         </div>
                                     </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm text-gray-600 font-medium whitespace-nowrap">
                                            {{ $file->received_date->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <a href="{{ route('qc.show', $file) }}" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-lg font-bold text-xs text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg active:scale-95">
                                            {{ __('Review') }}
                                            <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-24 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="p-6 bg-gray-50 rounded-full mb-4">
                                                <svg class="h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-500 mb-1">{{ __('All caught up!') }}</h3>
                                            <p class="text-gray-400 max-w-xs">{{ __('No files are currently awaiting QC review matching your criteria.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($files->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $files->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>