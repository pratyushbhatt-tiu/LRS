<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Fee Rules') }}
            </h2>
            @can('create', App\Models\FeeRule::class)
                <a href="{{ route('masters.fee-rules.create') }}"
                    class="px-4 py-2 bg-gray-800 text-white rounded-2xl hover:bg-gray-700 transition duration-200 shadow-md">
                    Add New Fee Rule
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rule Name
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Criteria
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fees
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Priority
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($feeRules as $rule)
                                <tr class="{{ $rule->trashed() ? 'bg-red-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $rule->rule_name }}
                                        <div class="text-xs text-gray-500">
                                            {{ $rule->effective_from ? $rule->effective_from->format('d-m-Y') : '—' }} to
                                            {{ $rule->effective_to ? $rule->effective_to->format('d-m-Y') : 'Indefinite' }}
                                        </div>
                                        @if($rule->trashed())
                                            <span
                                                class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Deleted
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <div>Client: {{ $rule->client->name ?? 'All' }}</div>
                                        <div>Doc: {{ $rule->docType->name ?? 'All' }}</div>
                                        <div>Loc:
                                            @if($rule->county)
                                                {{ $rule->county->name }}, {{ $rule->county->state->code }}
                                            @elseif($rule->state)
                                                {{ $rule->state->name }}
                                            @else
                                                All
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>Base: ${{ number_format($rule->base_fee, 2) }}</div>
                                        @if($rule->per_page_fee > 0)
                                            <div>Page: ${{ number_format($rule->per_page_fee, 2) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $rule->priority }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @can('update', $rule)
                                                                    <form method="POST" action="{{ route('masters.fee-rules.toggle-active', $rule) }}"
                                                                        class="inline">
                                                                        @csrf
                                                                        <button type="submit"
                                                                            title="{{ $rule->active ? 'Click to deactivate' : 'Click to activate' }}"
                                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full cursor-pointer transition-colors
                                                                                                                        {{ $rule->active
                                            ? 'bg-green-100 text-green-800 hover:bg-red-100 hover:text-red-800'
                                            : 'bg-gray-100 text-gray-800 hover:bg-green-100 hover:text-green-800' }}">
                                                                            {{ $rule->active ? 'Active' : 'Inactive' }}
                                                                        </button>
                                                                    </form>
                                        @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                {{ $rule->active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $rule->active ? 'Active' : 'Inactive' }}
                                            </span>
                                        @endcan
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            @if($rule->trashed())
                                                @can('restore', $rule)
                                                    <form method="POST" action="{{ route('masters.fee-rules.restore', $rule->id) }}"
                                                        class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900"
                                                            onclick="return confirm('Are you sure you want to restore this rule?')">
                                                            Restore
                                                        </button>
                                                    </form>
                                                @endcan
                                            @else
                                                @can('update', $rule)
                                                    <a href="{{ route('masters.fee-rules.edit', $rule) }}"
                                                        class="text-indigo-600 hover:text-indigo-900">
                                                        Edit
                                                    </a>
                                                @endcan
                                                @can('delete', $rule)
                                                    <form method="POST" action="{{ route('masters.fee-rules.destroy', $rule) }}"
                                                        class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('Are you sure you want to delete this rule?')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No fee rules found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($feeRules->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $feeRules->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>