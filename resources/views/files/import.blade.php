<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center text-sm">
            <div class="flex items-center gap-4">
                <a href="{{ route('files.index') }}"
                   class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">Bulk Import Files</h2>
                    <p class="text-gray-500 text-xs mt-0.5">Upload a CSV to create multiple files at once</p>
                </div>
            </div>
            {{-- Template download button --}}
            <a href="{{ route('files.import.template') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-2xl hover:bg-gray-50 transition shadow-sm text-sm font-medium">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download CSV Template
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 rounded-2xl px-5 py-4 text-sm">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-2xl px-5 py-4 text-sm">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Upload Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900">Upload CSV File</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Max file size: 2MB. Accepted format: .csv</p>
                </div>

                <form action="{{ route('files.import.upload') }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf

                    {{-- Drag-and-drop zone --}}
                    <div x-data="{ dragging: false, fileName: null }"
                         class="relative border-2 border-dashed rounded-2xl p-10 text-center transition-colors"
                         :class="dragging ? 'border-indigo-400 bg-indigo-50' : 'border-gray-300 bg-gray-50/50 hover:border-indigo-300 hover:bg-indigo-50/30'"
                         @dragover.prevent="dragging = true"
                         @dragleave="dragging = false"
                         @drop.prevent="dragging = false; fileName = $event.dataTransfer.files[0]?.name; $refs.csvInput.files = $event.dataTransfer.files">

                        <input type="file" name="csv_file" id="csv_file" accept=".csv,.txt"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               x-ref="csvInput"
                               @change="fileName = $event.target.files[0]?.name">

                        <div class="pointer-events-none">
                            <div class="mx-auto w-14 h-14 rounded-2xl bg-indigo-100 flex items-center justify-center mb-4">
                                <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <template x-if="fileName">
                                <p class="text-sm font-semibold text-indigo-700" x-text="'Selected: ' + fileName"></p>
                            </template>
                            <template x-if="!fileName">
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">Drag & drop your CSV here</p>
                                    <p class="text-xs text-gray-400 mt-1">or click anywhere in this box to browse</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                                class="px-6 py-2.5 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 transition-all shadow-lg hover:shadow-indigo-500/40 text-sm">
                            Upload &amp; Import
                        </button>
                    </div>
                </form>
            </div>

            {{-- Instructions Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900">CSV Format Instructions</h3>
                </div>
                <div class="p-6 text-sm text-gray-700 space-y-4">
                    <p>Your CSV file must include the following columns in the <strong>first row</strong> (header row):</p>

                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-bold">
                                <tr>
                                    <th class="px-4 py-3">Column Name</th>
                                    <th class="px-4 py-3">Required</th>
                                    <th class="px-4 py-3">Example</th>
                                    <th class="px-4 py-3">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-4 py-3 font-mono text-indigo-700">client_code</td>
                                    <td class="px-4 py-3"><span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-bold">Required</span></td>
                                    <td class="px-4 py-3 text-gray-500">CLIENT001</td>
                                    <td class="px-4 py-3 text-gray-500">Must match an active Client code in the system</td>
                                </tr>
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-4 py-3 font-mono text-indigo-700">doc_type_code</td>
                                    <td class="px-4 py-3"><span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-bold">Required</span></td>
                                    <td class="px-4 py-3 text-gray-500">DEED</td>
                                    <td class="px-4 py-3 text-gray-500">Must match an active Document Type code</td>
                                </tr>
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-4 py-3 font-mono text-indigo-700">recording_purpose_code</td>
                                    <td class="px-4 py-3"><span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-bold">Required</span></td>
                                    <td class="px-4 py-3 text-gray-500">STANDARD</td>
                                    <td class="px-4 py-3 text-gray-500">Must match an active Recording Purpose code</td>
                                </tr>
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-4 py-3 font-mono text-indigo-700">state_code</td>
                                    <td class="px-4 py-3"><span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-bold">Required</span></td>
                                    <td class="px-4 py-3 text-gray-500">CA</td>
                                    <td class="px-4 py-3 text-gray-500">Two-letter state code (active states only)</td>
                                </tr>
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-4 py-3 font-mono text-indigo-700">county_name</td>
                                    <td class="px-4 py-3"><span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-bold">Required</span></td>
                                    <td class="px-4 py-3 text-gray-500">Los Angeles</td>
                                    <td class="px-4 py-3 text-gray-500">Full county name within the given state</td>
                                </tr>
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-4 py-3 font-mono text-indigo-700">received_date</td>
                                    <td class="px-4 py-3"><span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-bold">Required</span></td>
                                    <td class="px-4 py-3 text-gray-500">2026-03-09</td>
                                    <td class="px-4 py-3 text-gray-500">Format: YYYY-MM-DD</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl p-4 text-xs text-amber-800">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Invalid rows are <strong>skipped</strong> — valid rows will still be imported. After import, you can download an <strong>error CSV</strong> with all failed rows highlighted.</span>
                    </div>
                </div>
            </div>

            {{-- Recent Imports Table --}}
            @if($recentImports->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900">Recent Imports</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-bold">
                                <tr>
                                    <th class="px-6 py-3 text-left">File</th>
                                    <th class="px-6 py-3 text-left">Status</th>
                                    <th class="px-6 py-3 text-center">Total</th>
                                    <th class="px-6 py-3 text-center">Success</th>
                                    <th class="px-6 py-3 text-center">Failed</th>
                                    <th class="px-6 py-3 text-left">Date</th>
                                    <th class="px-6 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($recentImports as $log)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-6 py-3 font-medium text-gray-800 max-w-xs truncate">{{ $log->filename }}</td>
                                        <td class="px-6 py-3">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $log->status_class }}">
                                                {{ $log->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-center text-gray-600">{{ $log->total_rows }}</td>
                                        <td class="px-6 py-3 text-center text-green-700 font-bold">{{ $log->success_rows }}</td>
                                        <td class="px-6 py-3 text-center {{ $log->failed_rows > 0 ? 'text-red-700 font-bold' : 'text-gray-400' }}">{{ $log->failed_rows }}</td>
                                        <td class="px-6 py-3 text-gray-500 text-xs">{{ $log->created_at->format('M d, Y H:i') }}</td>
                                        <td class="px-6 py-3 text-right">
                                            <a href="{{ route('files.import.show', $log) }}"
                                               class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">View →</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>