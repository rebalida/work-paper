<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Working Paper</title>
    <style>
        body { font-family: DM sans, sans-serif; font-size: 12px; }
        h1, h2, h3 { margin: 0; padding: 0; }
        h1 { font-size: 20px; margin-bottom: 10px; }
        h2 { font-size: 16px; margin-top: 15px; }
        h3 { font-size: 14px; margin-top: 10px; }
        table { border-radius: 8px; width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 0.5px solid rgba(0, 0, 0, 0.75); padding: 5px; text-align: left; }
        th { background-color: #ffffff; }
        .section { margin-bottom: 15px; }
        .section-type { font-weight: bold; margin-bottom: 5px; }
        .total { font-weight: bold; }
        .full-column-header {
            background: #6D7AE0;
        }
        .full-column-header-faded {
            background-color:#6d7ae04d;
        }
    </style>
</head>
<body>

<h1 style="text-align:center;">Working Paper</h1>

<div class="section">
    <p><strong>Client:</strong> {{ $workingPaper->user->name ?? 'N/A' }}</p>
    <p><strong>Financial Year:</strong> {{ $workingPaper->financial_year }}</p>
    <p><strong>Status:</strong> {{ $workingPaper->status_label }}</p>
    <p><strong>Submitted At:</strong> {{ $workingPaper->submitted_at?->format('d M Y H:i') ?? '-' }}</p>
    <p><strong>Reviewed By:</strong> {{ $workingPaper->reviewer->name ?? '-' }}</p>
    
</div>


{{-- Wage Data --}}
@if($workingPaper->hasType('wage') && $workingPaper->wageData)
<div class="section" style="margin-bottom:2.5rem;">
    <table>
        <tr>
            <th class ="full-column-header" colspan="2" style="padding:12px 0px;text-align:center;font-size:14px;color:white;">
                Wage Data
            </th>
        </tr>
        <tr>
            <th class="full-column-header-faded">Salary</th>
            <td>{{ $workingPaper->wageData->salary_wages }}</td>
        </tr>
        <tr>
            <th class="full-column-header-faded">Tax Withheld</th>
            <td>{{ $workingPaper->wageData->tax_withheld}}</td>
        </tr>
        <tr>
            <th class="full-column-header-faded">Other Employment Items</th>
            <td>{{ $workingPaper->wageData->other_employment_items}}</td>
        </tr>
        <tr>
        </tr>
    </table>
</div>
@endif

{{-- Rental Properties --}}
@if($workingPaper->hasType('rental') && $workingPaper->rentalProperties->count())
<div class="section" style="margin-bottom:2.5rem;">
<table width="100%">

{{-- TITLE --}}
<tr>
    <th class="full-column-header" colspan="7" style="text-align:center;color:white;">
        Rental Properties
    </th>
</tr>

{{-- PROPERTY HEADER --}}
<tr>
    <th>Address</th>
    <th>Ownership %</th>
    <th colspan="2">Period</th>
    <th colspan="3">Client Comment</th>
</tr>

@foreach($workingPaper->rentalProperties as $property)
<tr>
    <td>{{ $property->address_label }}</td>
    <td>{{ number_format($property->ownership_percentage,2) }}</td>
    <td colspan="2">{{ $property->period_rented }}</td>
    <td colspan="3">{{ $property->client_comment }}</td>
</tr>
@endforeach

{{-- EXPENSE TITLE --}}
<tr>
    <th class="full-column-header" colspan="7" style="color:white;">
        Expenses
    </th>
</tr>

<tr>
    <th>Type</th>
    <th>Description</th>
    <th>Inc GST</th>
    <th>GST</th>
    <th>Net</th>
    <th>Quarter</th>
    <th>Comment</th>
</tr>
@php
        $rentalExpenses = $workingPaper->expenseItems->where('section_type', 'rental');
    @endphp
@foreach($rentalExpenses as $expense)
<tr>
    <td>{{ $expense->field_type }}</td>
    <td>{{ $expense->description }}</td>
    <td>{{ number_format($expense->amount_inc_gst,2) }}</td>
    <td>{{ number_format($expense->gst_amount,2) }}</td>
    <td>{{ number_format($expense->net_ex_gst,2) }}</td>
    <td>{{ $expense->quarter }}</td>
    <td>{{ $expense->client_comment }}</td>
</tr>
@endforeach

</table>

</div>

@endif


@php
    // Group income and expense items by section_type
    $incomeByType = $workingPaper->incomeItems->groupBy('section_type');
    $expenseByType = $workingPaper->expenseItems->groupBy('section_type');

    // Get all unique section types from income and expense items
    $allSectionTypes = $incomeByType->keys()->merge($expenseByType->keys())->unique();


@endphp

@php
$labels = [
    'sole_trader' => 'Sole Trader',
    'bas' => 'Business Activity Statement',
    'ctax' => 'Company Tax',
    'ttax' => 'Trust Tax',
    'smsf' => 'Self-Managed Super Fund',
];
@endphp


@foreach($allSectionTypes as $type)

@php
$title = $labels[$type] ?? ucfirst($type);
@endphp

    {{-- Skip 'rental' because we already printed it above --}}
    @if($type === 'rental') 
        @continue
    @endif

    <div class="section"  style="margin-bottom:2.5rem;">

<table width="100%">

<tr>
    <th class="full-column-header" colspan="7" style="color:white;">
        {{ $title }}
    </th>
</tr>

{{-- INCOME --}}
@if(isset($incomeByType[$type]))

<tr>
    <th colspan="7">Income</th>
</tr>

<tr>
    <th>Description</th>
    <th>Amount</th>
    <th>Quarter</th>
    <th colspan="4">Comment</th>
</tr>

@foreach($incomeByType[$type] as $income)
<tr>
    <td>{{ $income->description }}</td>
    <td>{{ number_format($income->amount,2) }}</td>
    <td>{{ $income->quarter }}</td>
    <td colspan="4">{{ $income->client_comment }}</td>
</tr>
@endforeach

@endif


{{-- EXPENSES --}}
@if(isset($expenseByType[$type]))

<tr>
    <th colspan="7">Expenses</th>
</tr>

<tr>
    <th>Field</th>
    <th>Description</th>
    <th>Inc GST</th>
    <th>GST</th>
    <th>Net</th>
    <th>Quarter</th>
    <th>Comment</th>
</tr>

@foreach($expenseByType[$type] as $expense)
<tr>
    <td>{{ $expense->field_type }}</td>
    <td>{{ $expense->description }}</td>
    <td>{{ number_format($expense->amount_inc_gst,2) }}</td>
    <td>{{ number_format($expense->gst_amount,2) }}</td>
    <td>{{ number_format($expense->net_ex_gst,2) }}</td>
    <td>{{ $expense->quarter }}</td>
    <td>{{ $expense->client_comment }}</td>
</tr>
@endforeach

@endif

</table>


</div>

@endforeach



</body>
</html>
