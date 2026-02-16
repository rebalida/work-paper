<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            Rental Property Section
        </h3>

        <!-- Add Property Form -->
        @if($workingPaper->canBeEditedByClient())
            <div x-data="{ showPropertyForm: false }" class="mb-6">
                <button 
                    @click="showPropertyForm = !showPropertyForm" 
                    type="button" 
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                >
                    + Add Property
                </button>

                <div x-show="showPropertyForm" x-cloak class="mt-4 border rounded-lg p-4 bg-gray-50">
                    <h4 class="font-semibold text-gray-800 mb-3">New Property</h4>
                    <form method="POST" action="{{ route('client.rental-property.store', $workingPaper) }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Property Address/Nickname <span class="text-red-500">*</span></label>
                                <input type="text" name="address_label" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g., 123 Main St or 'Sydney Rental'">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ownership % (Optional)</label>
                                <input type="number" step="0.01" min="0" max="100" name="ownership_percentage" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="100.00">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Period Rented (Optional)</label>
                                <input type="text" name="period_rented" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g., Full Year or Jan-Jun">
                            </div>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Add Property
                            </button>
                            <button type="button" @click="showPropertyForm = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Property List -->
        @if($workingPaper->rentalProperties->count() === 0)
            <div class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                <p class="text-gray-500 italic">No properties added yet. Click "Add Property" to start.</p>
            </div>
        @else
            @foreach($workingPaper->rentalProperties as $property)
                <div class="border rounded-lg p-5 mb-4 bg-gradient-to-r from-blue-50 to-white" x-data="{ propertyOpen: true, editingIncomeId: null, editingExpenseId: null }">
                    <!-- Property Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">{{ $property->address_label }}</h4>
                            <div class="text-sm text-gray-600 mt-1">
                                @if($property->ownership_percentage)
                                    <span class="mr-3">Ownership: {{ $property->ownership_percentage }}%</span>
                                @endif
                                @if($property->period_rented)
                                    <span>Period: {{ $property->period_rented }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button @click="propertyOpen = !propertyOpen" class="text-blue-600 hover:text-blue-800 text-sm">
                                <span x-show="propertyOpen">Hide</span>
                                <span x-show="!propertyOpen">Show</span>
                            </button>
                            @if($workingPaper->canBeEditedByClient())
                                <form method="POST" action="{{ route('client.rental-property.destroy', $property) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this property and all its income/expenses?')" class="text-red-600 hover:text-red-800 text-sm">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div x-show="propertyOpen" x-cloak>
                        <!-- Income Section for This Property -->
                        <div class="mb-6">
                            <h5 class="font-semibold text-gray-800 mb-2">Income</h5>
                            
                            @php
                                $propertyIncomes = $property->incomeItems;
                            @endphp

                            @if($propertyIncomes->count() > 0)
                                <div class="overflow-x-auto mb-3">
                                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quarter</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($propertyIncomes as $income)
                                                <!-- Normal Row -->
                                                <tr x-show="editingIncomeId !== {{ $income->id }}">
                                                    <td class="px-3 py-2">{{ $income->description }}</td>
                                                    <td class="px-3 py-2">${{ number_format($income->amount, 2) }}</td>
                                                    <td class="px-3 py-2">{{ strtoupper($income->quarter ?? 'All') }}</td>
                                                    <td class="px-3 py-2">
                                                        @if($workingPaper->canBeEditedByClient())
                                                            <button @click="editingIncomeId = {{ $income->id }}" type="button" class="text-blue-600 hover:text-blue-800 mr-3">
                                                                Edit
                                                            </button>
                                                            <form method="POST" action="{{ route('client.income.destroy', $income) }}" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" onclick="return confirm('Delete this income?')" class="text-red-600 hover:text-red-800">Delete</button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>

                                                <!-- Edit Row -->
                                                <tr x-show="editingIncomeId === {{ $income->id }}" x-cloak class="bg-blue-50">
                                                    <td colspan="4" class="px-3 py-3">
                                                        <form method="POST" action="{{ route('client.income.update', $income) }}" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="rental_property_id" value="{{ $property->id }}">

                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                                                    <input type="text" name="description" value="{{ $income->description }}" required class="w-full text-sm rounded-md border-gray-300 shadow-sm">
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                                                                    <input type="number" step="0.01" name="amount" value="{{ $income->amount }}" required class="w-full text-sm rounded-md border-gray-300 shadow-sm">
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quarter</label>
                                                                    <select name="quarter" class="w-full text-sm rounded-md border-gray-300 shadow-sm">
                                                                        <option value="all" {{ $income->quarter === 'all' ? 'selected' : '' }}>All</option>
                                                                        <option value="q1" {{ $income->quarter === 'q1' ? 'selected' : '' }}>Q1</option>
                                                                        <option value="q2" {{ $income->quarter === 'q2' ? 'selected' : '' }}>Q2</option>
                                                                        <option value="q3" {{ $income->quarter === 'q3' ? 'selected' : '' }}>Q3</option>
                                                                        <option value="q4" {{ $income->quarter === 'q4' ? 'selected' : '' }}>Q4</option>
                                                                    </select>
                                                                </div>

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Invoice (Optional)</label>
                                                                    <input type="file" name="invoice" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm">
                                                                    @if($income->hasMedia('invoices'))
                                                                        <p class="text-xs text-gray-500 mt-1">Current: {{ $income->getFirstMedia('invoices')->file_name }}</p>
                                                                    @endif
                                                                </div>

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Client Comment</label>
                                                                    <textarea name="client_comment" rows="2" class="w-full text-sm rounded-md border-gray-300 shadow-sm">{{ $income->client_comment }}</textarea>
                                                                </div>
                                                            </div>

                                                            <div class="mt-3 flex gap-2">
                                                                <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700">Save Changes</button>
                                                                <button type="button" @click="editingIncomeId = null" class="px-3 py-1.5 bg-gray-300 text-gray-700 text-sm rounded hover:bg-gray-400">Cancel</button>
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
                                    <button 
                                        @click="showIncomeForm = !showIncomeForm; editingIncomeId = null" 
                                        type="button"
                                        class="px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700"
                                    >
                                        + Add Income
                                    </button>

                                    <div x-show="showIncomeForm" x-cloak class="mt-3 border rounded p-3 bg-white">
                                        <form method="POST" action="{{ route('client.income.store', $workingPaper) }}" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="section_type" value="rental">
                                            <input type="hidden" name="rental_property_id" value="{{ $property->id }}">

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                                                    <input type="text" name="description" required class="w-full text-sm rounded-md border-gray-300 shadow-sm" placeholder="e.g., Rent received">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount <span class="text-red-500">*</span></label>
                                                    <input type="number" step="0.01" name="amount" required class="w-full text-sm rounded-md border-gray-300 shadow-sm" placeholder="0.00">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quarter</label>
                                                    <select name="quarter" class="w-full text-sm rounded-md border-gray-300 shadow-sm">
                                                        <option value="all">All</option>
                                                        <option value="q1">Q1</option>
                                                        <option value="q2">Q2</option>
                                                        <option value="q3">Q3</option>
                                                        <option value="q4">Q4</option>
                                                    </select>
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Invoice (Optional)</label>
                                                    <input type="file" name="invoice" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm">
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Client Comment</label>
                                                    <textarea name="client_comment" rows="2" class="w-full text-sm rounded-md border-gray-300 shadow-sm"></textarea>
                                                </div>
                                            </div>

                                            <div class="mt-3 flex gap-2">
                                                <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700">Save</button>
                                                <button type="button" @click="showIncomeForm = false" class="px-3 py-1.5 bg-gray-300 text-gray-700 text-sm rounded hover:bg-gray-400">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Expense Section for This Property -->
                        <div>
                            <h5 class="font-semibold text-gray-800 mb-2">Expenses</h5>
                            
                            @php
                                $propertyExpenses = $property->expenseItems;
                            @endphp

                            @if($propertyExpenses->count() > 0)
                                <div class="overflow-x-auto mb-3">
                                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Inc GST</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">GST</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Net</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quarter</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Receipt</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($propertyExpenses as $expense)
                                                <!-- Normal Row -->
                                                <tr x-show="editingExpenseId !== {{ $expense->id }}">
                                                    <td class="px-3 py-2">{{ $expense->description }}</td>
                                                    <td class="px-3 py-2">${{ number_format($expense->amount_inc_gst, 2) }}</td>
                                                    <td class="px-3 py-2">${{ number_format($expense->gst_amount, 2) }}</td>
                                                    <td class="px-3 py-2">${{ number_format($expense->net_ex_gst, 2) }}</td>
                                                    <td class="px-3 py-2">{{ strtoupper($expense->quarter ?? 'All') }}</td>
                                                    <td class="px-3 py-2">
                                                        @if($expense->hasMedia('receipts'))
                                                            <button @click="$dispatch('open-file-viewer', {url: '{{ route('media.view-expense', $expense) }}', name: '{{ $expense->getFirstMedia('receipts')->file_name }}'})" type="button" class="text-blue-600 hover:underline">View</button>
                                                        @else
                                                            <span class="text-red-600">Missing</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2">
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
                                                    <td colspan="7" class="px-3 py-3">
                                                        <form method="POST" action="{{ route('client.expense.update', $expense) }}" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="rental_property_id" value="{{ $property->id }}">

                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                                                    <input type="text" name="description" value="{{ $expense->description }}" required class="w-full text-sm rounded-md border-gray-300 shadow-sm">
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quarter</label>
                                                                    <select name="quarter" class="w-full text-sm rounded-md border-gray-300 shadow-sm">
                                                                        <option value="all" {{ $expense->quarter === 'all' ? 'selected' : '' }}>All</option>
                                                                        <option value="q1" {{ $expense->quarter === 'q1' ? 'selected' : '' }}>Q1</option>
                                                                        <option value="q2" {{ $expense->quarter === 'q2' ? 'selected' : '' }}>Q2</option>
                                                                        <option value="q3" {{ $expense->quarter === 'q3' ? 'selected' : '' }}>Q3</option>
                                                                        <option value="q4" {{ $expense->quarter === 'q4' ? 'selected' : '' }}>Q4</option>
                                                                    </select>
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (inc GST)</label>
                                                                    <input type="number" step="0.01" name="amount_inc_gst" x-model="amountIncGst" required class="w-full text-sm rounded-md border-gray-300 shadow-sm">
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">GST Amount</label>
                                                                    <input type="number" step="0.01" name="gst_amount" :value="autoCalculateGST ? (amountIncGst - (amountIncGst / 1.1)).toFixed(2) : '{{ $expense->gst_amount }}'" class="w-full text-sm rounded-md border-gray-300 shadow-sm" :readonly="autoCalculateGST">
                                                                </div>

                                                                <div>
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Net (ex GST)</label>
                                                                    <input type="number" step="0.01" name="net_ex_gst" :value="autoCalculateGST ? (amountIncGst / 1.1).toFixed(2) : '{{ $expense->net_ex_gst }}'" class="w-full text-sm rounded-md border-gray-300 shadow-sm" :readonly="autoCalculateGST">
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
                                                                    <input type="file" name="receipt" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm">
                                                                    <p class="text-xs text-gray-500 mt-1">Upload new file to replace existing</p>
                                                                </div>

                                                                <div class="md:col-span-2">
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Client Comment</label>
                                                                    <textarea name="client_comment" rows="2" class="w-full text-sm rounded-md border-gray-300 shadow-sm">{{ $expense->client_comment }}</textarea>
                                                                </div>
                                                            </div>

                                                            <div class="mt-3 flex gap-2">
                                                                <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700">Save Changes</button>
                                                                <button type="button" @click="editingExpenseId = null" class="px-3 py-1.5 bg-gray-300 text-gray-700 text-sm rounded hover:bg-gray-400">Cancel</button>
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
                                    <button 
                                        @click="showExpenseForm = !showExpenseForm; editingExpenseId = null" 
                                        type="button"
                                        class="px-3 py-1.5 bg-red-600 text-white text-sm rounded hover:bg-red-700"
                                    >
                                        + Add Expense
                                    </button>

                                    <div x-show="showExpenseForm" x-cloak class="mt-3 border rounded p-3 bg-white">
                                        <form method="POST" action="{{ route('client.expense.store', $workingPaper) }}" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="section_type" value="rental">
                                            <input type="hidden" name="rental_property_id" value="{{ $property->id }}">

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                                                    <input type="text" name="description" required class="w-full text-sm rounded-md border-gray-300 shadow-sm" placeholder="e.g., Repairs & maintenance">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (inc GST) <span class="text-red-500">*</span></label>
                                                    <input type="number" step="0.01" name="amount_inc_gst" x-model="amountIncGst" required class="w-full text-sm rounded-md border-gray-300 shadow-sm" placeholder="0.00">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quarter</label>
                                                    <select name="quarter" class="w-full text-sm rounded-md border-gray-300 shadow-sm">
                                                        <option value="all">All</option>
                                                        <option value="q1">Q1</option>
                                                        <option value="q2">Q2</option>
                                                        <option value="q3">Q3</option>
                                                        <option value="q4">Q4</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">GST Amount</label>
                                                    <input type="number" step="0.01" name="gst_amount" :value="autoCalculateGST ? (amountIncGst - (amountIncGst / 1.1)).toFixed(2) : ''" class="w-full text-sm rounded-md border-gray-300 shadow-sm" :readonly="autoCalculateGST">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Net (ex GST)</label>
                                                    <input type="number" step="0.01" name="net_ex_gst" :value="autoCalculateGST ? (amountIncGst / 1.1).toFixed(2) : ''" class="w-full text-sm rounded-md border-gray-300 shadow-sm" :readonly="autoCalculateGST">
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="inline-flex items-center">
                                                        <input type="checkbox" x-model="autoCalculateGST" class="rounded border-gray-300">
                                                        <span class="ml-2 text-sm text-gray-700">Auto-calculate GST (10%)</span>
                                                    </label>
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Receipt <span class="text-red-500">*</span></label>
                                                    <input type="file" name="receipt" required accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm">
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Client Comment</label>
                                                    <textarea name="client_comment" rows="2" class="w-full text-sm rounded-md border-gray-300 shadow-sm"></textarea>
                                                </div>
                                            </div>

                                            <div class="mt-3 flex gap-2">
                                                <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700">Save</button>
                                                <button type="button" @click="showExpenseForm = false" class="px-3 py-1.5 bg-gray-300 text-gray-700 text-sm rounded hover:bg-gray-400">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Property Summary -->
                        @if($propertyIncomes->count() > 0 || $propertyExpenses->count() > 0)
                            <div class="mt-4 p-3 bg-blue-100 rounded">
                                <div class="grid grid-cols-3 gap-2 text-sm">
                                    <div>
                                        <p class="text-gray-600">Income:</p>
                                        <p class="font-bold text-green-600">${{ number_format($propertyIncomes->sum('amount'), 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Expenses (Net):</p>
                                        <p class="font-bold text-red-600">${{ number_format($propertyExpenses->sum('net_ex_gst'), 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Net:</p>
                                        <p class="font-bold {{ ($propertyIncomes->sum('amount') - $propertyExpenses->sum('net_ex_gst')) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            ${{ number_format($propertyIncomes->sum('amount') - $propertyExpenses->sum('net_ex_gst'), 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Overall Rental Summary -->
            <div class="mt-6 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg">
                <h5 class="font-bold text-gray-900 mb-3">All Properties Summary</h5>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Total Rental Income:</p>
                        <p class="text-xl font-bold text-green-600">
                            ${{ number_format($workingPaper->rentalProperties->sum(fn($p) => $p->incomeItems->sum('amount')), 2) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Rental Expenses (Net):</p>
                        <p class="text-xl font-bold text-red-600">
                            ${{ number_format($workingPaper->rentalProperties->sum(fn($p) => $p->expenseItems->sum('net_ex_gst')), 2) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Net Rental Income:</p>
                        @php
                            $totalRentalIncome = $workingPaper->rentalProperties->sum(fn($p) => $p->incomeItems->sum('amount'));
                            $totalRentalExpenses = $workingPaper->rentalProperties->sum(fn($p) => $p->expenseItems->sum('net_ex_gst'));
                            $netRental = $totalRentalIncome - $totalRentalExpenses;
                        @endphp
                        <p class="text-xl font-bold {{ $netRental >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ${{ number_format($netRental, 2) }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>