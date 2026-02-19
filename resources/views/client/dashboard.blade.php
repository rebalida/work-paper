<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                {{ __('Tax Data Capture') }}
            </h2>
            <p class="text-sm text-gray-500">Manage your financial data for the tax year.</p>
        </div>
    </x-slot>

    <div x-data="fileViewerData()" @open-file-viewer.window="openFile($event.detail.url, $event.detail.name)">
        @include('components.file-viewer-modal')
    </div>

    <div x-data="workingPaperApp({{ json_encode($workingPaper->selected_types ?? []) }})" class="space-y-8 max-w-7xl mx-auto">
        
        @if (session('success'))
            <div class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-100 shadow-sm" role="alert">
                <x-heroicon-o-information-circle class="flex-shrink-0 w-5 h-5 mr-3" />
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if($workingPaper->status === 'rejected' && $workingPaper->admin_comment)
            <div class="bg-red-50 border border-red-200 rounded-xl p-6 shadow-sm">
                <div class="flex gap-4">
                    <div class="p-3 bg-red-100 rounded-full h-fit text-red-600">
                        <x-heroicon-o-exclamation-circle class="w-6 h-6" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-red-900">Action Required: Revision Needed</h3>
                        <p class="mt-1 text-red-700">The reviewer has returned your working paper. Please address the feedback below:</p>
                        <div class="mt-3 bg-white p-4 rounded-lg border border-red-100 text-gray-800 italic">
                            "{{ $workingPaper->admin_comment }}"
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(in_array($workingPaper->status, ['submitted', 'resubmitted', 'approved']))
            <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-6 flex items-center gap-4 shadow-sm">
                <div class="p-3 {{ $workingPaper->status === 'approved' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' }} rounded-full">
                    @if($workingPaper->status === 'approved')
                        <x-heroicon-o-check class="w-6 h-6" />
                    @else
                        <x-heroicon-o-clock class="w-6 h-6" />
                    @endif
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">
                        @if($workingPaper->status === 'approved')
                            Approved & Finalized
                        @else
                            Submission Under Review
                        @endif
                    </h3>
                    <p class="text-sm text-gray-500">
                        Status updated: {{ $workingPaper->updated_at->diffForHumans() }}
                    </p>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-lg text-gray-900">Setup & Configuration</h3>
            </div>
            <div class="p-6">
                <div class="max-w-xs mb-8">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Financial Year</label>
                    <div class="relative">
                        <select 
                            onchange="window.location.href='{{ route('client.dashboard') }}?year='+this.value" 
                            class="block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg shadow-sm bg-gray-50"
                        >
                            @foreach($financialYears as $year)
                                <option value="{{ $year }}" {{ $year === $workingPaper->financial_year ? 'selected' : '' }}>
                                    Financial Year {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Work Modules Required</label>
                    <form method="POST" action="{{ route('client.working-paper.update-types', $workingPaper) }}">
                        @csrf
                        @method('PATCH')
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
                            @foreach($availableTypes as $key => $label)
                                <label class="relative flex flex-col items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition-all duration-200" 
                                       :class="selectedTypes.includes('{{ $key }}') ? 'border-indigo-500 bg-indigo-50/30 ring-1 ring-indigo-500' : 'border-gray-200'">
                                    
                                    <input type="checkbox" name="selected_types[]" value="{{ $key }}" x-model="selectedTypes" class="absolute top-3 right-3 h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" {{ !$workingPaper->canBeEditedByClient() ? 'disabled' : '' }}>
                                    
                                    <div class="w-10 h-10 mb-3 rounded-full flex items-center justify-center"
                                         :class="selectedTypes.includes('{{ $key }}') ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-400'">
                                        @if($key == 'wage')
                                            <x-heroicon-o-banknotes class="w-5 h-5" />
                                        @elseif($key == 'rental')
                                            <x-heroicon-o-home class="w-5 h-5" />
                                        @elseif($key == 'bas')
                                            <x-heroicon-o-document-text class="w-5 h-5" />
                                        @elseif($key == 'sole_trader')
                                            <x-heroicon-o-briefcase class="w-5 h-5" />
                                        @elseif($key == 'ctax')
                                            <x-heroicon-o-building-office class="w-5 h-5" />
                                        @elseif($key == 'ttax')
                                            <x-heroicon-o-receipt-percent class="w-5 h-5" />
                                        @elseif($key == 'smsf')
                                            <x-heroicon-o-chart-bar class="w-5 h-5" />
                                        @else
                                            <x-heroicon-o-chart-bar-square class="w-5 h-5" />
                                        @endif
                                    </div>
                                    <span class="text-sm font-medium text-center" :class="selectedTypes.includes('{{ $key }}') ? 'text-indigo-900' : 'text-gray-600'">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>

                        @if($workingPaper->canBeEditedByClient())
                            <div class="flex justify-end">
                                <button type="submit" class="text-sm px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700 transition-colors shadow-sm">
                                    Update Modules
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div x-show="selectedTypes.includes('wage')" x-cloak x-transition>
                @include('client.sections.wage', ['workingPaper' => $workingPaper])
            </div>

            <div x-show="selectedTypes.includes('rental')" x-cloak x-transition>
                @include('client.sections.rental', ['workingPaper' => $workingPaper])
            </div>

            <div x-show="selectedTypes.includes('sole_trader')" x-cloak x-transition>
                @include('client.sections.sole-trader', ['workingPaper' => $workingPaper])
            </div>

            <div x-show="selectedTypes.includes('bas')" x-cloak x-transition>
                @include('client.sections.bas', ['workingPaper' => $workingPaper])
            </div>

            <div x-show="selectedTypes.includes('ctax')" x-cloak x-transition>
                @include('client.sections.ctax', ['workingPaper' => $workingPaper])
            </div>

            <div x-show="selectedTypes.includes('ttax')" x-cloak x-transition>
                @include('client.sections.ttax', ['workingPaper' => $workingPaper])
            </div>

            <div x-show="selectedTypes.includes('smsf')" x-cloak x-transition>
                @include('client.sections.smsf', ['workingPaper' => $workingPaper])
            </div>
        </div>

        <div class="sticky bottom-4 z-30" x-show="selectedTypes.length > 0" x-transition.opacity.duration.500ms>
            <div class="bg-slate-900 text-white rounded-xl shadow-2xl p-4 flex flex-col md:flex-row justify-between items-center gap-4 max-w-4xl mx-auto border border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="hidden md:block p-2 bg-slate-800 rounded-lg text-indigo-400">
                        <x-heroicon-o-check-circle class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="font-bold text-lg">Ready to submit?</p>
                        <p class="text-xs text-slate-400">Ensure all data is accurate before finalizing.</p>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    @if($workingPaper->status === 'approved')
                        <a href="{{ route('client.working-paper.export-pdf', $workingPaper) }}" target="_blank" class="px-5 py-2.5 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-sm font-semibold transition-colors">
                            Download PDF
                        </a>
                    @elseif($workingPaper->canBeEditedByClient())
                        <form method="POST" action="{{ route('client.working-paper.submit', $workingPaper) }}">
                            @csrf
                            <button 
                                type="submit" 
                                onclick="return confirm('{{ $workingPaper->status === 'rejected' ? 'Resubmit this working paper for review?' : 'Submit this working paper? You will not be able to edit it after submission.' }}')"
                                class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-bold shadow-lg shadow-indigo-500/30 transition-all hover:scale-105"
                            >
                                {{ $workingPaper->status === 'rejected' ? 'Resubmit for Review' : 'Finalize & Submit' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

    </div>

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
</x-app-layout>