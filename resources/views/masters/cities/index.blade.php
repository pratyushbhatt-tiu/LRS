<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Cities') }}
            </h2>
            @can('create', App\Models\City::class)
                <a href="{{ route('masters.cities.create') }}"
                    class="px-4 py-2 bg-indigo-600 text-black rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Add New City
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <x-flash-message type="success">{{ session('success') }}</x-flash-message>
            @endif

            <x-masters.search-filter :search="request('search', '')" :status="request('status', 'all')"
                :withTrashed="request()->boolean('with_trashed')" />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    County</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    State</th>
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
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($cities as $city)
                                <tr class="{{ $city->trashed() ? 'bg-red-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $city->county->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $city->county->state->code ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $city->code }}
                                        @if($city->trashed())
                                            <span
                                                class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Deleted</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $city->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @can('update', $city)
                                                                    <form method="POST" action="{{ route('masters.cities.toggle-active', $city) }}"
                                                                        class="inline">
                                                                        @csrf
                                                                        <button type="submit"
                                                                            title="{{ $city->active ? 'Click to deactivate' : 'Click to activate' }}"
                                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full cursor-pointer transition-colors
                                                                                                                        {{ $city->active
                                            ? 'bg-green-100 text-green-800 hover:bg-red-100 hover:text-red-800'
                                            : 'bg-gray-100 text-gray-800 hover:bg-green-100 hover:text-green-800' }}">
                                                                            {{ $city->active ? 'Active' : 'Inactive' }}
                                                                        </button>
                                                                    </form>
                                        @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                {{ $city->active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $city->active ? 'Active' : 'Inactive' }}
                                            </span>
                                        @endcan
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            @if($city->trashed())
                                                @can('restore', $city)
                                                    <form method="POST" action="{{ route('masters.cities.restore', $city->id) }}"
                                                        class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900"
                                                            onclick="return confirm('Are you sure you want to restore this city?')">Restore</button>
                                                    </form>
                                                @endcan
                                            @else
                                                @can('update', $city)
                                                    <a href="{{ route('masters.cities.edit', $city) }}"
                                                        class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                @endcan
                                                @can('delete', $city)
                                                    <form method="POST" action="{{ route('masters.cities.destroy', $city) }}"
                                                        class="inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('Are you sure you want to delete this city?')">Delete</button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No cities found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($cities->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">{{ $cities->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>