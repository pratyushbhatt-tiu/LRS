<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center text-sm">
            <div class="flex items-center gap-4">
                <a href="{{ route('files.index') }}"
                    class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                        File #{{ $file->file_no }}
                    </h2>
                    <div class="flex items-center gap-2 mt-1">
                        <x-status-badge :status="$file->current_status" />
                        <span class="text-gray-400">|</span>
                        <span class="text-gray-500 font-medium">Ref: {{ $file->partner_ref_no ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3" x-data="{}">
                @can('files.edit', $file)
                    <a href="{{ route('files.edit', $file) }}" class="w-36 p-3 text-center bg-gray-800 text-white 
                                   rounded-xl hover:bg-gray-700 
                                   transition duration-200 shadow-md">
                        Edit Details
                    </a>
                @endcan

                <!-- Specialized Shipping Button -->
                @can('files.ship')
                    @if(in_array($file->current_status, [config('constants.file_statuses.ACCOUNTING_APPROVED'), config('constants.file_statuses.SHIPPING')]))
                        <a href="{{ route('shipping.show', $file) }}" 
                           class="w-48 p-3 text-center bg-gray-900 text-white rounded-xl hover:bg-gray-700 transition duration-200 shadow-lg shadow-gray-100 font-bold">
                            Process Shipment
                        </a>
                    @endif
                @endcan

                <!-- Transition Actions -->
                @php
                    $allowedTransitions = config('constants.status_transitions')[$file->current_status] ?? [];
                    // Exclude RECORDING from generic transition if coming from SHIPPING/ACCOUNTING_APPROVED 
                    // to force use of Shipping Module
                    $excludedTransitions = [config('constants.file_statuses.RECORDING')];
                    $filteredTransitions = array_diff($allowedTransitions, $excludedTransitions);
                @endphp

                @foreach($filteredTransitions as $nextStatus)
                    <button type="button" @click="$dispatch('open-modal', 'transition-modal-{{ $nextStatus }}')"
                        class="bg-white rounded-2xl shadow-sm border border-gray-800 p-3 font-bold hover:bg-gray-50 transition-colors">
                        Move to {{ config("constants.status_config.{$nextStatus}.label") }}
                    </button>

                    <!-- Transition Modal -->
                    <x-modal name="transition-modal-{{ $nextStatus }}" focusable>
                        <form method="POST" action="{{ route('files.transition', $file) }}" class="p-6 text-sm">
                            @csrf
                            <input type="hidden" name="status" value="{{ $nextStatus }}">

                            <h2 class="text-lg font-bold text-gray-900 mb-4 uppercase tracking-tight">
                                Transition to {{ config("constants.status_config.{$nextStatus}.label") }}
                            </h2>

                            <p class="text-gray-600 mb-4">
                                Are you sure you want to move this file to the
                                <strong>{{ config("constants.status_config.{$nextStatus}.label") }}</strong> stage?
                            </p>

                            <div class="mb-6">
                                <x-input-label for="notes" value="Notes (Optional)" class="font-bold uppercase text-[10px] text-gray-400 tracking-widest" />
                                <textarea name="notes" id="notes" rows="3"
                                    class="mt-1 block w-full rounded-xl border-gray-100 bg-gray-50/50 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm"
                                    placeholder="Add any relevant notes for this transition..."></textarea>
                            </div>

                            <div class="flex justify-end gap-3">
                                <button type="button" x-on:click="$dispatch('close')"
                                    class="px-5 py-2.5 bg-gray-100 text-gray-500 rounded-xl font-bold hover:bg-gray-200 transition-colors uppercase text-[10px] tracking-widest">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-8 py-2.5 bg-gray-900 text-white rounded-xl font-bold hover:bg-black transition-all shadow-lg uppercase text-[10px] tracking-widest">
                                    Confirm Transition
                                </button>
                            </div>
                        </form>
                    </x-modal>
                @endforeach
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-sm">

                <!-- Left Column: File Details -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Basic Info Card -->
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="font-bold text-gray-900">General Information</h3>
                            <span class="text-xs text-gray-400">Created
                                {{ $file->created_at->format('d-m-Y H:i') }}</span>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-900 uppercase tracking-widest mb-1">Client</label>
                                <div class="text-base font-bold text-gray-700">{{ $file->client->name }}</div>
                            </div>
                            <br>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-900 uppercase tracking-widest mb-1">Received
                                    Date</label>
                                <div class="text-base font-bold text-gray-900">
                                    {{ $file->received_date->format('d-m-Y') }}
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-900 uppercase tracking-widest mb-1">{{ __('Number of Pages') }}</label>
                                <div class="text-base font-bold text-indigo-600">
                                    {{ $file->page_count ?? 1 }} {{ __('Pages') }}
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-900 uppercase tracking-widest mb-1">Document
                                    Type</label>
                                <div class="text-base font-medium text-gray-700">{{ $file->docType->name }}
                                    ({{ $file->docType->code }})</div>
                            </div>
                            <br>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-900 uppercase tracking-widest mb-1">Recording
                                    Purpose</label>
                                <div class="text-base font-medium text-gray-700">{{ $file->recordingPurpose->name }}
                                </div>
                            </div>
                            <br>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-900 uppercase tracking-widest mb-1">County
                                    / State</label>
                                <div class="text-base font-medium text-gray-700">{{ $file->county->name }},
                                    {{ $file->state->name }}
                                </div>
                            </div>
                            <br>
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-900 uppercase tracking-widest mb-1">Partner
                                    Ref No</label>
                                <div class="text-base font-medium text-gray-700">{{ $file->partner_ref_no ?: 'None' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fee Lines Section (Preview/Placeholders) -->
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="font-bold text-gray-900">Fee Calculations</h3>
                            @if(count($file->feeLines) > 0)
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">Total:
                                    ${{ number_format($file->total_fees, 2) }}</span>
                            @endif
                        </div>
                        <div class="p-0">
                            @if(count($file->feeLines) > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="text-[10px] uppercase tracking-widest text-gray-400 bg-gray-50/50 border-b border-gray-50">
                                                <th class="px-6 py-4 font-bold">{{ __('Description') }}</th>
                                                <th class="px-6 py-4 font-bold">{{ __('Rule Source') }}</th>
                                                <th class="px-6 py-4 font-bold text-center">{{ __('Qty') }}</th>
                                                <th class="px-6 py-4 font-bold text-right">{{ __('Unit Price') }}</th>
                                                <th class="px-6 py-4 font-bold text-right">{{ __('Total Amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            @foreach($file->feeLines as $line)
                                                @php 
                                                    $classification = 'Basic';
                                                    if (str_contains(strtolower($line->description), 'surcharge')) $classification = 'Surcharge';
                                                    if (str_contains(strtolower($line->description), 'page')) $classification = 'Per-Page';
                                                    if (str_contains(strtolower($line->description), 'base') || str_contains(strtolower($line->description), 'processing')) $classification = 'Base';
                                                    if (str_contains(strtolower($line->description), 'minimum')) $classification = 'Min Cap';
                                                    if (str_contains(strtolower($line->description), 'maximum') || str_contains(strtolower($line->description), 'cap discount')) $classification = 'Max Cap';

                                                    $badgeClass = match($classification) {
                                                        'Surcharge' => 'bg-amber-100 text-amber-700',
                                                        'Per-Page' => 'bg-blue-100 text-blue-700',
                                                        'Base' => 'bg-indigo-100 text-indigo-700',
                                                        'Min Cap' => 'bg-emerald-100 text-emerald-700',
                                                        'Max Cap' => 'bg-red-100 text-red-700',
                                                        default => 'bg-gray-100 text-gray-700'
                                                    };
                                                @endphp
                                                <tr class="group/row hover:bg-gray-50/50 transition-colors">
                                                    <td class="px-6 py-4">
                                                        <div class="font-bold text-gray-900">{{ $line->description }}</div>
                                                        <div class="mt-1">
                                                            <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest {{ $badgeClass }}">
                                                                {{ $classification }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-tight">
                                                        {{ $line->feeRule ? $line->feeRule->rule_name : __('Manual Override') }}
                                                    </td>
                                                    <td class="px-6 py-4 text-center font-mono text-xs text-gray-500">{{ number_format($line->quantity, 2) }}</td>
                                                    <td class="px-6 py-4 text-right font-mono text-xs text-gray-500">${{ number_format($line->unit_price, 2) }}</td>
                                                    <td class="px-6 py-4 text-right">
                                                        <span class="font-black text-gray-900">${{ number_format($line->total_amount, 2) }}</span>
                                                        @if($line->is_override)
                                                            <div class="text-[8px] font-black text-amber-500 uppercase tracking-widest mt-0.5">{{ __('Adjusted') }}</div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-gray-50/30">
                                                <td colspan="4" class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Grand Total Calculation') }}</td>
                                                <td class="px-6 py-4 text-right font-black text-lg text-indigo-600">${{ number_format($file->total_fees, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-sm font-bold text-gray-500 mb-1">{{ __('No Fee Calculations Found') }}</p>
                                    <p class="text-xs text-gray-400">{{ __('No matching fee rules were found for this file\'s client, document type, state, and county combination. Fees will appear here once a matching rule is configured.') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recording Information Card (if applicable) -->
                    @if($file->instrument_no || $file->book || $file->recorded_at)
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mt-8">
                            <div class="px-6 py-4 bg-pink-50/50 border-b border-pink-100 flex items-center gap-3">
                                <span class="p-2 rounded-lg bg-pink-600 text-white shadow-md shadow-pink-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </span>
                                <h3 class="font-black text-pink-900 uppercase tracking-tight text-sm">Legal Recording Data</h3>
                            </div>
                            <div class="p-8 grid grid-cols-1 md:grid-cols-4 gap-8">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Instrument Number</label>
                                    <div class="text-sm font-black text-gray-900">{{ $file->instrument_no ?: __('N/A') }}</div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Book / Page</label>
                                    <div class="text-sm font-black text-gray-900">
                                        @if($file->book)
                                            {{ $file->book }} / {{ $file->page }}
                                        @else
                                            {{ __('N/A') }}
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Recording Date</label>
                                    <div class="text-sm font-black text-gray-900">
                                        {{ $file->recorded_at ? $file->recorded_at->format('d-M-Y') : __('N/A') }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Recording Fee</label>
                                    <div class="text-sm font-black text-pink-600">
                                        ${{ number_format($file->recording_fee, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Shipping Information Card (if applicable) -->
                    @if($file->courier)
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mt-8">
                            <div class="px-6 py-4 bg-indigo-50/50 border-b border-indigo-100 flex items-center gap-3">
                                <span class="p-2 rounded-lg bg-indigo-600 text-white shadow-md shadow-indigo-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </span>
                                <h3 class="font-black text-indigo-900 uppercase tracking-tight text-sm">Shipping Information</h3>
                            </div>
                            <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Courier / Method</label>
                                    <div class="text-sm font-black text-gray-900">{{ $file->courier }}</div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tracking Number</label>
                                    <div class="text-sm font-black text-gray-900 font-mono">
                                        {{ $file->tracking_number ?: __('N/A (Hand Delivered)') }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Ship Date</label>
                                    <div class="text-sm font-black text-gray-900">
                                        {{ $file->shipped_at ? $file->shipped_at->format('d-M-Y') : __('N/A') }}
                                    </div>
                                </div>
                                @if($file->shipping_notes)
                                    <div class="md:col-span-3 pt-6 border-t border-gray-50 mt-2">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Dispatch Notes</label>
                                        <div class="p-4 bg-gray-50/50 rounded-xl border border-gray-100 italic text-xs text-gray-600 font-medium leading-relaxed">
                                            "{{ $file->shipping_notes }}"
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Return Information Card (if applicable) -->
                    @if($file->return_courier)
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mt-8">
                            <div class="px-6 py-4 bg-emerald-50/50 border-b border-emerald-100 flex items-center gap-3">
                                <span class="p-2 rounded-lg bg-emerald-600 text-white shadow-md shadow-emerald-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
                                    </svg>
                                </span>
                                <h3 class="font-black text-emerald-900 uppercase tracking-tight text-sm">{{ __('Return to Partner') }}</h3>
                            </div>
                            <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ __('Courier') }}</label>
                                    <div class="text-sm font-black text-gray-900">{{ $file->return_courier }}</div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ __('Tracking Number') }}</label>
                                    <div class="text-sm font-black text-gray-900 font-mono">
                                        {{ $file->return_tracking_no ?: __('N/A (Hand Delivered)') }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ __('Return Date') }}</label>
                                    <div class="text-sm font-black text-gray-900">
                                        {{ $file->returned_at ? $file->returned_at->format('d-M-Y') : __('N/A') }}
                                    </div>
                                </div>
                                @if($file->return_notes)
                                    <div class="md:col-span-3 pt-6 border-t border-gray-50 mt-2">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">{{ __('Closing Notes') }}</label>
                                        <div class="p-4 bg-gray-50/50 rounded-xl border border-gray-100 italic text-xs text-gray-600 font-medium leading-relaxed">
                                            "{{ $file->return_notes }}"
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column: Status Timeline -->
                <div class="space-y-8">
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100">
                            <h3 class="font-bold text-gray-900">Status Timeline</h3>
                        </div>
                        <div class="p-6">
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    @foreach($file->statusHistory->sortByDesc('created_at') as $history)
                                        <li>
                                            <div class="relative pb-8">
                                                @if(!$loop->last)
                                                    <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200"
                                                        aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span
                                                            class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white {{ config("constants.status_config.{$history->to_status}.bg_class") }}">
                                                            <div
                                                                class="h-2.5 w-2.5 rounded-full {{ config("constants.status_config.{$history->to_status}.text_class") }} bg-current">
                                                            </div>
                                                        </span>
                                                    </div>
                                                    <div class="flex-1 min-w-0 flex justify-between space-x-4">
                                                        <div>
                                                            <p class="text-sm font-bold text-gray-900">
                                                                {{ config("constants.status_config.{$history->to_status}.label") }}
                                                            </p>
                                                            @if($history->notes)
                                                                <p class="mt-1 text-xs text-gray-500 italic">
                                                                    "{{ $history->notes }}"</p>
                                                            @endif
                                                        </div>
                                                        <div class="text-right text-xs whitespace-nowrap text-gray-400">
                                                            <time
                                                                datetime="{{ $history->created_at }}">{{ $history->created_at->format('d-m-Y H:i') }}</time>
                                                            <div class="font-medium text-gray-500 mt-0.5">by
                                                                {{ $history->changedBy->name }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>