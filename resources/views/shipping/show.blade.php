<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center text-sm">
            <div class="flex items-center gap-4">
                <a href="{{ route('shipping.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h2 class="font-black text-2xl text-gray-900 leading-tight  tracking-tighter">
                    {{ __('Process Shipment') }} - #{{ $file->file_no }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                @php $config = $file->getStatusConfig(); @endphp
                <span class="px-4 py-1.5 rounded-full font-black text-[10px]  tracking-widest {{ $config['bg_class'] }} {{ $config['text_class'] }} border {{ $config['border_class'] }}">
                    {{ $config['label'] }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8">
                
                <!-- File Summary Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div>
                                <p class="text-[10px] font-black text-gray-400  tracking-widest mb-1">{{ __('Client') }}</p>
                                <p class="text-sm font-bold text-gray-900">{{ $file->client->name }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400  tracking-widest mb-1">{{ __('Location') }}</p>
                                <p class="text-sm font-bold text-gray-900">{{ $file->county->name }}, {{ $file->state->code }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400  tracking-widest mb-1">{{ __('Doc Type') }}</p>
                                <p class="text-sm font-bold text-gray-900">{{ $file->docType->name }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400  tracking-widest mb-1">{{ __('Received') }}</p>
                                <p class="text-sm font-bold text-gray-900">{{ $file->received_date->format('d-M-Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Form -->
                <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                        <h3 class="font-black text-lg text-gray-900  tracking-tight flex items-center">
                            <span class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center mr-3 text-sm">🚚</span>
                            {{ __('Dispatch Information') }}
                        </h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium">{{ __('Provide the carrier tracking details to finalize document shipment.') }}</p>
                    </div>

                    <div class="p-8">
                        <form action="{{ route('shipping.ship', $file) }}" method="POST">
                            @csrf
                            
                            @if($errors->any())
                                <div class="p-4 mb-8 bg-red-50 border border-red-100 rounded-2xl">
                                    <ul class="list-disc list-inside text-xs text-red-600 font-bold space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Courier Selection -->
                                <div>
                                    <label for="courier" class="block text-[11px] font-black text-gray-500  tracking-widest mb-3 ml-1">{{ __('Carrier / Method') }} <span class="text-red-500">*</span></label>
                                    <select name="courier" id="courier" required
                                        class="block w-full border-gray-100 bg-gray-50/50 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold py-3 transition-all">
                                        <option value="">{{ __('Select Courier...') }}</option>
                                        <option value="FedEx" {{ old('courier') == 'FedEx' ? 'selected' : '' }}>FedEx</option>
                                        <option value="UPS" {{ old('courier') == 'UPS' ? 'selected' : '' }}>UPS</option>
                                        <option value="USPS" {{ old('courier') == 'USPS' ? 'selected' : '' }}>USPS</option>
                                        <option value="Courier" {{ old('courier') == 'Courier' ? 'selected' : '' }}>Local Courier</option>
                                        <option value="Hand Delivered" {{ old('courier') == 'Hand Delivered' ? 'selected' : '' }}>Hand Delivered</option>
                                    </select>
                                </div>

                                <!-- Shipping Date -->
                                <div>
                                    <label for="shipped_at" class="block text-[11px] font-black text-gray-500  tracking-widest mb-3 ml-1">{{ __('Shipping Date') }} <span class="text-red-500">*</span></label>
                                    <input type="date" name="shipped_at" id="shipped_at" required
                                        value="{{ old('shipped_at', date('Y-m-d')) }}"
                                        max="{{ date('Y-m-d') }}"
                                        class="block w-full border-gray-100 bg-gray-50/50 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold py-3 transition-all">
                                </div>

                                <!-- Tracking Number -->
                                <div class="md:col-span-2">
                                    <label for="tracking_number" class="block text-[11px] font-black text-gray-500  tracking-widest mb-3 ml-1">{{ __('Tracking Number') }}</label>
                                    <div class="flex items-center bg-gray-50/50 border border-gray-100 rounded-xl focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500 transition-all shadow-sm h-[56px] overflow-hidden">
                                        <span class="pl-5 pr-3 text-gray-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                            </svg>
                                        </span>
                                        <input type="text" name="tracking_number" id="tracking_number" 
                                            value="{{ old('tracking_number', $file->tracking_number) }}"
                                            placeholder="{{ __('Enter tracking ID (Optional for hand delivery)') }}"
                                            class="block w-full border-0 bg-transparent focus:ring-0 text-sm font-black text-indigo-600 p-0 pr-4 h-full">
                                    </div>
                                    <p class="mt-2 text-[10px] text-gray-400 font-medium ml-1 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('Required if carrier is FedEx, UPS, or USPS') }}
                                    </p>
                                </div>

                                <!-- Shipping Notes -->
                                <div class="md:col-span-2">
                                    <label for="shipping_notes" class="block text-[11px] font-black text-gray-500  tracking-widest mb-3 ml-1">{{ __('Dispatch Notes') }}</label>
                                    <textarea name="shipping_notes" id="shipping_notes" rows="3" 
                                        class="block w-full border-gray-100 bg-gray-50/50 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold p-4 transition-all"
                                        placeholder="{{ __('Any special instructions or observations about the courier package...') }}">{{ old('shipping_notes') }}</textarea>
                                </div>
                            </div>

                            <div class="mt-12 flex items-center justify-between gap-4 border-t border-gray-50 pt-8">
                                <a href="{{ route('shipping.index') }}" class="px-8 py-4 bg-white border border-gray-200 rounded-xl font-black text-xs text-gray-400 uppercase tracking-widest hover:bg-gray-50 hover:text-gray-600 transition-all active:scale-95 flex items-center justify-center">
                                    {{ __('Back to Queue') }}
                                </a>
                                <button type="submit" class="px-12 py-4 bg-gray-900 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-gray-200 active:scale-95 flex items-center justify-center min-w-[200px]">
                                    {{ __('Ship File') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
