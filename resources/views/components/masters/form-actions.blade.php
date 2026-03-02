@props(['cancelRoute', 'deleteRoute' => null, 'restoreRoute' => null, 'isDeleted' => false])

<div class="flex items-center justify-between pt-6 border-t border-gray-200">
    <div>
        @if($isDeleted && $restoreRoute)
            <form method="POST" action="{{ $restoreRoute }}" class="inline">
                @csrf
                <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                    onclick="return confirm('Are you sure you want to restore this record?')">
                    Restore
                </button>
            </form>
        @elseif($deleteRoute)
            <form method="POST" action="{{ $deleteRoute }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    onclick="return confirm('Are you sure you want to delete this record?')">
                    Delete
                </button>
            </form>
        @endif
    </div>

    <div class="flex gap-3">
        <a href="{{ $cancelRoute }}"
            class="px-4 py-2 bg-white border border-gray-300 rounded-xl font-bold text-gray-900 hover:bg-gray-70 transition-all shadow-sm">
            Cancel
        </a>

        @if(!$isDeleted)
            <button type="submit"
                class="px-4 py-2 bg-gray-800 text-white rounded-2xl hover:bg-gray-700 transition duration-200 shadow-md">
                Save
            </button>
        @endif
    </div>
</div>