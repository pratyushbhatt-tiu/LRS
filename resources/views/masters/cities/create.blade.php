<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create City') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('masters.cities.store') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="county_id" class="block text-sm font-medium text-gray-700 mb-1">County <span
                                    class="text-red-500">*</span></label>
                            <select name="county_id" id="county_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('county_id') border-red-500 @enderror"
                                required>
                                <option value="">Select County</option>
                                @foreach($counties as $county)
                                    <option value="{{ $county->id }}" {{ old('county_id') == $county->id ? 'selected' : '' }}>
                                        {{ $county->name }} ({{ $county->state->code ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('county_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="mb-4">
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">City Code <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('code') border-red-500 @enderror"
                                required>
                            @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">City Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                                required>
                            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="mb-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                            @error('active')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <x-masters.form-actions :cancelRoute="route('masters.cities.index')" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>