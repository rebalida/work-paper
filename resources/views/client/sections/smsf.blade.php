<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            SMSF Section
        </h3>

        <!-- Income Section -->
        <div class="mb-8" x-data="{ editingIncomeId: null }">
            <h4 class="text-md font-semibold text-gray-800 mb-3">Income</h4>
            
            @php
                $smsfIncomes = $workingPaper->incomeItems->where('section_type', 'smsf');
            @endphp

            @if($smsfIncomes->count() > 0)
                <div class="overflow-x-auto mb-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quarter</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($smsfIncomes as $income)
                                <!-- Normal Row -->
                                <tr x-show="editingIncomeId !== {{ $income->id }}">
                                    <td class="px-4 py-2 text-sm">{{ ucfirst($income->income_type ?? 'Other') }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $income->description }}</td>
                                    <td class="px-4 py-2 text-sm">${{ number_format($income->amount, 2) }}</td>
                                    <td class="px-4 py-2 text-sm">{{ strtoupper($income->quarter ?? 'All') }}</td>
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
                                    <td colspan="5" class="px-4 py-4">
                                        <form method="POST" action="{{ route('client.income.update', $income) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PATCH')
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Income Type</label>
                                                    <select name="income_type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        <option value="contribution" @if($income->income_type == 'contribution') selected @endif>Contribution</option>
                                                        <option value="interest" @if($income->income_type == 'interest') selected @endif>Interest</option>
                                                        <option value="dividend" @if($income->income_type == 'dividend') selected @endif>Dividend</option>
                                                        <option value="rent" @if($income->income_type == 'rent') selected @endif>Rent</option>
                                                        <option value="capital_gain" @if($income->income_type == 'capital_gain') selected @endif>Capital Gain</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                                                    <input type="number" step="0.01" name="amount" value="{{ $income->amount }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.00">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                                    <input type="text" name="description" value="{{ $income->description }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g., Monthly super contributions">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quarter</label>
                                                    <select name="quarter" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        <option value="all" @if($income->quarter == 'all') selected @endif>All</option>
                                                        <option value="q1" @if($income->quarter == 'q1') selected @endif>Q1 (Jul-Sep)</option>
                                                        <option value="q2" @if($income->quarter == 'q2') selected @endif>Q2 (Oct-Dec)</option>
                                                        <option value="q3" @if($income->quarter == 'q3') selected @endif>Q3 (Jan-Mar)</option>
                                                        <option value="q4" @if($income->quarter == 'q4') selected @endif>Q4 (Apr-Jun)</option>
                                                    </select>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Invoice</label>
                                                    @if($income->hasMedia('invoices'))
                                                        <div class="mb-2 p-2 bg-gray-50 rounded border">
                                                            <p class="text-xs text-gray-600 mb-2">Current: {{ $income->getFirstMedia('invoices')->file_name }}</p>
                                                            <label class="inline-flex items-center">
                                                                <input type="checkbox" name="remove_invoice" value="1" class="rounded border-gray-300">
                                                                <span class="ml-2 text-sm text-red-600">Remove current invoice</span>
                                                            </label>
                                                        </div>
                                                    @endif
                                                    <input type="file" name="invoice" accept=".pdf,.jpg,.jpeg,.png" class="w-full">
                                                    <p class="text-xs text-gray-500 mt-1">Upload new file to replace existing (optional)</p>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Client Comment (Optional)</label>
                                                    <textarea name="client_comment" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $income->client_comment }}</textarea>
                                                </div>
                                            </div>
                                            <div class="mt-4 flex gap-2">
                                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                                                    Save Income
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
                    </table>
                </div>
            @endif

            <!-- Add Income Form -->
            @if($workingPaper->canBeEditedByClient())
                <div x-data="{ showIncomeForm: false }">
                    <button @click="showIncomeForm = !showIncomeForm; editingIncomeId = null" type="button" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 mb-3">
                        + Add Income Line
                    </button>

                    <div x-show="showIncomeForm" x-cloak class="border rounded-lg p-4 bg-gray-50">
                        <form method="POST" action="{{ route('client.income.store', $workingPaper) }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="section_type" value="smsf">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Income Type</label>
                                    <select name="income_type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select Type</option>
                                        <option value="contribution">Contribution</option>
                                        <option value="interest">Interest</option>
                                        <option value="dividend">Dividend</option>
                                        <option value="rent">Rent</option>
                                        <option value="capital_gain">Capital Gain</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                                    <input type="number" step="0.01" name="amount" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.00">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <input type="text" name="description" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g., Monthly super contributions">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quarter</label>
                                    <select name="quarter" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="all">All</option>
                                        <option value="q1">Q1 (Jul-Sep)</option>
                                        <option value="q2">Q2 (Oct-Dec)</option>
                                        <option value="q3">Q3 (Jan-Mar)</option>
                                        <option value="q4">Q4 (Apr-Jun)</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Invoice (Optional)</label>
                                    <input type="file" name="invoice" accept=".pdf,.jpg,.jpeg,.png" class="w-full">
                                    <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max 10MB)</p>
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
                $smsfExpenses = $workingPaper->expenseItems->where('section_type', 'smsf');
            @endphp

            @if($smsfExpenses->count() > 0)
                <div class="overflow-x-auto mb-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Inc GST</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">GST</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Net</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quarter</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Receipt</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($smsfExpenses as $expense)
                                <!-- Normal Row -->
                                <tr x-show="editingExpenseId !== {{ $expense->id }}">
                                    <td class="px-4 py-2 text-sm">{{ strtoupper($expense->field_type ?? '-') }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $expense->description }}</td>
                                    <td class="px-4 py-2 text-sm">${{ number_format($expense->amount_inc_gst, 2) }}</td>
                                    <td class="px-4 py-2 text-sm">${{ number_format($expense->gst_amount, 2) }}</td>
                                    <td class="px-4 py-2 text-sm">${{ number_format($expense->net_ex_gst, 2) }}</td>
                                    <td class="px-4 py-2 text-sm">{{ strtoupper($expense->quarter ?? 'All') }}</td>
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
                                                <button type="submit" onclick="return confirm('Delete this expense item?')" class="text-red-600 hover:text-red-800">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                <!-- Edit Row -->
                                <tr x-show="editingExpenseId === {{ $expense->id }}" x-cloak class="bg-blue-50" x-data="{ autoCalculateGST: true, amountIncGst: {{ $expense->amount_inc_gst }} }">
                                    <td colspan="8" class="px-4 py-4">
                                        <form method="POST" action="{{ route('client.expense.update', $expense) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PATCH')
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Field Type <span class="text-red-500">*</span></label>
                                                    <select name="field_type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        <option value="a" @if($expense->field_type == 'a') selected @endif>Type A</option>
                                                        <option value="b" @if($expense->field_type == 'b') selected @endif>Type B</option>
                                                        <option value="c" @if($expense->field_type == 'c') selected @endif>Type C</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quarter <span class="text-red-500">*</span></label>
                                                    <select name="quarter" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        <option value="all" @if($expense->quarter == 'all') selected @endif>All</option>
                                                        <option value="q1" @if($expense->quarter == 'q1') selected @endif>Q1 (Jul-Sep)</option>
                                                        <option value="q2" @if($expense->quarter == 'q2') selected @endif>Q2 (Oct-Dec)</option>
                                                        <option value="q3" @if($expense->quarter == 'q3') selected @endif>Q3 (Jan-Mar)</option>
                                                        <option value="q4" @if($expense->quarter == 'q4') selected @endif>Q4 (Apr-Jun)</option>
                                                    </select>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                                                    <input type="text" name="description" value="{{ $expense->description }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g., Accountant fees">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (inc GST) <span class="text-red-500">*</span></label>
                                                    <input type="number" step="0.01" name="amount_inc_gst" x-model="amountIncGst" value="{{ $expense->amount_inc_gst }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.00">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">GST Amount</label>
                                                    <input type="number" step="0.01" name="gst_amount" :value="autoCalculateGST ? (amountIncGst - (amountIncGst / 1.1)).toFixed(2) : ''" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Auto-calculated" :readonly="autoCalculateGST">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Net (ex GST)</label>
                                                    <input type="number" step="0.01" name="net_ex_gst" :value="autoCalculateGST ? (amountIncGst / 1.1).toFixed(2) : ''" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Auto-calculated" :readonly="autoCalculateGST">
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
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Client Comment (Optional)</label>
                                                    <textarea name="client_comment" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $expense->client_comment }}</textarea>
                                                </div>
                                            </div>
                                            <div class="mt-4 flex gap-2">
                                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                                                    Save Expense
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
                    </table>
                </div>
            @endif

            <!-- Add Expense Form -->
            @if($workingPaper->canBeEditedByClient())
                <div x-data="{ showExpenseForm: false, autoCalculateGST: true, amountIncGst: 0 }">
                    <button @click="showExpenseForm = !showExpenseForm; editingExpenseId = null" type="button" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 mb-3">
                        + Add Expense Line
                    </button>

                    <div x-show="showExpenseForm" x-cloak class="border rounded-lg p-4 bg-gray-50">
                        <form method="POST" action="{{ route('client.expense.store', $workingPaper) }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="section_type" value="smsf">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Field Type <span class="text-red-500">*</span></label>
                                    <select name="field_type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select Type</option>
                                        <option value="a">Type A</option>
                                        <option value="b">Type B</option>
                                        <option value="c">Type C</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quarter <span class="text-red-500">*</span></label>
                                    <select name="quarter" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="all">All</option>
                                        <option value="q1">Q1 (Jul-Sep)</option>
                                        <option value="q2">Q2 (Oct-Dec)</option>
                                        <option value="q3">Q3 (Jan-Mar)</option>
                                        <option value="q4">Q4 (Apr-Jun)</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                                    <input type="text" name="description" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g., Accountant fees">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (inc GST) <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" name="amount_inc_gst" x-model="amountIncGst" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.00">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">GST Amount</label>
                                    <input type="number" step="0.01" name="gst_amount" :value="autoCalculateGST ? (amountIncGst - (amountIncGst / 1.1)).toFixed(2) : ''" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Auto-calculated" :readonly="autoCalculateGST">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Net (ex GST)</label>
                                    <input type="number" step="0.01" name="net_ex_gst" :value="autoCalculateGST ? (amountIncGst / 1.1).toFixed(2) : ''" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Auto-calculated" :readonly="autoCalculateGST">
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
                                    <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max 10MB) - REQUIRED</p>
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

        <!-- Totals Summary -->
        @if($smsfIncomes->count() > 0 || $smsfExpenses->count() > 0)
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <h5 class="font-semibold text-gray-900 mb-2">Summary</h5>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Total Income:</p>
                        <p class="text-lg font-bold text-green-600">${{ number_format($smsfIncomes->sum('amount'), 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Expenses (Net):</p>
                        <p class="text-lg font-bold text-red-600">${{ number_format($smsfExpenses->sum('net_ex_gst'), 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total GST:</p>
                        <p class="text-lg font-bold text-blue-600">${{ number_format($smsfExpenses->sum('gst_amount'), 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Net Profit:</p>
                        <p class="text-lg font-bold {{ ($smsfIncomes->sum('amount') - $smsfExpenses->sum('net_ex_gst')) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ${{ number_format($smsfIncomes->sum('amount') - $smsfExpenses->sum('net_ex_gst'), 2) }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>