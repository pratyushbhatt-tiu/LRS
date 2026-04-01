<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('qc.pending') }}" class="mr-4 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight uppercase tracking-tight">
                    {{ __('QC Review') }}: <span class="text-indigo-600">{{ $file->file_no }}</span>
                </h2>
            </div>
            
            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-200 uppercase tracking-widest shadow-sm">
                <span class="relative flex h-2 w-2 mr-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                </span>
                {{ __('In Review') }}
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left: File Details -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <!-- Core Information Card -->
                    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-800">{{ __('File Information') }}</h3>
                            <button type="button" onclick="window.print()" class="text-gray-400 hover:text-indigo-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-12">
                                <section>
                                    <h4 class="text-xs uppercase font-bold text-gray-400 tracking-widest mb-6 border-b border-gray-50 pb-2">{{ __('Project Identifiers') }}</h4>
                                    <div class="space-y-6">
                                        <div>
                                            <p class="text-[10px] uppercase tracking-tighter text-gray-400 font-bold mb-1">{{ __('LRS File Number') }}</p>
                                            <p class="text-lg font-black text-gray-900 font-mono tracking-tight">{{ $file->file_no }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] uppercase tracking-tighter text-gray-400 font-bold mb-1">{{ __('Partner Reference') }}</p>
                                            <p class="text-base font-bold text-gray-700 font-mono">{{ $file->partner_ref_no ?: 'N/A' }}</p>
                                        </div>
                                    </div>
                                </section>

                                <section>
                                    <h4 class="text-xs uppercase font-bold text-gray-400 tracking-widest mb-6 border-b border-gray-50 pb-2">{{ __('Entity & Type') }}</h4>
                                    <div class="space-y-6">
                                        <div>
                                            <p class="text-[10px] uppercase tracking-tighter text-gray-400 font-bold mb-1">{{ __('Client Name') }}</p>
                                            <p class="text-lg font-bold text-gray-900">{{ $file->client->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] uppercase tracking-tighter text-gray-400 font-bold mb-1">{{ __('Document Type') }}</p>
                                            <p class="inline-flex px-2 py-1 bg-indigo-50 text-indigo-700 rounded text-sm font-bold">{{ $file->docType->name }}</p>
                                        </div>
                                    </div>
                                </section>

                                <section>
                                    <h4 class="text-xs uppercase font-bold text-gray-400 tracking-widest mb-6 border-b border-gray-50 pb-2">{{ __('Geographical Data') }}</h4>
                                    <div class="space-y-6">
                                        <div>
                                            <p class="text-[10px] uppercase tracking-tighter text-gray-400 font-bold mb-1">{{ __('State') }}</p>
                                            <p class="text-base font-bold text-gray-900">{{ $file->state->name }} <span class="text-gray-400 font-normal">({{ $file->state->code }})</span></p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] uppercase tracking-tighter text-gray-400 font-bold mb-1">{{ __('County') }}</p>
                                            <p class="text-base font-bold text-gray-900">{{ $file->county->name }}</p>
                                        </div>
                                    </div>
                                </section>

                                <section>
                                    <h4 class="text-xs uppercase font-bold text-gray-400 tracking-widest mb-6 border-b border-gray-50 pb-2">{{ __('Process Metadata') }}</h4>
                                    <div class="space-y-6">
                                        <div>
                                            <p class="text-[10px] uppercase tracking-tighter text-gray-400 font-bold mb-1">{{ __('Purpose of Recording') }}</p>
                                            <p class="text-base font-bold text-gray-900">{{ $file->recordingPurpose->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] uppercase tracking-tighter text-gray-400 font-bold mb-1">{{ __('Received Date') }}</p>
                                            <p class="text-base font-bold text-gray-900">{{ $file->received_date->format('l, F j, Y') }}</p>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>

                    <!-- Status History Timeline -->
                    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-bold text-gray-800">{{ __('Workflow History') }}</h3>
                        </div>
                        <div class="p-8">
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    @foreach($file->statusHistory as $history)
                                        <li>
                                            <div class="relative pb-8">
                                                @if (!$loop->last)
                                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white bg-indigo-500 text-white">
                                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                        <div>
                                                            <p class="text-sm text-gray-500">
                                                                <span class="font-bold text-indigo-700">Changed to {{ $history->to_status }}</span>
                                                                @if($history->from_status)
                                                                    <span class="text-gray-400"> (from {{ $history->from_status }})</span>
                                                                @endif
                                                                by <span class="font-semibold text-gray-800">{{ $history->changedBy->name }}</span>
                                                            </p>
                                                            @if($history->notes)
                                                                <p class="mt-1 text-sm bg-gray-50 p-2 rounded-lg border border-gray-100 italic text-gray-600">
                                                                    {{ $history->notes }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                        <div class="text-right text-xs whitespace-nowrap text-gray-400 font-medium">
                                                            <time datetime="{{ $history->created_at }}">{{ $history->created_at->format('M j, Y H:i') }}</time>
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

                <!-- Right: Action Panel -->
                <div class="space-y-8">
                    
                    <!-- The Review Decision Card -->
                    <div class="bg-white shadow-2xl rounded-2xl border-4 border-indigo-500/20 overflow-hidden sticky top-8">
                        <div class="p-8">
                            <h3 class="text-xl font-black text-gray-900 mb-6 uppercase tracking-tighter">{{ __('Final QC Decision') }}</h3>
                            
                            <form id="qcDecisionForm" method="POST" action="" data-pass-action="{{ route('qc.pass', $file) }}" data-fail-action="{{ route('qc.fail', $file) }}" class="space-y-6">
                                @csrf
                                
                                <div>
                                    <label for="notes" class="block text-sm font-bold text-gray-700 mb-2">{{ __('Verification Notes') }}</label>
                                    <textarea id="notes" name="notes" rows="4" 
                                        class="block w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm shadow-sm" 
                                        placeholder="{{ __('Record your findings here. Mandatory for Failures.') }}"></textarea>
                                    @error('notes')
                                        <p class="mt-1 text-xs text-red-600 font-bold uppercase">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 gap-4 pt-2">
                                    <!-- Pass Button -->
                                    <button type="submit" onclick="setDecision('pass', event)" 
                                        class="group relative w-full flex items-center justify-center gap-3 py-4 px-6 border border-transparent rounded-2xl text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-300 font-bold text-lg shadow-lg hover:shadow-xl hover:-translate-y-1 active:scale-[0.98]">
                                        <svg class="h-6 w-6 text-green-200 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ __('Pass QC Checklist') }}
                                    </button>

                                    <!-- Fail Button -->
                                    <button type="submit" onclick="setDecision('fail', event)" 
                                        class="group relative w-full flex items-center justify-center gap-3 py-4 px-6 border-2 border-red-100 rounded-2xl text-red-600 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-300 font-bold text-lg shadow-sm hover:shadow-md active:scale-[0.98]">
                                        <svg class="h-6 w-6 text-red-400 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        {{ __('Fail & Return') }}
                                    </button>

                                    <p class="text-center text-xs text-gray-400 font-medium px-4">
                                        {{ __('By clicking Pass, you certify that all document requirements and metadata are accurate and complete.') }}
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Helpful Links -->
                    <div class="bg-gray-100 rounded-2xl p-6 border border-gray-200">
                        <h4 class="font-bold text-gray-800 text-sm mb-4">{{ __('QC Resources') }}</h4>
                        <div class="space-y-3">
                            <a href="#" class="flex items-center text-xs text-indigo-600 font-bold hover:underline transition-all underline-offset-4">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                {{ __('State Recording Guidelines') }}
                            </a>
                            <a href="#" class="flex items-center text-xs text-indigo-600 font-bold hover:underline transition-all underline-offset-4">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('Fee Calculation Sheet') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function setDecision(type, event) {
            const form = document.getElementById('qcDecisionForm');
            const passAction = form.dataset.passAction;
            const failAction = form.dataset.failAction;
            const notes = document.getElementById('notes').value;

            if (type === 'pass') {
                form.action = passAction;
            } else {
                if (!notes.trim() || notes.trim().length < 5) {
                    alert('Please provide detailed notes (min 5 characters) for the failure reason.');
                    event.preventDefault();
                    return;
                }
                form.action = failAction;
            }
        }
    </script>
</x-app-layout>
