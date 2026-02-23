<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit File') }}: #{{ $file->file_no }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-2xl sm:rounded-2xl border border-white/20">
                <div class="p-8">
                    <form action="{{ route('files.update', $file) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Section: Basic Information -->
                        <div class="mb-10">
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                                <span class="bg-indigo-100 text-indigo-600 p-2 rounded-lg mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </span>
                                Basic Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="file_no" :value="__('File Number')"
                                        class="font-bold text-gray-700" />
                                    <x-text-input id="file_no" name="file_no" type="text"
                                        class="mt-1 block w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                                        :value="old('file_no', $file->file_no)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('file_no')" />
                                </div>
                                <div>
                                    <x-input-label for="partner_ref_no" :value="__('Partner Reference No')"
                                        class="font-bold text-gray-700" />
                                    <x-text-input id="partner_ref_no" name="partner_ref_no" type="text"
                                        class="mt-1 block w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                                        :value="old('partner_ref_no', $file->partner_ref_no)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('partner_ref_no')" />
                                </div>
                                <div>
                                    <x-input-label for="client_id" :value="__('Client')"
                                        class="font-bold text-gray-700" />
                                    <select id="client_id" name="client_id"
                                        class="mt-1 block w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition duration-200">
                                        <option value="">Select Client</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id', $file->client_id) == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('client_id')" />
                                </div>
                                <div>
                                    <x-input-label for="received_date" :value="__('Received Date')"
                                        class="font-bold text-gray-700" />
                                    <x-text-input id="received_date" name="received_date" type="date"
                                        class="mt-1 block w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                                        :value="old('received_date', $file->received_date->format('Y-m-d'))" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('received_date')" />
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-100 mb-10">

                        <!-- Section: Document & Location -->
                        <div class="mb-10">
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                                <span class="bg-purple-100 text-purple-600 p-2 rounded-lg mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </span>
                                Document & Location
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <x-input-label for="doc_type_id" :value="__('Document Type')"
                                        class="font-bold text-gray-700" />
                                    <select id="doc_type_id" name="doc_type_id"
                                        class="mt-1 block w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition duration-200">
                                        <option value="">Select Document Type</option>
                                        @foreach($docTypes as $docType)
                                            <option value="{{ $docType->id }}" {{ old('doc_type_id', $file->doc_type_id) == $docType->id ? 'selected' : '' }}>
                                                {{ $docType->name }} ({{ $docType->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('doc_type_id')" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="recording_purpose_id" :value="__('Recording Purpose')"
                                        class="font-bold text-gray-700" />
                                    <select id="recording_purpose_id" name="recording_purpose_id"
                                        class="mt-1 block w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition duration-200">
                                        <option value="">Select Purpose</option>
                                        @foreach($purposes as $purpose)
                                            <option value="{{ $purpose->id }}" {{ old('recording_purpose_id', $file->recording_purpose_id) == $purpose->id ? 'selected' : '' }}>
                                                {{ $purpose->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('recording_purpose_id')" />
                                </div>
                                <div>
                                    <x-input-label for="state_id" :value="__('State')"
                                        class="font-bold text-gray-700" />
                                    <select id="state_id" name="state_id"
                                        class="mt-1 block w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition duration-200">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state->id }}" {{ old('state_id', $file->state_id) == $state->id ? 'selected' : '' }}>
                                                {{ $state->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('state_id')" />
                                </div>
                                <div>
                                    <x-input-label for="county_id" :value="__('County')"
                                        class="font-bold text-gray-700" />
                                    <select id="county_id" name="county_id"
                                        class="mt-1 block w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition duration-200">
                                        <option value="">Select County</option>
                                        @foreach($counties as $county)
                                            <option value="{{ $county->id }}" {{ old('county_id', $file->county_id) == $county->id ? 'selected' : '' }}>
                                                {{ $county->name }} ({{ $county->state->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('county_id')" />
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                            <a href="{{ route('files.show', $file) }}"
                                class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition-all duration-200">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-8 py-2.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition-all duration-200 shadow-lg hover:shadow-indigo-500/50 transform hover:-translate-y-0.5">
                                Update File
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>