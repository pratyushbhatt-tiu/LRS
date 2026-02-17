<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Document Types') }}
            </h2>
            @can('create', App\Models\DocType::class)
                <a href="{{ route('masters.doc-types.create') }}"
                    class="px-4 py-2 bg-indigo-600 text-black rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Add New Doc Type
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <x-flash-message type="success">
                    {{ session('success') }}
                </x-flash-message>
            @endif

            <x-masters.search-filter :search="request('search', '')" :status="request('status', 'all')"
                :withTrashed="request()->boolean('with_trashed')" />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Code</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Files</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($docTypes as $docType)
                                <tr class="{{ $docType->trashed() ? 'bg-red-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $docType->code }}
                                        @if($docType->trashed())
                                            <span
                                                class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Deleted</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $docType->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($docType->active)
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                        @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $docType->files_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            @if($docType->trashed())
                                                @can('restore', $docType)
                                                    <form method="POST"
                                                        action="{{ route('masters.doc-types.restore', $docType->id) }}"
                                                        class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900"
                                                            onclick="return confirm('Restore this document type?')">Restore</button>
                                                    </form>
                                                @endcan
                                            @else
                                                @can('update', $docType)
                                                    <a href="{{ route('masters.doc-types.edit', $docType) }}"
                                                        class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                @endcan
                                                @can('delete', $docType)
                                                    <form method="POST" action="{{ route('masters.doc-types.destroy', $docType) }}"
                                                        class="inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('Delete this document type?')">Delete</button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No document types
                                        found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($docTypes->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">{{ $docTypes->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>