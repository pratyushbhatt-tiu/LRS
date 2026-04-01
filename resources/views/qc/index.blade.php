<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('QC Dashboard') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('qc.pending') }}" class="inline-flex items-center px-6 py-3 bg-gray-900 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-gray-800 active:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition-all duration-200 shadow-lg shadow-gray-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    {{ __('View Pending QC') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
                <!-- Pending QC Card -->
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm transition-all hover:shadow-md flex items-center">
                    <div class="p-2.5 rounded-lg bg-orange-50 text-orange-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('Pending') }}</p>
                        <div class="flex items-baseline gap-2">
                            <h3 class="text-2xl font-black text-gray-900">{{ $stats['pending'] }}</h3>
                            <a href="{{ route('qc.pending') }}" class="text-[10px] font-bold text-indigo-600 hover:underline inline-flex items-center">
                                {{ __('Action') }}
                                <svg class="w-2.5 h-2.5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Passed Today Card -->
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm transition-all hover:shadow-md flex items-center">
                    <div class="p-2.5 rounded-lg bg-green-50 text-green-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('Passed Today') }}</p>
                        <h3 class="text-2xl font-black text-gray-900">{{ $stats['passed_today'] }}</h3>
                    </div>
                </div>

                <!-- Failed Today Card -->
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm transition-all hover:shadow-md flex items-center">
                    <div class="p-2.5 rounded-lg bg-red-50 text-red-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('Failed Today') }}</p>
                        <h3 class="text-2xl font-black text-gray-900">{{ $stats['failed_today'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
                <!-- Recent Activity Table -->
                <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-800">{{ __('Recent QC Activity') }}</h3>
                    </div>
                    <div class="p-0">
                        @if($recentActivity->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="text-xs uppercase text-gray-500 bg-gray-50/50 border-b border-gray-100">
                                            <th class="px-6 py-3 font-semibold">{{ __('File No') }}</th>
                                            <th class="px-6 py-3 font-semibold">{{ __('Action') }}</th>
                                            <th class="px-6 py-3 font-semibold">{{ __('Performed By') }}</th>
                                            <th class="px-6 py-3 font-semibold">{{ __('Time') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($recentActivity as $activity)
                                            <tr class="hover:bg-gray-50/50 transition-colors">
                                                <td class="px-6 py-4 font-bold text-gray-900 hover:text-indigo-600 transition-colors">
                                                    <a href="{{ route('files.show', $activity->file) }}">{{ $activity->file->file_no }}</a>
                                                </td>
                                                <td class="px-6 py-4">
                                                    @if($activity->to_status === config('constants.file_statuses.ACCOUNTING'))
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                            {{ __('PASSED') }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                                            {{ __('FAILED') }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                                    {{ $activity->changedBy->name }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    {{ $activity->created_at->diffForHumans() }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                <p class="text-gray-500 italic">{{ __('No recent activity recorded.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Tips / Help Card -->
                <div class="bg-gray-900 rounded-2xl shadow-xl overflow-hidden text-white relative">
                    <div class="absolute top-0 right-0 p-8 opacity-10">
                        <svg class="w-32 h-32" fill="white" viewBox="0 0 24 24">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                        </svg>
                    </div>
                    <div class="p-8 relative z-10">
                        <h3 class="text-2xl font-bold mb-4">{{ __('QC Best Practices') }}</h3>
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <span class="bg-gray-800 p-1 rounded-full mr-3 mt-1 text-gray-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                                <p class="text-gray-300">Verify all document names and counts match the physical file.</p>
                            </li>
                            <li class="flex items-start">
                                <span class="bg-gray-800 p-1 rounded-full mr-3 mt-1 text-gray-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                                <p class="text-gray-300">Ensure state and county recording requirements are met.</p>
                            </li>
                            <li class="flex items-start">
                                <span class="bg-gray-800 p-1 rounded-full mr-3 mt-1 text-gray-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                                <p class="text-gray-300">Always provide clear, actionable notes when failing a file.</p>
                            </li>
                        </ul>
                    </div>
                    <div class="px-8 pb-8">
                        <a href="{{ route('qc.pending') }}" class="inline-block w-full text-center bg-white text-gray-900 font-black py-4 rounded-xl hover:bg-gray-50 transition-all shadow-xl active:scale-95 uppercase tracking-widest text-xs">
                            {{ __('Start Processing Now') }}
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>