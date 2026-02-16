<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tax Data Capture') }}
        </h2>
    </x-slot>

    <!-- File Viewer Modal -->
    <div x-data="fileViewerData()" @open-file-viewer.window="openFile($event.detail.url, $event.detail.name)">
        @include('components.file-viewer-modal')
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Rejection Alert -->
            @if($workingPaper->status === 'rejected' && $workingPaper->admin_comment)
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-6 rounded-r-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-lg font-semibold text-red-800">
                                Your working paper was returned for revision
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p class="font-medium">Feedback from {{ $workingPaper->reviewer->name ?? 'Admin' }}:</p>
                                <p class="mt-1 bg-white p-3 rounded border border-red-200">{{ $workingPaper->admin_comment }}</p>
                            </div>
                            <p class="mt-3 text-sm text-red-600">
                                Please review the feedback above, make necessary corrections, and resubmit your working paper.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Pending Review Alert -->
            @if(in_array($workingPaper->status, ['submitted', 'resubmitted']))
                <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-semibold text-blue-800">
                                {{ $workingPaper->status === 'resubmitted' ? 'Resubmitted - Under Review' : 'Submitted - Under Review' }}
                            </h3>
                            <p class="mt-1 text-sm text-blue-700">
                                Your working paper is currently being reviewed by our team. You will be notified once the review is complete.
                            </p>
                            <p class="mt-2 text-xs text-blue-600">
                                Submitted: {{ $workingPaper->submitted_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Approved Alert -->
            @if($workingPaper->status === 'approved')
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-6 rounded-r-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-semibold text-green-800">
                                Working Paper Approved
                            </h3>
                            <p class="mt-1 text-sm text-green-700">
                                Your working paper has been approved by {{ $workingPaper->reviewer->name ?? 'admin' }}.
                            </p>
                            <p class="mt-2 text-xs text-green-600">
                                Approved: {{ $workingPaper->reviewed_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Define Alpine component BEFORE using it -->
            <script>
                function workingPaperApp(initialTypes) {
                    return {
                        selectedTypes: Array.isArray(initialTypes) ? initialTypes : [],
                    }
                }

                function fileViewerData() {
                    return {
                        ...fileViewerModal(),
                        
                        openFile(url, name) {
                            this.openModal(url, name);
                        }
                    }
                }
            </script>

            <!-- Main Container with Alpine.js -->
            <div x-data="workingPaperApp({{ json_encode($workingPaper->selected_types ?? []) }})" class="space-y-6">
                
                <!-- Type Selector Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Financial Year Switcher -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Financial Year</label>
                            <select 
                                onchange="window.location.href='{{ route('client.dashboard') }}?year='+this.value" 
                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                @foreach($financialYears as $year)
                                    <option value="{{ $year }}" {{ $year === $workingPaper->financial_year ? 'selected' : '' }}>
                                        {{ $year }}
                                        @if($year === $workingPaper->financial_year)
                                            (Current)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                Select a year to view or create working papers for that period
                            </p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Select Work Types</label>
                            
                            <form method="POST" action="{{ route('client.working-paper.update-types', $workingPaper) }}">
                                @csrf
                                @method('PATCH')
                                
                                <div class="flex flex-wrap gap-3 mb-4">
                                    @foreach($availableTypes as $key => $label)
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input 
                                                type="checkbox" 
                                                name="selected_types[]" 
                                                value="{{ $key }}"
                                                x-model="selectedTypes"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                {{ in_array($key, $workingPaper->selected_types ?? []) ? 'checked' : '' }}
                                                {{ !$workingPaper->canBeEditedByClient() ? 'disabled' : '' }}
                                            >
                                            <span class="ml-2 text-sm font-medium text-gray-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                @if($workingPaper->canBeEditedByClient())
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                        Update Types
                                    </button>
                                @else
                                    <p class="text-sm text-amber-600">
                                        @if($workingPaper->status === 'approved')
                                            This working paper has been approved. No changes allowed.
                                        @else
                                            This working paper is under review. Work types cannot be changed.
                                        @endif
                                    </p>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Wage Section -->
                <div x-show="selectedTypes.includes('wage')" x-cloak>
                    @include('client.sections.wage', ['workingPaper' => $workingPaper])
                </div>

                <!-- Rental Property Section -->
                <div x-show="selectedTypes.includes('rental')" x-cloak>
                    @include('client.sections.rental', ['workingPaper' => $workingPaper])
                </div>

                <!-- Sole Trader Section -->
                <div x-show="selectedTypes.includes('sole_trader')" x-cloak>
                    @include('client.sections.sole-trader', ['workingPaper' => $workingPaper])
                </div>

                <!-- BAS Section -->
                <div x-show="selectedTypes.includes('bas')" x-cloak>
                    @include('client.sections.bas', ['workingPaper' => $workingPaper])
                </div>

                <!-- Company Tax Section -->
                <div x-show="selectedTypes.includes('ctax')" x-cloak>
                    @include('client.sections.ctax', ['workingPaper' => $workingPaper])
                </div>

                <!-- Trust Tax Section -->
                <div x-show="selectedTypes.includes('ttax')" x-cloak>
                    @include('client.sections.ttax', ['workingPaper' => $workingPaper])
                </div>

                <!-- SMSF Section -->
                <div x-show="selectedTypes.includes('smsf')" x-cloak>
                    @include('client.sections.smsf', ['workingPaper' => $workingPaper])
                </div>

                <!-- Submit Button -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-show="selectedTypes.length > 0">
                    <div class="p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600">
                                    Status: 
                                    <span class="font-semibold px-2 py-1 rounded {{ $workingPaper->status_color }}">
                                        {{ $workingPaper->status_label }}
                                    </span>
                                </p>
                                @if($workingPaper->submitted_at)
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $workingPaper->status === 'resubmitted' ? 'Resubmitted' : 'Submitted' }}: 
                                        {{ $workingPaper->submitted_at->format('d M Y, h:i A') }}
                                    </p>
                                @endif
                                @if($workingPaper->reviewed_at)
                                    <p class="text-sm text-gray-600 mt-1">
                                        Reviewed: {{ $workingPaper->reviewed_at->format('d M Y, h:i A') }}
                                    </p>
                                @endif
                            </div>
                            
                            @if($workingPaper->canBeEditedByClient())
                                <form method="POST" action="{{ route('client.working-paper.submit', $workingPaper) }}">
                                    @csrf
                                    <button 
                                        type="submit" 
                                        onclick="return confirm('{{ $workingPaper->status === 'rejected' ? 'Resubmit this working paper for review?' : 'Submit this working paper? You will not be able to edit it after submission.' }}')"
                                        class="px-6 py-3 {{ $workingPaper->status === 'rejected' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-md font-semibold"
                                    >
                                        {{ $workingPaper->status === 'rejected' ? 'Resubmit for Review' : 'Submit All Data' }}
                                    </button>
                                </form>
                            @else
<div class="flex flex-col gap-3">
    <div class="text-sm text-gray-500 italic">
        @if($workingPaper->status === 'approved')
            This working paper has been approved
        @else
            This working paper is under review
        @endif
    </div>

    <!-- Export PDF Button (only if approved) -->
    @if($workingPaper->status === 'approved')
        <a
            href="{{ route('client.working-paper.export-pdf', $workingPaper) }}"
            target="_blank"
            class="inline-flex items-center justify-center px-5 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-md text-sm font-semibold w-fit"
        >
            Export as PDF
        </a>
    @endif
</div>
@endif


                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>