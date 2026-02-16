<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            BAS (Business Activity Statement) Section
        </h3>

        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
            <p class="text-sm text-yellow-800">
                <strong>Important:</strong> BAS requires quarterly data (Q1-Q4 only). The "All" option is disabled to prevent double-counting in your BAS lodgement.
            </p>
        </div>

        <!-- Income Section -->
        <div class="mb-8" x-data="{ editingIncomeId: null }">
            <h4 class="text-md font-semibold text-gray-800 mb-3">Income (Sales)</h4>
            
            @php
                $basIncomes = $workingPaper->incomeItems->where('section_type', 'bas');
                $incomeByQuarter = [
                    'q1' => $basIncomes->where('quarter', 'q1'),
                    'q2' => $basIncomes->where('quarter', 'q2'),
                    'q3' => $basIncomes->where('quarter', 'q3'),
                    'q4' => $basIncomes->where('quarter', 'q4'),
                ];
            @endphp

            @if($basIncomes->count() > 0)
                <div class="overflow-x-auto mb-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quarter</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($basIncomes as $income)
                                <!-- Normal Row -->
                                <tr x-show="editingIncomeId !== {{ $income->id }}" class="{{ $loop->iteration % 2 == 0 ? 'bg-gray-50' : '' }}">
                                    <td class="px-4 py-2 text-sm font-semibold">{{ strtoupper($income->quarter) }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $income->description }}</td>
                                    <td class="px-4 py-2 text-sm font-medium">${{ number_format($income->amount, 2) }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        @if($workingPaper->canBeEditedByClient())
                                            <button @click="editingIncomeId = {{ $income->id }}" type="button" class="text-blue-600 hover:text-blue-800 mr-3">
                                                Edit
                                            </button>
                                            <form method="POST" action="{{ route('client.income.destroy', $income) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Delete this income item?')" class="text-red-600 hover:text-red-800">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Edit Row -->
                                <tr x-show="editingIncomeId === {{ $income->id }}" x-cloak class="bg-blue-50">
                                    <td colspan="4" class="px-4 py-4">
                                        <form method="POST" action="{{ route('client.income.update', $income) }}">
                                            @csrf
                                            @method('PATCH')

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                                    <input type="text" name="description" value="{{ $income->description }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                                                    <input type="number" step="0.01" name="amount" value="{{ $income->amount }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quarter (BAS)</label>
                                                    <select name="quarter" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        <option value="q1" {{ $income->quarter === 'q1' ? 'selected' : '' }}>Q1 (Jul-Sep)</option>
                                                        <option value="q2" {{ $income->quarter === 'q2' ? 'selected' : '' }}>Q2 (Oct-Dec)</option>
                                                        <option value="q3" {{ $income->quarter === 'q3' ? 'selected' : '' }}>Q3 (Jan-Mar)</option>
                                                        <option value="q4" {{ $income->quarter === 'q4' ? 'selected' : '' }}>Q4 (Apr-Jun)</option>
                                                    </select>
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Client Comment</label>
                                                    <textarea name="client_comment" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $income->client_comment }}</textarea>
                                                </div>
                                            </div>

                                            <div class="mt-4 flex gap-2">
                                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                                                    Save Changes
                                                </button>
                                                <button type="button" @click="editingIncomeId = null" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100">
                            <tr>
                                <td colspan="2" class="px-4 py-2 text-sm font-bold text-gray-900">Total Income</td>
                                <td class="px-4 py-2 text-sm font-bold text-green-600">${{ number_format($basIncomes->sum('amount'), 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Quarterly Breakdown -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                    @foreach(['q1' => 'Q1 (Jul-Sep)', 'q2' => 'Q2 (Oct-Dec)', 'q3' => 'Q3 (Jan-Mar)', 'q4' => 'Q4 (Apr-Jun)'] as $qKey => $qLabel)
                        <div class="p-3 {{ $incomeByQuarter[$qKey]->count() > 0 ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }} border rounded">
                            <p class="text-xs text-gray-600">{{ $qLabel }}</p>
                            <p class="text-lg font-bold {{ $incomeByQuarter[$qKey]->count() > 0 ? 'text-green-700' : 'text-gray-400' }}">
                                ${{ number_format($incomeByQuarter[$qKey]->sum('amount'), 2) }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $incomeByQuarter[$qKey]->count() }} item(s)</p>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Add Income Form -->
            @if($workingPaper->canBeEditedByClient())
                <div x-data="{ showIncomeForm: false }">
                    <button 
                        @click="showIncomeForm = !showIncomeForm; editingIncomeId = null" 
                        type="button"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 mb-3"
                    >
                        + Add Income Line
                    </button>

                    <div x-show="showIncomeForm" x-cloak class="border rounded-lg p-4 bg-gray-50">
                        <form method="POST" action="{{ route('client.income.store', $workingPaper) }}">
                            @csrf
                            <input type="hidden" name="section_type" value="bas">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                                    <input type="text" name="description" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g., Sales revenue">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" name="amount" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.00">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quarter (BAS) <span class="text-red-500">*</span></label>
                                    <select name="quarter" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select Quarter</option>
                                        <option value="q1">Q1 (Jul-Sep)</option>
                                        <option value="q2">Q2 (Oct-Dec)</option>
                                        <option value="q3">Q3 (Jan-Mar)</option>
                                        <option value="q4">Q4 (Apr-Jun)</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Client Comment (Optional)</label>
                                    <textarea name="client_comment" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>
                            </div>

                            <div class="mt-4 flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    Save Income
                                </button>
                                <button type="button" @click="showIncomeForm = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- Expense Section -->
        <div x-data="{ editingExpenseId: null }">
            <h4 class="text-md font-semibold text-gray-800 mb-3">Expenses</h4>
            
            @php
                $basExpenses = $workingPaper->expenseItems->where('section_type', 'bas');
                $expenseByQuarter = [
                    'q1' => $basExpenses->where('quarter', 'q1'),
                    'q2' => $basExpenses->where('quarter', 'q2'),
                    'q3' => $basExpenses->where('quarter', 'q3'),
                    'q4' => $basExpenses->where('quarter', 'q4'),
                ];
            @endphp

            @if($basExpenses->count() > 0)
                <div class="overflow-x-auto mb-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quarter</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Inc GST</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">GST</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Net</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Receipt</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($basExpenses as $expense)
                                <!-- Normal Row -->
                                <tr x-show="editingExpenseId !== {{ $expense->id }}" class="{{ $loop->iteration % 2 == 0 ? 'bg-gray-50' : '' }}">
                                    <td class="px-4 py-2 text-sm font-semibold">{{ strtoupper($expense->quarter) }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $expense->description }}</td>
                                    <td class="px-4 py-2 text-sm">${{ number_format($expense->amount_inc_gst, 2) }}</td>
                                    <td class="px-4 py-2 text-sm">${{ number_format($expense->gst_amount, 2) }}</td>
                                    <td class="px-4 py-2 text-sm">${{ number_format($expense->net_ex_gst, 2) }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        @if($expense->hasMedia('receipts'))
                                            <button @click="$dispatch('open-file-viewer', {url: '{{ route('media.view-expense', $expense) }}', name: '{{ $expense->getFirstMedia('receipts')->file_name }}'})" type="button" class="text-blue-600 hover:underline">View</button>
                                        @else
                                            <span class="text-red-600">Missing</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        @if($workingPaper->canBeEditedByClient())
                                            <button @click="editingExpenseId = {{ $expense->id }}" type="button" class="text-blue-600 hover:text-blue-800 mr-3">
                                                Edit
                                            </button>
                                            <form method="POST" action="{{ route('client.expense.destroy', $expense) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Delete this expense?')" class="text-red-600 hover:text-red-800">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Edit Row -->
                                <tr x-show="editingExpenseId === {{ $expense->id }}" x-cloak class="bg-blue-50" x-data="{ autoCalculateGST: true, amountIncGst: {{ $expense->amount_inc_gst }} }">
                                    <td colspan="7" class="px-4 py-4">
                                        <form method="POST" action="{{ route('client.expense.update', $expense) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PATCH')

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                                    <input type="text" name="description" value="{{ $expense->description }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quarter (BAS)</label>
                                                    <select name="quarter" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        <option value="q1" {{ $expense->quarter === 'q1' ? 'selected' : '' }}>Q1 (Jul-Sep)</option>
                                                        <option value="q2" {{ $expense->quarter === 'q2' ? 'selected' : '' }}>Q2 (Oct-Dec)</option>
                                                        <option value="q3" {{ $expense->quarter === 'q3' ? 'selected' : '' }}>Q3 (Jan-Mar)</option>
                                                        <option value="q4" {{ $expense->quarter === 'q4' ? 'selected' : '' }}>Q4 (Apr-Jun)</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (inc GST)</label>
                                                    <input type="number" step="0.01" name="amount_inc_gst" x-model="amountIncGst" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">GST Amount</label>
                                                    <input type="number" step="0.01" name="gst_amount" :value="autoCalculateGST ? (amountIncGst - (amountIncGst / 1.1)).toFixed(2) : '{{ $expense->gst_amount }}'" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :readonly="autoCalculateGST">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Net (ex GST)</label>
                                                    <input type="number" step="0.01" name="net_ex_gst" :value="autoCalculateGST ? (amountIncGst / 1.1).toFixed(2) : '{{ $expense->net_ex_gst }}'" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :readonly="autoCalculateGST">
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="inline-flex items-center">
                                                        <input type="checkbox" x-model="autoCalculateGST" class="rounded border-gray-300">
                                                        <span class="ml-2 text-sm text-gray-700">Auto-calculate GST (10%)</span>
                                                    </label>
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Receipt</label>
                                                    @if($expense->hasMedia('receipts'))
                                                        <div class="mb-2 p-2 bg-gray-50 rounded border">
                                                            <p class="text-xs text-gray-600 mb-2">Current: {{ $expense->getFirstMedia('receipts')->file_name }}</p>
                                                            <label class="inline-flex items-center">
                                                                <input type="checkbox" name="remove_receipt" value="1" class="rounded border-gray-300">
                                                                <span class="ml-2 text-sm text-red-600">Remove current receipt</span>
                                                            </label>
                                                        </div>
                                                    @endif
                                                    <input type="file" name="receipt" accept=".pdf,.jpg,.jpeg,.png" class="w-full">
                                                    <p class="text-xs text-gray-500 mt-1">Upload new file to replace existing</p>
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Client Comment</label>
                                                    <textarea name="client_comment" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $expense->client_comment }}</textarea>
                                                </div>
                                            </div>

                                            <div class="mt-4 flex gap-2">
                                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                                                    Save Changes
                                                </button>
                                                <button type="button" @click="editingExpenseId = null" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100">
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-sm font-bold text-gray-900">Total Expenses (Net)</td>
                                <td class="px-4 py-2 text-sm font-bold text-red-600">${{ number_format($basExpenses->sum('net_ex_gst'), 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Quarterly Breakdown -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                    @foreach(['q1' => 'Q1 (Jul-Sep)', 'q2' => 'Q2 (Oct-Dec)', 'q3' => 'Q3 (Jan-Mar)', 'q4' => 'Q4 (Apr-Jun)'] as $qKey => $qLabel)
                        <div class="p-3 {{ $expenseByQuarter[$qKey]->count() > 0 ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200' }} border rounded">
                            <p class="text-xs text-gray-600">{{ $qLabel }}</p>
                            <p class="text-lg font-bold {{ $expenseByQuarter[$qKey]->count() > 0 ? 'text-red-700' : 'text-gray-400' }}">
                                ${{ number_format($expenseByQuarter[$qKey]->sum('net_ex_gst'), 2) }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $expenseByQuarter[$qKey]->count() }} item(s)</p>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Add Expense Form -->
            @if($workingPaper->canBeEditedByClient())
                <div x-data="{ showExpenseForm: false, autoCalculateGST: true, amountIncGst: 0 }">
                    <button 
                        @click="showExpenseForm = !showExpenseForm; editingExpenseId = null" 
                        type="button"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 mb-3"
                    >
                        + Add Expense Line
                    </button>

                    <div x-show="showExpenseForm" x-cloak class="border rounded-lg p-4 bg-gray-50">
                        <form method="POST" action="{{ route('client.expense.store', $workingPaper) }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="section_type" value="bas">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                                    <input type="text" name="description" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quarter (BAS) <span class="text-red-500">*</span></label>
                                    <select name="quarter" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select Quarter</option>
                                        <option value="q1">Q1 (Jul-Sep)</option>
                                        <option value="q2">Q2 (Oct-Dec)</option>
                                        <option value="q3">Q3 (Jan-Mar)</option>
                                        <option value="q4">Q4 (Apr-Jun)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (inc GST) <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" name="amount_inc_gst" x-model="amountIncGst" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">GST Amount</label>
                                    <input type="number" step="0.01" name="gst_amount" :value="autoCalculateGST ? (amountIncGst - (amountIncGst / 1.1)).toFixed(2) : ''" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :readonly="autoCalculateGST">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Net (ex GST)</label>
                                    <input type="number" step="0.01" name="net_ex_gst" :value="autoCalculateGST ? (amountIncGst / 1.1).toFixed(2) : ''" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :readonly="autoCalculateGST">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" x-model="autoCalculateGST" class="rounded border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700">Auto-calculate GST (10%)</span>
                                    </label>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Receipt <span class="text-red-500">*</span></label>
                                    <input type="file" name="receipt" required accept=".pdf,.jpg,.jpeg,.png" class="w-full">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Client Comment (Optional)</label>
                                    <textarea name="client_comment" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>
                            </div>

                            <div class="mt-4 flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    Save Expense
                                </button>
                                <button type="button" @click="showExpenseForm = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- Annual Summary -->
        @if($basIncomes->count() > 0 || $basExpenses->count() > 0)
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <h5 class="font-semibold text-gray-900 mb-2">Annual BAS Summary</h5>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Total Income:</p>
                        <p class="text-lg font-bold text-green-600">${{ number_format($basIncomes->sum('amount'), 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Expenses (Net):</p>
                        <p class="text-lg font-bold text-red-600">${{ number_format($basExpenses->sum('net_ex_gst'), 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total GST Collected:</p>
                        <p class="text-lg font-bold text-blue-600">${{ number_format($basIncomes->sum('amount') * 0.1, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total GST Paid:</p>
                        <p class="text-lg font-bold text-orange-600">${{ number_format($basExpenses->sum('gst_amount'), 2) }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>