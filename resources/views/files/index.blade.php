<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Files Management') }}
            </h2>
            @can('files.create')
                <a href="{{ route('files.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border-2 border-slate-900 rounded-md font-bold text-xs text-slate-900 uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all duration-200 shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('New File') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Glassmorphism Filters Section -->
            <div class="mb-8 p-6 bg-white/80 backdrop-blur-md rounded-2xl shadow-xl border border-white/20">
                <form action="{{ route('files.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <div class="relative flex items-center">

                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="File No / Partner Ref..."
                                class="w-full pl-20 pr-4 py-3 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl transition duration-200 shadow-sm">
                        </div>
                    </div>
                    <br>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl transition duration-200">
                            <option value=""
                                class="w-full pl-20 pr-4 py-3 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl transition duration-200 shadow-sm">
                                All Statuses</option>
                            @foreach(config('constants.file_statuses') as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ config("constants.status_config.{$status}.label") }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <br>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                        <select name="client_id"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl transition duration-200">
                            <option value="">All Clients</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-center gap-4 mt-6">
                        <button type="submit" class=" px-4 py-2 bg-gray-800 text-white 
               rounded-2xl hover:bg-gray-700 
               transition duration-200 shadow-md">
                            Filter
                        </button>

                        <a href="{{ route('files.index') }}" class="text-center px-4 py-2 bg-gray-200 text-gray-700 
               rounded-xl hover:bg-gray-300 
               transition duration-200 shadow-sm">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Premium Table Section -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">File
                                    No</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Client</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Doc
                                    Type</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Location</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Received</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 text-right">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($files as $file)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-200 group">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                            {{ $file->file_no }}
                                        </div>
                                        <div class="text-xs text-gray-400">Ref: {{ $file->partner_ref_no ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 font-medium">{{ $file->client->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $file->docType->name }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $file->county->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $file->state->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $file->received_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <x-status-badge :status="$file->current_status" class="shadow-sm border-2" />
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('files.show', $file) }}"
                                            class="inline-flex p-2 text-indigo-600 hover:bg-indigo-100 rounded-lg transition-all transform hover:scale-110"
                                            title="View Details">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        @can('files.edit', $file)
                                            <a href="{{ route('files.edit', $file) }}"
                                                class="inline-flex p-2 text-orange-600 hover:bg-orange-100 rounded-lg transition-all transform hover:scale-110"
                                                title="Edit File">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 italic">
                                        No files found matching your criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($files->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $files->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>