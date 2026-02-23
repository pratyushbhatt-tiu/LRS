@props(['search' => '', 'status' => 'all', 'withTrashed' => false, 'showStatusFilter' => true, 'showTrashedToggle' => true])

<div class="bg-white p-4 rounded-lg shadow-sm mb-4">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <!-- Search Input -->
        <div class="flex-1 min-w-[200px]">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Search..."
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <!-- Status Filter -->
        @if($showStatusFilter)
            <div class="w-40">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        @endif

        <!-- Show Deleted Toggle -->
        @if($showTrashedToggle)
            <div class="flex items-center">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="with_trashed" value="1" {{ $withTrashed ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Show Deleted</span>
                </label>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex items-center gap-2">
            <button type="submit" class="px-4 py-2 bg-white-600 text-black border-2 border-black rounded-md
               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Filter
            </button>

            <a href="{{ url()->current() }}" class="px-4 py-2 bg-white-500 text-black border-2 border-black rounded-md
               focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                Clear
            </a>
        </div>

    </form>
</div>