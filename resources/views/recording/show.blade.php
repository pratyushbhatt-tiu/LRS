<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('recording.index') }}" class="p-2 bg-white border border-gray-100 rounded-xl text-gray-400 hover:text-gray-900 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
                {{ __('Legal Recording Data Entry') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('recording.record', $file) }}" method="POST">
                @csrf
                
                <div class="bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/50 overflow-hidden">
                    <!-- File Header Context -->
                    <div class="px-8 py-6 bg-gray-900 text-white flex justify-between items-center">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-1">{{ __('Processing File') }}</p>
                            <h3 class="text-xl font-black tracking-tight">{{ $file->file_no }}</h3>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-1">{{ __('Client') }}</p>
                            <h3 class="text-sm font-bold">{{ $file->client->name }}</h3>
                        </div>
                    </div>

                    <div class="p-8">
                        @if($errors->any())
                            <div class="mb-8 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-xs font-black text-red-800 uppercase tracking-widest">{{ __('Validation Errors') }}</p>
                                        <ul class="mt-1 list-disc list-inside text-xs text-red-700 font-medium">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Left Column: Primary Identifiers -->
                            <div class="space-y-6">
                                <h4 class="text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">{{ __('Legal Identifiers') }}</h4>
                                
                                <div>
                                    <label for="instrument_no" class="block text-[11px] font-black text-gray-500 tracking-widest mb-3 ml-1">{{ __('Instrument Number') }}</label>
                                    <input type="text" name="instrument_no" id="instrument_no" 
                                        value="{{ old('instrument_no') }}"
                                        placeholder="e.g. 2024-00012345"
                                        class="block w-full bg-gray-50 border-gray-100 rounded-2xl focus:ring-gray-900 focus:border-gray-900 text-sm font-bold p-4 transition-all">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="book" class="block text-[11px] font-black text-gray-500 tracking-widest mb-3 ml-1">{{ __('Book') }}</label>
                                        <input type="text" name="book" id="book" 
                                            value="{{ old('book') }}"
                                            placeholder="Book ID"
                                            class="block w-full bg-gray-50 border-gray-100 rounded-2xl focus:ring-gray-900 focus:border-gray-900 text-sm font-bold p-4 transition-all">
                                    </div>
                                    <div>
                                        <label for="page" class="block text-[11px] font-black text-gray-500 tracking-widest mb-3 ml-1">{{ __('Page') }}</label>
                                        <input type="text" name="page" id="page" 
                                            value="{{ old('page') }}"
                                            placeholder="Page No"
                                            class="block w-full bg-gray-50 border-gray-100 rounded-2xl focus:ring-gray-900 focus:border-gray-900 text-sm font-bold p-4 transition-all">
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Timeline & Fees -->
                            <div class="space-y-6">
                                <h4 class="text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">{{ __('Timeline & Costs') }}</h4>

                                <div>
                                    <label for="recorded_at" class="block text-[11px] font-black text-gray-500 tracking-widest mb-3 ml-1">{{ __('Recording Date') }}</label>
                                    <input type="date" name="recorded_at" id="recorded_at" 
                                        value="{{ old('recorded_at', date('Y-m-d')) }}"
                                        class="block w-full bg-gray-50 border-gray-100 rounded-2xl focus:ring-gray-900 focus:border-gray-900 text-sm font-bold p-4 transition-all">
                                </div>

                                <div>
                                    <label for="recording_fee" class="block text-[11px] font-black text-gray-500 tracking-widest mb-3 ml-1">{{ __('Actual Recording Fee ($)') }}</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                                        <input type="number" step="0.01" name="recording_fee" id="recording_fee" 
                                            value="{{ old('recording_fee') }}"
                                            placeholder="0.00"
                                            class="block w-full bg-gray-50 border-gray-100 rounded-2xl focus:ring-gray-900 focus:border-gray-900 text-sm font-bold p-4 pl-8 transition-all">
                                    </div>
                                    <p class="mt-2 text-[10px] text-gray-400 font-medium ml-1">{{ __('Enter the official fee charged by the county office.') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-12 flex items-center justify-between gap-4 border-t border-gray-50 pt-8">
                            <a href="{{ route('recording.index') }}" class="px-10 py-5 bg-white border border-gray-100 rounded-2xl font-black text-xs text-gray-400 uppercase tracking-widest hover:bg-gray-50 transition-all active:scale-95">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="px-16 py-5 bg-gray-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-black transition-all shadow-2xl shadow-gray-200 active:scale-95 flex items-center">
                                {{ __('Finalize Recording') }}
                                <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
