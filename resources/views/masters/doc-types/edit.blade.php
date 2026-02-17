<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Document Type') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if($docType->trashed())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <p class="font-bold">This item has been deleted.</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('masters.doc-types.update', $docType) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Code <span class="text-red-500">*</span></label>
                            <input type="text" name="code" id="code" value="{{ old('code', $docType->code) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('code') border-red-500 @enderror" required autofocus {{ $docType->trashed() ? 'disabled' : '' }}>
                            @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $docType->name) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror" required {{ $docType->trashed() ? 'disabled' : '' }}>
                            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="mb-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="active" value="1" {{ old('active', $docType->active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $docType->trashed() ? 'disabled' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                            @error('active')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <x-masters.form-actions 
                            :cancelRoute="route('masters.doc-types.index')"
                            :deleteRoute="$docType->trashed() ? null : route('masters.doc-types.destroy', $docType)"
                            :restoreRoute="$docType->trashed() ? route('masters.doc-types.restore', $docType->id) : null"
                            :isDeleted="$docType->trashed()"
                        />
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
