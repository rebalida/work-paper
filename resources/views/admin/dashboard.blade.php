<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                {{ __('Admin Dashboard') }}
            </h2>
            <p class="text-sm text-gray-500">Review and manage all client working papers.</p>
        </div>
    </x-slot>

    <div class="space-y-8 max-w-7xl mx-auto">

        @if (session('success'))
            <div class="flex items-center p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-100 shadow-sm" role="alert">
                <x-heroicon-o-check-circle class="flex-shrink-0 w-5 h-5 mr-3" />
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Pending Review --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pending Review</p>
                            <p class="text-3xl font-bold text-yellow-600">{{ $pendingCount }}</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full text-yellow-600">
                            <x-heroicon-o-clock class="w-7 h-7" />
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-yellow-400"></div>
            </div>

            {{-- Approved --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Approved</p>
                            <p class="text-3xl font-bold text-green-600">{{ $statusCounts['approved'] }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full text-green-600">
                            <x-heroicon-o-check-badge class="w-7 h-7" />
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-green-400"></div>
            </div>

            {{-- Rejected --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Rejected</p>
                            <p class="text-3xl font-bold text-red-600">{{ $statusCounts['rejected'] }}</p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-full text-red-600">
                            <x-heroicon-o-x-circle class="w-7 h-7" />
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-red-400"></div>
            </div>

        </div>

        {{-- Filters & Search --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-lg text-gray-900">Filters & Search</h3>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Status</label>
                            <select name="status" class="block w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 text-sm shadow-sm transition-all duration-200">
                                <option value="">All Statuses</option>
                                <option value="submitted"    {{ $currentStatus === 'submitted'    ? 'selected' : '' }}>Submitted</option>
                                <option value="resubmitted" {{ $currentStatus === 'resubmitted'  ? 'selected' : '' }}>Resubmitted</option>
                                <option value="approved"    {{ $currentStatus === 'approved'     ? 'selected' : '' }}>Approved</option>
                                <option value="rejected"    {{ $currentStatus === 'rejected'     ? 'selected' : '' }}>Rejected</option>
                                <option value="draft"       {{ $currentStatus === 'draft'        ? 'selected' : '' }}>Draft</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Financial Year</label>
                            <select name="year" class="block w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 text-sm shadow-sm transition-all duration-200">
                                <option value="">All Years</option>
                                @foreach($financialYears as $year)
                                    <option value="{{ $year }}" {{ $currentYear === $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Search Client</label>
                            <input
                                type="text"
                                name="search"
                                value="{{ $searchQuery }}"
                                placeholder="Name or email..."
                                class="block w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 text-sm shadow-sm transition-all duration-200"
                            >
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 px-4 py-2.5 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-700 transition-colors shadow-sm">
                                Apply Filters
                            </button>
                            @if($currentStatus || $currentYear || $searchQuery)
                                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 text-sm font-semibold text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    Clear
                                </a>
                            @endif
                        </div>

                    </div>
                </form>
            </div>
        </div>

        {{-- Working Papers Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="font-bold text-lg text-gray-900">Working Papers</h3>
                @if($workingPapers->total() > 0)
                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                        {{ $workingPapers->total() }} {{ Str::plural('result', $workingPapers->total()) }}
                    </span>
                @endif
            </div>

            <div class="p-6">
                @if($workingPapers->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                                <tr>
                                    <th class="pb-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Client</th>
                                    <th class="pb-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Financial Year</th>
                                    <th class="pb-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Work Modules</th>
                                    <th class="pb-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="pb-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Submitted</th>
                                    <th class="pb-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reviewed By</th>
                                    <th class="pb-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($workingPapers as $paper)
                                    <tr class="hover:bg-gray-50/60 transition-colors">
                                        <td class="py-4 pr-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm flex-shrink-0">
                                                    {{ strtoupper(substr($paper->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">{{ $paper->user->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $paper->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 pr-4 text-sm text-gray-700 font-medium whitespace-nowrap">
                                            {{ $paper->financial_year }}
                                        </td>
                                        <td class="py-4 pr-4">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($paper->selected_types ?? [] as $type)
                                                    <span class="px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-800 rounded-md">
                                                        {{ strtoupper($type) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="py-4 pr-4 whitespace-nowrap">
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-lg {{ $paper->status_color }}">
                                                {{ $paper->status_label }}
                                            </span>
                                        </td>
                                        <td class="py-4 pr-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $paper->submitted_at ? $paper->submitted_at->format('M d, Y') : '—' }}
                                        </td>
                                        <td class="py-4 pr-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $paper->reviewer ? $paper->reviewer->name : '—' }}
                                        </td>
                                        <td class="py-4">
                                            <a href="{{ route('admin.working-paper.show', $paper) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                                <x-heroicon-o-eye class="w-3.5 h-3.5" />
                                                Review
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $workingPapers->links() }}
                    </div>

                @else
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-14 h-14 bg-gray-100 rounded-full text-gray-400 mb-4">
                            <x-heroicon-o-document-text class="w-7 h-7" />
                        </div>
                        <p class="text-gray-500 font-medium">No working papers found.</p>
                        @if($currentStatus || $currentYear || $searchQuery)
                            <a href="{{ route('admin.dashboard') }}" class="mt-2 inline-block text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                Clear filters to see all papers
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>