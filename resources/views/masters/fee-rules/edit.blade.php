<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Fee Rule') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if($feeRule->trashed())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <p class="font-bold">This rule has been deleted.</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('masters.fee-rules.update', $feeRule) }}">
                        @csrf
                        @method('PUT')

                        <!-- Rule Name and Priority -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="rule_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Rule Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="rule_name" id="rule_name"
                                    value="{{ old('rule_name', $feeRule->rule_name) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required {{ $feeRule->trashed() ? 'disabled' : '' }}>
                                @error('rule_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                                    Priority <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="priority" id="priority"
                                    value="{{ old('priority', $feeRule->priority) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required {{ $feeRule->trashed() ? 'disabled' : '' }}>
                                @error('priority')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <!-- Date Range -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label for="effective_from" class="block text-sm font-medium text-gray-700 mb-1">
                                    Effective From <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="effective_from" id="effective_from"
                                    value="{{ old('effective_from', $feeRule->effective_from->format('Y-m-d')) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required {{ $feeRule->trashed() ? 'disabled' : '' }}>
                                @error('effective_from')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="effective_to" class="block text-sm font-medium text-gray-700 mb-1">Effective
                                    To</label>
                                <input type="date" name="effective_to" id="effective_to"
                                    value="{{ old('effective_to', $feeRule->effective_to ? $feeRule->effective_to->format('Y-m-d') : '') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ $feeRule->trashed() ? 'disabled' : '' }}>
                                @error('effective_to')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <!-- Criteria Section -->
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Matching Criteria</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label for="client_id"
                                    class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                                <select name="client_id" id="client_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ $feeRule->trashed() ? 'disabled' : '' }}>
                                    <option value="">All Clients</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id', $feeRule->client_id) == $client->id ? 'selected' : '' }}>{{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="doc_type_id" class="block text-sm font-medium text-gray-700 mb-1">Document
                                    Type</label>
                                <select name="doc_type_id" id="doc_type_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ $feeRule->trashed() ? 'disabled' : '' }}>
                                    <option value="">All Types</option>
                                    @foreach($docTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('doc_type_id', $feeRule->doc_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="state_id" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                                <select name="state_id" id="state_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ $feeRule->trashed() ? 'disabled' : '' }}>
                                    <option value="">All States</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}" {{ old('state_id', $feeRule->state_id) == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="county_id"
                                    class="block text-sm font-medium text-gray-700 mb-1">County</label>
                                <select name="county_id" id="county_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ $feeRule->trashed() ? 'disabled' : '' }}>
                                    <option value="">All Counties</option>
                                    @foreach($counties as $county)
                                        <option value="{{ $county->id }}" {{ old('county_id', $feeRule->county_id) == $county->id ? 'selected' : '' }}>
                                            {{ $county->name }} ({{ $county->state->code ?? '?' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Fees Section -->
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Fee Calculation</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6 p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label for="base_fee" class="block text-sm font-medium text-gray-700 mb-1">Base
                                    Fee</label>
                                <input type="number" step="0.01" name="base_fee" id="base_fee"
                                    value="{{ old('base_fee', $feeRule->base_fee) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ $feeRule->trashed() ? 'disabled' : '' }}>
                            </div>
                            <div>
                                <label for="per_page_fee" class="block text-sm font-medium text-gray-700 mb-1">Per Page
                                    Fee</label>
                                <input type="number" step="0.01" name="per_page_fee" id="per_page_fee"
                                    value="{{ old('per_page_fee', $feeRule->per_page_fee) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ $feeRule->trashed() ? 'disabled' : '' }}>
                            </div>
                            <div>
                                <label for="minimum_fee" class="block text-sm font-medium text-gray-700 mb-1">Min
                                    Fee</label>
                                <input type="number" step="0.01" name="minimum_fee" id="minimum_fee"
                                    value="{{ old('minimum_fee', $feeRule->minimum_fee) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ $feeRule->trashed() ? 'disabled' : '' }}>
                            </div>
                            <div>
                                <label for="maximum_fee" class="block text-sm font-medium text-gray-700 mb-1">Max
                                    Fee</label>
                                <input type="number" step="0.01" name="maximum_fee" id="maximum_fee"
                                    value="{{ old('maximum_fee', $feeRule->maximum_fee) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ $feeRule->trashed() ? 'disabled' : '' }}>
                            </div>
                        </div>

                        <!-- Active Status -->
                        <div class="mb-6">
                            <label
                                class="flex items-center {{ $feeRule->trashed() ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">
                                <input type="checkbox" name="active" value="1" {{ old('active', $feeRule->active) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ $feeRule->trashed() ? 'disabled' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                            @error('active')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <!-- Left Side: Delete/Restore -->
                            <div>
                                @if($feeRule->trashed())
                                    <button type="button" form="restore-form"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                                        onclick="return confirm('Are you sure you want to restore this fee rule?')">
                                        Restore Rule
                                    </button>
                                @else
                                    <button type="button" form="delete-form"
                                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                        onclick="return confirm('Are you sure you want to delete this fee rule?')">
                                        Delete Rule
                                    </button>
                                @endif
                            </div>

                            <!-- Right Side: Cancel/Save -->
                            <div class="flex gap-3">
                                <a href="{{ route('masters.fee-rules.index') }}"
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                    Cancel
                                </a>
                                @if(!$feeRule->trashed())
                                    <button type="submit"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                        Update Rule
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>

                    <!-- Separate Forms for Delete and Restore -->
                    @if($feeRule->trashed())
                        <form id="restore-form" method="POST"
                            action="{{ route('masters.fee-rules.restore', $feeRule->id) }}" class="hidden">
                            @csrf
                        </form>
                    @else
                        <form id="delete-form" method="POST" action="{{ route('masters.fee-rules.destroy', $feeRule) }}"
                            class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>