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

                <!-- Transition Actions -->
                @php
                    $allowedTransitions = config('constants.status_transitions')[$file->current_status] ?? [];
                @endphp

                @foreach($allowedTransitions as $nextStatus)
                    <button type="button" @click="$dispatch('open-modal', 'transition-modal-{{ $nextStatus }}')"
                        class="bg-white rounded-2xl shadow-sm border border-gray-800 p-3">
                        Move to {{ config("constants.status_config.{$nextStatus}.label") }}
                    </button>

                    <!-- Transition Modal (Alpine.js based via x-modal component) -->
                    <x-modal name="transition-modal-{{ $nextStatus }}" focusable>
                        <form method="POST" action="{{ route('files.transition', $file) }}" class="p-6 text-sm">
                            @csrf
                            <input type="hidden" name="status" value="{{ $nextStatus }}">

                            <h2 class="text-lg font-bold text-gray-900 mb-4">
                                Transition to {{ config("constants.status_config.{$nextStatus}.label") }}
                            </h2>

                            <p class="text-gray-600 mb-4">
                                Are you sure you want to move this file to the
                                <strong>{{ config("constants.status_config.{$nextStatus}.label") }}</strong> stage?
                            </p>

                            <div class="mb-6">
                                <x-input-label for="notes" value="Notes (Optional)" class="font-bold" />
                                <textarea name="notes" id="notes" rows="3"
                                    class="mt-1 block w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                                    placeholder="Add any relevant notes for this transition..."></textarea>
                            </div>

                            <div class="flex justify-end gap-3">
                                <button type="button" x-on:click="$dispatch('close')"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-6 py-2 bg-gray-900 text-white rounded-xl font-bold hover:bg-indigo-700 transition-all shadow-lg hover:shadow-indigo-500/50">
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
                            <br>
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
                            <br>
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
                        <div class="p-6">
                            @if(count($file->feeLines) > 0)
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="text-xs text-gray-400 uppercase font-bold">
                                            <th class="pb-4">Description</th>
                                            <th class="pb-4 text-center">Qty</th>
                                            <th class="pb-4 text-right">Unit Price</th>
                                            <th class="pb-4 text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($file->feeLines as $line)
                                            <tr>
                                                <td class="py-3 font-medium text-gray-700">{{ $line->description }}</td>
                                                <td class="py-3 text-center text-gray-600">{{ $line->quantity }}</td>
                                                <td class="py-3 text-right text-gray-600">
                                                    ${{ number_format($line->unit_price, 2) }}</td>
                                                <td class="py-3 text-right font-bold text-gray-900">
                                                    ${{ number_format($line->total_amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center py-8 text-gray-400 italic">
                                    No fee calculations recorded yet. This will be handled in Phase 7.
                                </div>
                            @endif
                        </div>
                    </div>
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