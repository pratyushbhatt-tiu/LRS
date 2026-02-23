<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit County') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if($county->trashed())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <p class="font-bold">This item has been deleted.</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    {{-- Main update form --}}
                    <form method="POST" action="{{ route('masters.counties.update', $county) }}" id="edit-form">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="state_id" class="block text-sm font-medium text-gray-700 mb-1">State <span
                                    class="text-red-500">*</span></label>
                            <select name="state_id" id="state_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('state_id') border-red-500 @enderror"
                                required {{ $county->trashed() ? 'disabled' : '' }}>
                                <option value="">Select State</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}" {{ old('state_id', $county->state_id) == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                @endforeach
                            </select>
                            @error('state_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="mb-4">
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">County Code <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="code" id="code" value="{{ old('code', $county->code) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('code') border-red-500 @enderror"
                                required {{ $county->trashed() ? 'disabled' : '' }}>
                            @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">County Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $county->name) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                                required {{ $county->trashed() ? 'disabled' : '' }}>
                            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="mb-6">
                            <label
                                class="flex items-center {{ $county->trashed() ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">
                                <input type="checkbox" name="active" value="1" {{ old('active', $county->active) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ $county->trashed() ? 'disabled' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                            @error('active')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </form>

                    {{-- Delete form (separate, NOT nested inside the update form) --}}
                    @if(!$county->trashed())
                        @can('delete', $county)
                            <form method="POST" action="{{ route('masters.counties.destroy', $county) }}" id="delete-form">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endcan
                    @endif

                    {{-- Restore form --}}
                    @if($county->trashed())
                        <form method="POST" action="{{ route('masters.counties.restore', $county->id) }}" id="restore-form">
                            @csrf
                        </form>
                    @endif

                    {{-- Action buttons row --}}
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <div>
                            @if($county->trashed())
                                <button type="submit" form="restore-form"
                                    onclick="return confirm('Are you sure you want to restore this county?')"
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    Restore
                                </button>
                            @else
                                @can('delete', $county)
                                    <button type="submit" form="delete-form"
                                        onclick="return confirm('Are you sure you want to delete this county?')"
                                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                        Delete
                                    </button>
                                @endcan
                            @endif
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('masters.counties.index') }}"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Cancel
                            </a>
                            @if(!$county->trashed())
                                <button type="submit" form="edit-form"
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                    Save
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>