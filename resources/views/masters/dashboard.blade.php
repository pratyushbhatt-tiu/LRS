<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Masters Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Clients -->
                <a href="{{ route('masters.clients.index') }}"
                    class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Clients</h5>
                    <p class="font-normal text-gray-700">Manage client records, codes, and status.</p>
                </a>

                <!-- Doc Types -->
                <a href="{{ route('masters.doc-types.index') }}"
                    class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Document Types</h5>
                    <p class="font-normal text-gray-700">Define document types (Deed, Mortgage, etc.) and codes.</p>
                </a>

                <!-- Recording Purposes -->
                <a href="{{ route('masters.recording-purposes.index') }}"
                    class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Recording Purposes</h5>
                    <p class="font-normal text-gray-700">Manage purpose codes for recording requests.</p>
                </a>

                <!-- States -->
                <a href="{{ route('masters.states.index') }}"
                    class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">States</h5>
                    <p class="font-normal text-gray-700">Manage US states and state codes.</p>
                </a>

                <!-- Counties -->
                <a href="{{ route('masters.counties.index') }}"
                    class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Counties</h5>
                    <p class="font-normal text-gray-700">Manage counties mapped to states.</p>
                </a>

                <!-- Cities -->
                <a href="{{ route('masters.cities.index') }}"
                    class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Cities</h5>
                    <p class="font-normal text-gray-700">Manage cities mapped to counties and states.</p>
                </a>

                <!-- Fee Rules -->
                <a href="{{ route('masters.fee-rules.index') }}"
                    class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Fee Rules</h5>
                    <p class="font-normal text-gray-700">Configure complex fee calculation logic.</p>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>