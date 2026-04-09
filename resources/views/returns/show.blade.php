<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('returns.index') }}" class="p-2 bg-white border border-gray-100 rounded-xl text-gray-400 hover:text-gray-900 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
                {{ __('Final Return Dispatch') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('returns.return', $file) }}" method="POST">
                @csrf
                
                <div class="bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/50 overflow-hidden">
                    <!-- Context Header -->
                    <div class="px-8 py-6 bg-gray-900 text-white flex justify-between items-center">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-1">{{ __('Closing File') }}</p>
                            <h3 class="text-xl font-black tracking-tight">{{ $file->file_no }}</h3>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-1">{{ __('Recorded At') }}</p>
                            <h3 class="text-sm font-bold">{{ $file->county->name }}, {{ $file->state->code }}</h3>
                        </div>
                    </div>

                    <div class="p-8">
                        <!-- Recording Reference Card -->
                        <div class="mb-8 p-6 bg-pink-50/50 border border-pink-100 rounded-2xl flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-pink-600 text-white rounded-xl flex items-center justify-center font-black text-xl">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-gray-900">{{ __('Official Recording Reference') }}</h4>
                                    <p class="text-xs text-pink-600 font-bold">Inst #: {{ $file->instrument_no ?? 'N/A' }} | Date: {{ $file->recorded_at?->format('d-m-Y') ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Left Column: Courier Details -->
                            <div class="space-y-6">
                                <h4 class="text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">{{ __('Return Dispatch') }}</h4>
                                
                                <div>
                                    <label for="return_courier" class="block text-[11px] font-black text-gray-500 tracking-widest mb-3 ml-1">{{ __('Return Courier') }}</label>
                                    <select name="return_courier" id="return_courier" class="block w-full bg-gray-50 border-gray-100 rounded-2xl focus:ring-gray-900 focus:border-gray-900 text-sm font-bold p-4 transition-all h-[56px]">
                                        <option value="">{{ __('Select Courier...') }}</option>
                                        <option value="FedEx">{{ __('FedEx') }}</option>
                                        <option value="UPS">{{ __('UPS') }}</option>
                                        <option value="USPS">{{ __('USPS') }}</option>
                                        <option value="DHL">{{ __('DHL') }}</option>
                                        <option value="Courier Service">{{ __('Local Courier') }}</option>
                                        <option value="Hand Delivered">{{ __('Hand Delivered') }}</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="return_tracking_no" class="block text-[11px] font-black text-gray-500 tracking-widest mb-3 ml-1">{{ __('Return Tracking #') }}</label>
                                    <input type="text" name="return_tracking_no" id="return_tracking_no" 
                                        value="{{ old('return_tracking_no') }}"
                                        placeholder="Enter tracking ID"
                                        class="block w-full bg-gray-50 border-gray-100 rounded-2xl focus:ring-gray-900 focus:border-gray-900 text-sm font-bold p-4 transition-all">
                                </div>
                            </div>

                            <!-- Right Column: Closing Info -->
                            <div class="space-y-6">
                                <h4 class="text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">{{ __('Finalization') }}</h4>

                                <div>
                                    <label for="returned_at" class="block text-[11px] font-black text-gray-500 tracking-widest mb-3 ml-1">{{ __('Return Date') }}</label>
                                    <input type="date" name="returned_at" id="returned_at" 
                                        value="{{ old('returned_at', date('Y-m-d')) }}"
                                        class="block w-full bg-gray-50 border-gray-100 rounded-2xl focus:ring-gray-900 focus:border-gray-900 text-sm font-bold p-4 transition-all">
                                </div>

                                <div>
                                    <label for="return_notes" class="block text-[11px] font-black text-gray-500 tracking-widest mb-3 ml-1">{{ __('Return Notes') }}</label>
                                    <textarea name="return_notes" id="return_notes" rows="2"
                                        placeholder="Optional shipping notes..."
                                        class="block w-full bg-gray-50 border-gray-100 rounded-2xl focus:ring-gray-900 focus:border-gray-900 text-sm font-bold p-4 transition-all"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-12 flex items-center justify-between gap-4 border-t border-gray-50 pt-8">
                            <a href="{{ route('returns.index') }}" class="px-10 py-5 bg-white border border-gray-100 rounded-2xl font-black text-xs text-gray-400 uppercase tracking-widest hover:bg-gray-50 transition-all active:scale-95">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="px-16 py-5 bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-2xl shadow-emerald-100 active:scale-95 flex items-center">
                                {{ __('Close File') }}
                                <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
