<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Client') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if($client->trashed())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <p class="font-bold">This client has been deleted.</p>
                    <p class="text-sm">You can restore it using the restore button below.</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('masters.clients.update', $client) }}">
                        @csrf
                        @method('PUT')

                        <!-- Client Code -->
                        <div class="mb-4">
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                                Client Code <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="code" 
                                id="code" 
                                value="{{ old('code', $client->code) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('code') border-red-500 @enderror"
                                required
                                autofocus
                                {{ $client->trashed() ? 'disabled' : '' }}
                            >
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Client Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Client Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="name" 
                                id="name" 
                                value="{{ old('name', $client->name) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                                required
                                {{ $client->trashed() ? 'disabled' : '' }}
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center {{ $client->trashed() ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">
                                <input 
                                    type="checkbox" 
                                    name="active" 
                                    value="1"
                                    {{ old('active', $client->active) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ $client->trashed() ? 'disabled' : '' }}
                                >
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                            @error('active')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <!-- Left Side: Delete/Restore (Visual only here, actual forms below or handled separately) -->
                            <div>
                                @if($client->trashed())
                                    <button type="button" form="restore-form"
                                        class="px-4 py-2 bg-green-600 text-black rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                                        onclick="return confirm('Are you sure you want to restore this client?')">
                                        Restore Client
                                    </button>
                                @else
                                    <button type="button" form="delete-form"
                                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                        onclick="return confirm('Are you sure you want to delete this client?')">
                                        Delete Client
                                    </button>
                                @endif
                            </div>

                            <!-- Right Side: Cancel/Save -->
                            <div class="flex gap-3">
                                <a href="{{ route('masters.clients.index') }}"
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-xl font-bold text-gray-900 hover:bg-gray-70 transition-all shadow-sm">
                                    Cancel
                                </a>
                                @if(!$client->trashed())
                                    <button type="submit"
                                        class="px-4 py-2 bg-gray-800 text-white rounded-2xl hover:bg-gray-700 transition duration-200 shadow-md">
                                        Update Client
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>

                    <!-- Separate Forms for Delete and Restore -->
                    @if($client->trashed())
                        <form id="restore-form" method="POST" action="{{ route('masters.clients.restore', $client->id) }}" class="hidden">
                            @csrf
                        </form>
                    @else
                        <form id="delete-form" method="POST" action="{{ route('masters.clients.destroy', $client) }}" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
