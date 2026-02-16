<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\WorkingPaper;
use App\Models\ExpenseItem;
use App\Models\IncomeItem;
use App\Models\RentalProperty;
use App\Models\WageData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class WorkingPaperDashboard extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display the client dashboard with type selector and sections
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $selectedYear = $request->query('year');
        
        if ($selectedYear) {
            if (!preg_match('/^\d{4}-\d{4}$/', $selectedYear)) {
                return redirect()->route('client.dashboard')->withErrors(['year' => 'Invalid financial year format']);
            }
            $financialYear = $selectedYear;
        } else {
            $financialYear = $this->getCurrentFinancialYear();
        }
        
        $workingPaper = WorkingPaper::firstOrCreate(
            [
                'user_id' => $user->id,
                'financial_year' => $financialYear,
            ],
            [
                'selected_types' => [],
                'status' => 'draft',
            ]
        );

        // Load all related data
        $workingPaper->load([
            'wageData',
            'rentalProperties.incomeItems',
            'rentalProperties.expenseItems',
            'incomeItems',
            'expenseItems',
            'reviewer',
        ]);

        return view('client.dashboard', [
            'workingPaper' => $workingPaper,
            'availableTypes' => WorkingPaper::getAvailableTypes(),
            'financialYears' => $this->getFinancialYears(),
        ]);
    }

    /**
     * Update selected work types
     */
    public function updateTypes(Request $request, WorkingPaper $workingPaper)
    {
        $this->authorize('update', $workingPaper);

        $validated = $request->validate([
            'selected_types' => 'required|array',
            'selected_types.*' => 'in:wage,rental,sole_trader,bas,ctax,ttax,smsf',
        ]);

        $workingPaper->update([
            'selected_types' => $validated['selected_types'],
        ]);

        return redirect()->route('client.dashboard', ['year' => $workingPaper->financial_year])->with('success', 'Work types updated successfully');
    }

    /**
     * Save wage data
     */
    public function saveWageData(Request $request, WorkingPaper $workingPaper)
    {
        $this->authorize('update', $workingPaper);

        $validated = $request->validate([
            'salary_wages' => 'nullable|numeric|min:0',
            'tax_withheld' => 'nullable|numeric|min:0',
            'other_employment_items' => 'nullable|string',
            'payg_summary' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $wageData = $workingPaper->wageData()->firstOrCreate([]);
        $wageData->update($validated);

        if ($request->hasFile('payg_summary')) {
            $wageData->clearMediaCollection('payg_summary');
            $wageData->addMedia($request->file('payg_summary'))->toMediaCollection('payg_summary');
        }

        return back()->with('success', 'Wage data saved successfully');
    }

    /**
     * Add rental property
     */
    public function addRentalProperty(Request $request, WorkingPaper $workingPaper)
    {
        $this->authorize('update', $workingPaper);

        $validated = $request->validate([
            'address_label' => 'required|string|max:255',
            'ownership_percentage' => 'nullable|numeric|min:0|max:100',
            'period_rented' => 'nullable|string|max:255',
        ]);

        $property = $workingPaper->rentalProperties()->create($validated);

        return back()->with('success', 'Rental property added successfully');
    }

    /**
     * Delete rental property
     */
    public function deleteRentalProperty(RentalProperty $rentalProperty) 
    {
        $this->authorize('update', $rentalProperty->workingPaper);

        $rentalProperty->delete();

        return back()->with('success', 'Rental property deleted successfully');
    }

    /**
     * Add income item
     */
    public function addIncome(Request $request, WorkingPaper $workingPaper)
    {
        $this->authorize('update', $workingPaper);

        $validated = $request->validate([
            'rental_property_id' => 'nullable|exists:rental_properties,id',
            'section_type' => 'required|in:rental,sole_trader,bas,ctax,ttax,smsf',
            'income_type' => 'nullable|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'quarter' => 'nullable|in:all,q1,q2,q3,q4',
            'client_comment' => 'nullable|string',
            'own_comment' => 'nullable|string',
            'invoice' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $income = $workingPaper->incomeItems()->create($validated);

        if ($request->hasFile('invoice')) {
            $income->addMedia($request->file('invoice'))->toMediaCollection('invoices');
        }

        return back()->with('success', 'Income item added successfully');
    }

    /**
     * Update income item
     */
    public function updateIncome(Request $request, IncomeItem $income)
    {
        $this->authorize('update', $income->workingPaper);

        $validated = $request->validate([
            'income_type' => 'nullable|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'quarter' => 'nullable|in:all,q1,q2,q3,q4',
            'client_comment' => 'nullable|string',
            'invoice' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $income->update($validated);

        if ($request->hasFile('invoice')) {
            $income->clearMediaCollection('invoices');
            $income->addMedia($request->file('invoice'))->toMediaCollection('invoices');
        }

        return back()->with('success', 'Income item updated successfully');
    }

    /**
     * Delete income item
     */
    public function deleteIncome(IncomeItem $income)
    {
        $this->authorize('update', $income->workingPaper);

        $income->delete();

        return back()->with('success', 'Income item deleted successfully');
    }

    /**
     * Add expense item
     */
    public function addExpense(Request $request, WorkingPaper $workingPaper)
    {
        $this->authorize('update', $workingPaper);

        $validated = $request->validate([
            'rental_property_id' => 'nullable|exists:rental_properties,id',
            'section_type' => 'required|in:wage,rental,sole_trader,bas,ctax,ttax,smsf',
            'field_type' => 'nullable|in:a,b,c',
            'description' => 'required|string',
            'amount_inc_gst' => 'required|numeric|min:0',
            'gst_amount' => 'nullable|numeric|min:0',
            'net_ex_gst' => 'nullable|numeric|min:0',
            'quarter' => 'nullable|in:all,q1,q2,q3,q4',
            'client_comment' => 'nullable|string',
            'own_comment' => 'nullable|string',
            'receipt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Auto-calculate GST if not provided
        if (empty($validated['gst_amount']) || empty($validated['net_ex_gst'])) {
            $validated['net_ex_gst'] = round($validated['amount_inc_gst'] / 1.1, 2);
            $validated['gst_amount'] = round($validated['amount_inc_gst'] - $validated['net_ex_gst'], 2);
        }

        $expense = $workingPaper->expenseItems()->create($validated);

        // Validate GST
        if (!$expense->validateGst()) {
            $expense->delete();
            return back()->withErrors(['gst' => 'GST calculation is invalid. Please check the amounts.']);
        }

        if ($request->hasFile('receipt')) {
            $expense->addMedia($request->file('receipt'))->toMediaCollection('receipts');
        }

        return back()->with('success', 'Expense item added successfully');
    }

    /**
     * Update expense item
     */
    public function updateExpense(Request $request, ExpenseItem $expense)
    {
        $this->authorize('update', $expense->workingPaper);

        $validated = $request->validate([
            'field_type' => 'nullable|in:a,b,c',
            'description' => 'required|string',
            'amount_inc_gst' => 'required|numeric|min:0',
            'gst_amount' => 'nullable|numeric|min:0',
            'net_ex_gst' => 'nullable|numeric|min:0',
            'quarter' => 'nullable|in:all,q1,q2,q3,q4',
            'client_comment' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Auto-calculate GST if not provided
        if (empty($validated['gst_amount']) || empty($validated['net_ex_gst'])) {
            $validated['net_ex_gst'] = round($validated['amount_inc_gst'] / 1.1, 2);
            $validated['gst_amount'] = round($validated['amount_inc_gst'] - $validated['net_ex_gst'], 2);
        }

        $expense->update($validated);

        // Validate GST
        if (!$expense->validateGst()) {
            return back()->withErrors(['gst' => 'GST calculation is invalid. Please check the amounts.']);
        }

        if ($request->hasFile('receipt')) {
            $expense->clearMediaCollection('receipts');
            $expense->addMedia($request->file('receipt'))->toMediaCollection('receipts');
        }

        return back()->with('success', 'Expense item updated successfully');
    }

    /**
     * Delete expense item
     */
    public function deleteExpense(ExpenseItem $expense)
    {
        $this->authorize('update', $expense->workingPaper);

        $expense->delete();

        return back()->with('success', 'Expense item deleted successfully');
    }

    /**
     * Submit working paper
     */
    public function submit(WorkingPaper $workingPaper)
    {
        $this->authorize('submit', $workingPaper);

        // Validation: Check all expenses have receipts
        $expensesWithoutReceipts = $workingPaper->expenseItems()->whereDoesntHave('media')->count();

        if ($expensesWithoutReceipts > 0) {
            return back()->withErrors(['submit' => "Please upload receipts for all {$expensesWithoutReceipts} expense(s) before submitting."]);
        }

        // Determine new status based on current status
        $newStatus = $workingPaper->status === 'rejected' ? 'resubmitted' : 'submitted';

        $workingPaper->update([
            'status' => $newStatus,
            'submitted_at' => now(),
        ]);

        $message = $newStatus === 'resubmitted' 
            ? 'Working paper resubmitted successfully! It will be reviewed by an admin.'
            : 'Working paper submitted successfully! It will be reviewed by an admin.';

        return back()->with('success', $message);
    }

    /**
     * Get current financial year (July to June)
     */
    private function getCurrentFinancialYear(): string
    {
        $now = now();
        $year = $now->year;
        $month = $now->month;

        if ($month >= 7) {
            return $year . '-' . ($year + 1);
        } else {
            return ($year - 1) . '-' . $year;
        }
    }

    /**
     * Get list of financial years
     */
    private function getFinancialYears(): array
    {
        $currentYear = now()->year;
        $years = [];

        for ($i = 0; $i < 5; $i++) {
            $year = $currentYear - $i;
            $years[] = $year . '-' . ($year + 1);
        }

        return $years;
    }
}