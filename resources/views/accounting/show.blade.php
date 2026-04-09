<x-app-layout x-data="{ 
    overrideLine: null, 
    form: {
        fee_line_id: '',
        new_total: 0,
        reason: ''
    },
    scrollToAdjustment() {
        this.$nextTick(() => {
            const section = document.getElementById('adjustment-section');
            if (section) section.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    }
}">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('accounting.pending') }}" class="mr-4 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h2 class="font-black text-2xl text-gray-900 leading-tight uppercase tracking-tighter">
                    {{ __('Accounting Audit') }}: <span class="text-purple-600">{{ $file->file_no }}</span>
                </h2>
            </div>
            
            <div class="inline-flex items-center px-4 py-2 bg-purple-50 text-purple-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-purple-100 shadow-sm">
                <svg class="w-2.5 h-2.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
                {{ __('Awaiting Settlement') }}
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Auto-calculation Notice --}}
            @if(isset($calculationSummary))
                <div class="mb-6 p-4 bg-blue-50 border border-blue-100 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-blue-700 font-medium">
                        {{ __('Fees were automatically calculated:') }} 
                        {{ $calculationSummary['rules_matched'] }} {{ __('rules matched') }}, 
                        {{ $calculationSummary['lines_created'] }} {{ __('fee lines created') }}, 
                        {{ __('Total') }}: ${{ number_format($calculationSummary['total_amount'], 2) }}
                    </p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left: Financial & File Summary -->
                <div class="lg:col-span-8 space-y-8">
                    
                    <!-- Fee Breakdown Card -->
                    <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-gray-100">
                        <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-800">{{ __('Financial Settlement Breakdown') }}</h3>
                            <div class="flex items-center gap-3">
                                {{-- Recalculate Fees Button --}}
                                <form method="POST" action="{{ route('accounting.recalculate-fees', $file) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-indigo-100 transition-all border border-indigo-100 active:scale-95" title="{{ __('Recalculate Fees') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        {{ __('Recalculate') }}
                                    </button>
                                </form>
                                <button type="button" class="text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:underline transition-colors">
                                    {{ __('Export PDF Invoice') }}
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            @if($file->feeLines->count() > 0)
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="text-[10px] uppercase tracking-widest text-gray-400 border-b border-gray-50 bg-white">
                                            <th class="px-6 py-4 font-bold">{{ __('Description') }}</th>
                                            <th class="px-6 py-4 font-bold">{{ __('Rule') }}</th>
                                            <th class="px-6 py-4 font-bold text-center">{{ __('Qty') }}</th>
                                            <th class="px-6 py-4 font-bold text-right">{{ __('Unit Price') }}</th>
                                            <th class="px-6 py-4 font-bold text-right">{{ __('Line Total') }}</th>
                                            <th class="px-6 py-4 font-bold text-center">{{ __('Audit') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 font-medium text-sm text-gray-700">
                                        @php $grandTotal = 0; @endphp
                                        @foreach($file->feeLines as $line)
                                            @php $grandTotal += $line->total_amount; @endphp
                                            @php 
                                                $classification = 'Basic';
                                                if (str_contains(strtolower($line->description), 'surcharge')) $classification = 'Surcharge';
                                                if (str_contains(strtolower($line->description), 'page')) $classification = 'Per-Page';
                                                if (str_contains(strtolower($line->description), 'base') || str_contains(strtolower($line->description), 'processing')) $classification = 'Base';
                                                if (str_contains(strtolower($line->description), 'minimum')) $classification = 'Min Cap';
                                                if (str_contains(strtolower($line->description), 'maximum') || str_contains(strtolower($line->description), 'cap')) $classification = 'Max Cap';

                                                $badgeClass = match($classification) {
                                                    'Surcharge' => 'bg-amber-100 text-amber-700',
                                                    'Per-Page' => 'bg-blue-100 text-blue-700',
                                                    'Base' => 'bg-indigo-100 text-indigo-700',
                                                    'Min Cap' => 'bg-emerald-100 text-emerald-700',
                                                    'Max Cap' => 'bg-red-100 text-red-700',
                                                    default => 'bg-gray-100 text-gray-700'
                                                };
                                            @endphp
                                            <tr class="hover:bg-gray-50/30 transition-colors group/row">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center">
                                                        <div class="flex flex-col">
                                                            <span class="font-bold text-gray-900 leading-tight">{{ $line->description }}</span>
                                                            <div class="mt-1">
                                                                <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest {{ $badgeClass }}">
                                                                    {{ $classification }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <button type="button" 
                                                            @click="overrideLine = {{ json_encode($line) }}; form.fee_line_id = '{{ $line->id }}'; form.new_total = '{{ $line->total_amount }}'; form.reason = ''; scrollToAdjustment()"
                                                            class="ml-3 p-1.5 bg-gray-50 rounded-lg text-gray-300 hover:text-indigo-600 opacity-0 group-hover/row:opacity-100 transition-all border border-transparent hover:border-indigo-100">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-tight">
                                                    {{ $line->feeRule ? $line->feeRule->rule_name : __('Manual Override') }}
                                                </td>
                                                <td class="px-6 py-4 text-center text-gray-500 font-mono text-xs">{{ number_format($line->quantity, 2) }}</td>
                                                <td class="px-6 py-4 text-right font-mono text-xs text-gray-500">${{ number_format($line->unit_price, 2) }}</td>
                                                <td class="px-6 py-4 text-right font-black text-gray-900">${{ number_format($line->total_amount, 2) }}</td>
                                                <td class="px-6 py-4 text-center">
                                                    @if($line->is_override)
                                                        <span class="inline-flex px-2 py-0.5 bg-amber-50 text-amber-700 rounded text-[9px] font-black uppercase tracking-widest border border-amber-100" title="{{ $line->override_reason }}">
                                                            {{ __('Override') }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex px-2 py-0.5 bg-gray-50 text-gray-400 rounded text-[9px] font-bold uppercase tracking-widest border border-gray-100">
                                                            {{ __('Auto') }}
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-gray-50/50">
                                            <td colspan="4" class="px-6 py-5 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest">{{ __('Total Amount Due') }}</td>
                                            <td class="px-6 py-5 text-right font-black text-2xl text-indigo-600">${{ number_format($grandTotal, 2) }}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            @else
                                {{-- Empty State: No Fee Lines --}}
                                <div class="px-6 py-16 text-center">
                                    <svg class="mx-auto h-14 w-14 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <h4 class="text-sm font-black text-gray-500 uppercase tracking-tighter mb-2">{{ __('No Fee Lines Found') }}</h4>
                                    <p class="text-xs text-gray-400 mb-4">{{ __('No matching fee rules were found for this file\'s parameters. Check that active fee rules exist for this client, document type, state, and county combination.') }}</p>
                                    <form method="POST" action="{{ route('accounting.recalculate-fees', $file) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-lg active:scale-95">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            {{ __('Run Fee Calculation') }}
                                        </button>
                                    </form>
                                </div>
                                @php $grandTotal = 0; @endphp
                            @endif
                        </div>
                    </div>

                    <!-- Manual Adjustment Section -->
                    <div x-show="overrideLine" 
                        id="adjustment-section"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-4"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="bg-white shadow-xl rounded-2xl overflow-hidden border-2 border-indigo-100">
                        <div class="px-6 py-4 bg-indigo-50/50 border-b border-indigo-100 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-black text-gray-900 uppercase tracking-tighter">{{ __('Manual Fee Adjustment') }}</h3>
                                <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mt-1">{{ __('Adjusting') }}: <span x-text="overrideLine ? overrideLine.description : ''"></span></p>
                            </div>
                            <button @click="overrideLine = null" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="p-8">
                            <form action="{{ route('accounting.fee-lines.override-static') }}" method="POST">
                                @csrf
                                <div class="mt-4 mb-4">
                                    @if($errors->any())
                                        <div class="p-3 mb-4 bg-red-50 border border-red-100 rounded-xl">
                                            <ul class="list-disc list-inside text-xs text-red-600 font-bold">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                
                                <input type="hidden" name="fee_line_id" x-model="form.fee_line_id">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div>
                                        <label for="new_total" class="block text-[11px] font-black text-gray-500 uppercase tracking-widest mb-3 ml-1">{{ __('New Amount ($)') }}</label>
                                        <div class="flex items-center bg-gray-50/50 border border-gray-100 rounded-xl focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500 transition-all shadow-sm h-[52px] @error('new_total') border-red-300 ring-1 ring-red-300 @enderror">
                                            <span class="pl-4 pr-2 text-gray-400 font-black text-lg select-none">$</span>
                                            <input type="number" step="0.01" name="new_total" id="new_total" required
                                                x-model="form.new_total"
                                                class="block w-full border-0 bg-transparent focus:ring-0 text-lg font-black text-gray-900 p-0 pr-4 h-full">
                                        </div>
                                        @error('new_total')
                                            <p class="mt-1 text-[10px] text-red-600 font-bold uppercase tracking-tight">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="reason" class="block text-[11px] font-black text-gray-500 uppercase tracking-widest mb-3 ml-1">{{ __('Required Justification') }}</label>
                                        <textarea name="reason" id="reason" rows="1" required
                                            x-model="form.reason"
                                            class="block w-full border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm shadow-sm placeholder-gray-300 bg-gray-50/50 @error('reason') border-red-300 @enderror"
                                            placeholder="{{ __('Enter the reason for this pricing change...') }}"></textarea>
                                        @error('reason')
                                            <p class="mt-1 text-[10px] text-red-600 font-bold uppercase tracking-tight">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mt-8 flex justify-end gap-4">
                                    <button type="button" @click="overrideLine = null"
                                        class="px-8 py-3  border border-black-900 rounded-xl text-black-400 hover:text-black-600 transition-all font-bold text-[11px]">
                                        {{ __('Cancel') }}
                                    </button>
                                    <button type="submit"
                                        class="px-8 py-2 bg-gray-900 text-white rounded-xl font-black text-[11px]  hover:bg-black transition-all shadow-xl shadow-gray-200 active:scale-95">
                                        {{ __('Save Pricing Changes') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Pricing Parameters & Context -->
                    <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-gray-100">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-xs font-bold text-gray-800 uppercase tracking-widest">{{ __('Pricing Parameters & Context') }}</h3>
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] font-black text-indigo-400 bg-indigo-50 px-2 py-0.5 rounded-full uppercase tracking-tighter border border-indigo-100">{{ __('Live Re-calc Active') }}</span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                                @php
                                    $isAccounting = Auth::user()->hasRole('Accounting');
                                    $isAdmin = Auth::user()->hasRole('Admin');
                                    $isPendingAccounting = ($file->current_status === config('constants.file_statuses.ACCOUNTING'));
                                    $canEditParams = $isAdmin || ($isAccounting && $isPendingAccounting);
                                @endphp

                                <!-- Re-calculation Form -->
                                <div class="md:col-span-6 bg-indigo-50/30 p-4 rounded-xl border border-indigo-50">
                                    @if($canEditParams)
                                        <form method="POST" action="{{ route('accounting.update-page-count', $file) }}" class="flex items-end gap-3">
                                            @csrf
                                            <div class="flex-grow">
                                                <label for="page_count" class="block text-[10px] uppercase font-bold text-indigo-400 tracking-widest mb-1.5 ml-1">{{ __('Adjust Page Count') }}</label>
                                                <div class="flex items-center bg-white border border-indigo-100 rounded-lg focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500 transition-all shadow-sm h-[42px]">
                                                    <input type="number" name="page_count" id="page_count" min="1" required
                                                        value="{{ $file->page_count ?? 1 }}"
                                                        class="block w-full border-0 bg-transparent focus:ring-0 text-sm font-black text-gray-900 pl-4 py-0 h-full">
                                                    <span class="pr-3 text-[9px] font-black text-gray-300 uppercase select-none tracking-tighter">{{ __('Pages') }}</span>
                                                </div>
                                            </div>
                                            <button type="submit" class="bg-gray-900 text-white p-2.5 rounded-lg hover:bg-black transition-all shadow-md active:scale-95 group" title="{{ __('Update & Recalculate') }}">
                                                <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <div>
                                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-1.5 ml-1">{{ __('Audited Parameter') }}</p>
                                            <div class="px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-lg text-sm font-black text-gray-400 flex justify-between items-center">
                                                <span>{{ $file->page_count ?? 1 }} {{ __('Pages') }}</span>
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Passive Context Metadata -->
                                <div class="md:col-span-6 grid grid-cols-2 gap-6 border-l border-gray-50 pl-8">
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-1">{{ __('Doc Type') }}</p>
                                        <p class="text-sm font-bold text-gray-900 truncate uppercase tracking-tight">{{ $file->docType->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-1">{{ __('Audit Status') }}</p>
                                        <p class="text-xs font-black text-gray-900 uppercase">
                                            @if($isPendingAccounting)
                                                <span class="text-amber-500">• {{ __('Reviewing') }}</span>
                                            @else
                                                <span class="text-emerald-500">• {{ __('Finalized') }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Matched Fee Rules Info Card -->
                    @if(isset($matchedRules) && $matchedRules->count() > 0)
                        <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-gray-100">
                            <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
                                <h3 class="text-xs font-bold text-gray-800 uppercase tracking-widest">{{ __('Matched Fee Rules') }}</h3>
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-emerald-100">
                                    {{ $matchedRules->count() }} {{ __('Rules Active') }}
                                </span>
                            </div>
                            <div class="divide-y divide-gray-50">
                                @foreach($matchedRules as $rule)
                                    <div class="px-6 py-4 hover:bg-gray-50/30 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-bold text-gray-900">{{ $rule->rule_name }}</p>
                                                <div class="flex items-center gap-3 mt-1.5">
                                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ __('Priority') }}: {{ $rule->priority }}</span>
                                                    @if($rule->client)
                                                        <span class="px-1.5 py-0.5 bg-blue-50 text-blue-600 rounded text-[8px] font-black uppercase tracking-widest">{{ $rule->client->name }}</span>
                                                    @else
                                                        <span class="px-1.5 py-0.5 bg-gray-50 text-gray-400 rounded text-[8px] font-black uppercase tracking-widest">{{ __('All Clients') }}</span>
                                                    @endif
                                                    @if($rule->state)
                                                        <span class="px-1.5 py-0.5 bg-purple-50 text-purple-600 rounded text-[8px] font-black uppercase tracking-widest">{{ $rule->state->code }}</span>
                                                    @endif
                                                    @if($rule->docType)
                                                        <span class="px-1.5 py-0.5 bg-indigo-50 text-indigo-600 rounded text-[8px] font-black uppercase tracking-widest">{{ $rule->docType->code }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="flex items-center gap-4 text-[10px] font-bold text-gray-500">
                                                    @if($rule->base_fee > 0)
                                                        <span>{{ __('Base') }}: ${{ number_format($rule->base_fee, 2) }}</span>
                                                    @endif
                                                    @if($rule->per_page_fee > 0)
                                                        <span>{{ __('Per Page') }}: ${{ number_format($rule->per_page_fee, 2) }}</span>
                                                    @endif
                                                    @if($rule->surcharge > 0)
                                                        <span>{{ __('Surcharge') }}: ${{ number_format($rule->surcharge, 2) }}</span>
                                                    @endif
                                                </div>
                                                @if($rule->effective_from || $rule->effective_to)
                                                    <p class="text-[9px] text-gray-400 mt-1">
                                                        {{ $rule->effective_from ? $rule->effective_from->format('d-m-Y') : '∞' }} 
                                                        → 
                                                        {{ $rule->effective_to ? $rule->effective_to->format('d-m-Y') : '∞' }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right: Settlement Control Panel -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- Submission Card -->
                    <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden sticky top-8">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-6 uppercase tracking-tight border-b-2 border-indigo-500 pb-2 inline-block">{{ __('Approval Panel') }}</h3>
                            
                            <div class="space-y-6">
                                <div class="bg-indigo-50/50 p-5 rounded-xl border border-indigo-100/50">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">{{ __('Grand Total') }}</span>
                                        <span class="text-[9px] font-black text-white bg-indigo-500 px-2 py-0.5 rounded uppercase">{{ __('Final') }}</span>
                                    </div>
                                    <div class="text-3xl font-black text-indigo-600 tracking-tighter">
                                        ${{ number_format($grandTotal ?? 0, 2) }}
                                    </div>
                                </div>

                                @php
                                    $targetStatus = ($file->current_status === config('constants.file_statuses.ACCOUNTING')) 
                                        ? config('constants.file_statuses.ACCOUNTING_APPROVED') 
                                        : config('constants.file_statuses.SHIPPING');
                                    
                                    $buttonLabel = ($file->current_status === config('constants.file_statuses.ACCOUNTING'))
                                        ? __('Approve Fees')
                                        : __('Finalize Billing');
                                @endphp
                                
                                <form method="POST" action="{{ route('files.transition', $file) }}" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="status" value="{{ $targetStatus }}">
                                    
                                    <div>
                                        <label for="notes" class="block text-[11px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">{{ __('Closing Notes') }}</label>
                                        <textarea id="notes" name="notes" rows="3" 
                                            class="block w-full border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm shadow-sm placeholder-gray-300" 
                                            placeholder="{{ __('Any final billing notes...') }}"></textarea>
                                    </div>

                                    <div class="space-y-3">
                                        <button type="submit" 
                                            class="group w-full flex items-center justify-center gap-3 py-4 border border-transparent rounded-xl text-white bg-gray-900 hover:bg-black transition-all font-bold text-xs uppercase tracking-widest shadow-lg shadow-gray-200 active:scale-95">
                                            <svg class="h-4 w-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $buttonLabel }}
                                        </button>

                                        <button type="button" onclick="document.getElementById('returnForm').classList.toggle('hidden')"
                                            class="w-full py-3 text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all font-bold text-[10px] uppercase tracking-widest rounded-xl">
                                            {{ __('Return to Quality Control') }}
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Hidden Return Form -->
                            <div id="returnForm" class="hidden mt-6 pt-6 border-t border-gray-100">
                                <form method="POST" action="{{ route('accounting.return', $file) }}" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label for="return_notes" class="block text-[11px] font-black text-red-400 uppercase tracking-widest mb-2 ml-1">{{ __('Return Reason') }}</label>
                                        <textarea id="return_notes" name="notes" rows="3" required
                                            class="block w-full border-red-50 rounded-xl focus:ring-red-500 focus:border-red-500 text-sm shadow-sm placeholder-red-200 bg-red-50/20 font-medium" 
                                            placeholder="{{ __('Describe the pricing issue...') }}"></textarea>
                                    </div>
                                    <button type="submit" class="w-full py-3 bg-red-500 text-white rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-red-600 transition-all">
                                        {{ __('Confirm Return') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Audit Preview -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-100">
                         <h4 class="font-bold text-gray-400 text-[10px] uppercase tracking-widest mb-4 border-b border-gray-50 pb-2">{{ __('Pre-Billing Audit Info') }}</h4>
                         <div class="space-y-3">
                             <div class="flex justify-between text-xs">
                                 <span class="text-gray-400">{{ __('File') }}:</span>
                                 <span class="font-bold text-gray-700">{{ $file->file_no }}</span>
                             </div>
                             <div class="flex justify-between text-xs">
                                 <span class="text-gray-400">{{ __('Client') }}:</span>
                                 <span class="font-bold text-gray-700">{{ $file->client->name }}</span>
                             </div>
                             <div class="flex justify-between text-xs">
                                 <span class="text-gray-400">{{ __('Pages') }}:</span>
                                 <span class="font-bold text-indigo-600">{{ $file->page_count ?? 1 }}</span>
                             </div>
                             <div class="flex justify-between text-xs">
                                 <span class="text-gray-400">{{ __('Location') }}:</span>
                                 <span class="font-bold text-gray-700">{{ $file->county->name }}, {{ $file->state->code }}</span>
                             </div>
                             <div class="flex justify-between text-xs">
                                 <span class="text-gray-400">{{ __('Calculated') }}:</span>
                                 <span class="font-bold text-gray-700">{{ now()->format('d-m-Y H:i') }}</span>
                             </div>
                             <div class="flex justify-between text-xs">
                                 <span class="text-gray-400">{{ __('Engine') }}:</span>
                                 <span class="font-bold text-indigo-600">Standard-V2</span>
                             </div>
                             <div class="flex justify-between text-xs">
                                 <span class="text-gray-400">{{ __('Rules Matched') }}:</span>
                                 <span class="font-bold text-emerald-600">{{ isset($matchedRules) ? $matchedRules->count() : 0 }}</span>
                             </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
