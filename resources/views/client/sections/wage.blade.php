<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            Wage Section
        </h3>

        <!-- Display Summary if data exists -->
        @if($workingPaper->wageData && ($workingPaper->wageData->salary_wages || $workingPaper->wageData->tax_withheld))
            <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg">
                <h4 class="text-md font-semibold text-gray-800 mb-3">Current Wage Information</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <p class="text-xs text-gray-600 mb-1">Gross Wages</p>
                        <p class="text-xl font-bold text-green-600">
                            ${{ number_format($workingPaper->wageData->salary_wages ?? 0, 2) }}
                        </p>
                    </div>
                    
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <p class="text-xs text-gray-600 mb-1">Tax Withheld</p>
                        <p class="text-xl font-bold text-red-600">
                            ${{ number_format($workingPaper->wageData->tax_withheld ?? 0, 2) }}
                        </p>
                    </div>
                    
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <p class="text-xs text-gray-600 mb-1">Net Income</p>
                        <p class="text-xl font-bold text-indigo-600">
                            ${{ number_format(($workingPaper->wageData->salary_wages ?? 0) - ($workingPaper->wageData->tax_withheld ?? 0), 2) }}
                        </p>
                    </div>
                </div>

                @if($workingPaper->wageData->other_employment_items)
                    <div class="p-3 bg-white rounded-lg shadow-sm mb-3">
                        <p class="text-xs font-medium text-gray-600 mb-1">Other Employment Items</p>
                        <p class="text-sm text-gray-700">{{ $workingPaper->wageData->other_employment_items }}</p>
                    </div>
                @endif

                @if($workingPaper->wageData->hasMedia('payg_summary'))
                    <div class="p-3 bg-white rounded-lg shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-600">PAYG Summary</p>
                            <p class="text-sm text-gray-500">{{ $workingPaper->wageData->getMedia('payg_summary')->first()?->file_name ?? 'Document attached' }}</p>
                        </div>
                        <button @click="$dispatch('open-file-viewer', { url: '{{ route('media.view-wage', $workingPaper->wageData) }}', name: '{{ $workingPaper->wageData->getMedia('payg_summary')->first()?->file_name ?? 'PAYG Summary' }}' })" type="button" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                            View Document
                        </button>
                    </div>
                @endif
            </div>
        @endif

        <!-- Form Section (Collapsible when data exists) -->
        <div x-data="{ showForm: {{ $workingPaper->wageData && ($workingPaper->wageData->salary_wages || $workingPaper->wageData->tax_withheld) ? 'false' : 'true' }} }">
            @if($workingPaper->wageData && ($workingPaper->wageData->salary_wages || $workingPaper->wageData->tax_withheld))
                <button @click="showForm = !showForm" type="button" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 mb-3">
                    <span x-show="!showForm">Edit Wage Data</span>
                    <span x-show="showForm">Cancel</span>
                </button>
            @endif

            <div x-show="showForm" x-cloak>
                <form method="POST" action="{{ route('client.wage.save', $workingPaper) }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Salary/Wages</label>
                            <input type="number" step="0.01" name="salary_wages" value="{{ $workingPaper->wageData->salary_wages ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.00">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tax Withheld</label>
                            <input type="number" step="0.01" name="tax_withheld" value="{{ $workingPaper->wageData->tax_withheld ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.00">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Other Employment Items</label>
                            <textarea name="other_employment_items" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Any additional employment-related information...">{{ $workingPaper->wageData->other_employment_items ?? '' }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Upload PAYG Summary (Optional)</label>
                            <input type="file" name="payg_summary" accept=".pdf,.jpg,.jpeg,.png" class="w-full">
                            <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max 10MB)</p>
                            
                            @if($workingPaper->wageData && $workingPaper->wageData->hasMedia('payg_summary'))
                                <div class="mt-2">
                                    <button @click="$dispatch('open-file-viewer', {url: '{{ route('media.view-wage', $workingPaper->wageData) }}', name: '{{ $workingPaper->wageData->getMedia('payg_summary')->first()?->file_name ?? 'PAYG Summary' }}'})" type="button" class="text-blue-600 hover:underline cursor-pointer text-sm">
                                        View current PAYG summary
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Save Wage Data
                        </button>
                        @if($workingPaper->wageData && ($workingPaper->wageData->salary_wages || $workingPaper->wageData->tax_withheld))
                            <button type="button" @click="showForm = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Cancel
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>