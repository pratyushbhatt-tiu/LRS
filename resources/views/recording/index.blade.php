<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
                {{ __('Recording Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-10">
                <!-- Awaiting Recording -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-xl bg-pink-50 text-pink-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ __('Awaiting Legal Data') }}</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ $stats['pending'] }}</h3>
                </div>

                <!-- Recorded Today -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-xl bg-gray-900 text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ __('Recorded Today') }}</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ $stats['recorded_today'] }}</h3>
                </div>
            </div>

            <!-- Recording Queue -->
            <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden mb-10">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-black text-gray-900 tracking-tight">{{ __('Recording Queue') }}</h3>
                        @if($pendingFiles->count() > 0)
                            <span class="flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-pink-100 text-pink-700">
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
                                <th class="px-6 py-4 font-black">{{ __('Jurisdiction') }}</th>
                                <th class="px-6 py-4 font-black">{{ __('Doc Type') }}</th>
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
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 uppercase tracking-tighter">
                                            {{ $file->docType->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <a href="{{ route('recording.show', $file) }}" class="inline-flex items-center px-5 py-2.5 bg-gray-900 border border-transparent rounded-xl font-black text-[10px] text-white uppercase tracking-widest hover:bg-black active:scale-95 transition-all shadow-md">
                                            {{ __('Enter Legal Data') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center">
                                        <div class="text-5xl mb-4">📑</div>
                                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">{{ __('No Files to Record') }}</h3>
                                        <p class="text-xs text-gray-400 mt-2 font-medium">{{ __('All shipped documents are up to date.') }}</p>
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
