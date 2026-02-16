<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Client\WorkingPaperDashboard;
use App\Http\Controllers\Admin\AdminDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Redirect to appropriate dashboard based on role
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('client.dashboard');
    })->name('dashboard');

    // View media files
    Route::get('/view-expense-media/{expense}', function (\App\Models\ExpenseItem $expense) {
        if (!$expense->hasMedia('receipts')) {
            abort(404);
        }
        
        $media = $expense->getFirstMedia('receipts');
        return response()->file($media->getPath(), [
            'Content-Type' => $media->mime_type,
            'Content-Disposition' => 'inline; filename="' . $media->file_name . '"'
        ]);
    })->name('media.view-expense');
    
    Route::get('/view-income-media/{income}', function (\App\Models\IncomeItem $income) {
        if (!$income->hasMedia('invoices')) {
            abort(404);
        }
        
        $media = $income->getFirstMedia('invoices');
        return response()->file($media->getPath(), [
            'Content-Type' => $media->mime_type,
            'Content-Disposition' => 'inline; filename="' . $media->file_name . '"'
        ]);
    })->name('media.view-income');
    
    Route::get('/view-wage-media/{wageData}', function (\App\Models\WageData $wageData) {
        if (!$wageData->hasMedia('payg_summary')) {
            abort(404);
        }
        
        $media = $wageData->getFirstMedia('payg_summary');
        return response()->file($media->getPath(), [
            'Content-Type' => $media->mime_type,
            'Content-Disposition' => 'inline; filename="' . $media->file_name . '"'
        ]);
    })->name('media.view-wage');

    // Client Routes
    Route::prefix('client')->name('client.')->group(function () {
        // Client dashboard
        Route::get('/dashboard', [WorkingPaperDashboard::class, 'index'])->name('dashboard');
        
        // Update selected work types
        Route::patch('/working-paper/{workingPaper}/types', [WorkingPaperDashboard::class, 'updateTypes'])
            ->name('working-paper.update-types');
        
        // Wage data
        Route::post('/working-paper/{workingPaper}/wage', [WorkingPaperDashboard::class, 'saveWageData'])
            ->name('wage.save');
        
        // Rental Property management
        Route::post('/working-paper/{workingPaper}/rental-property', [WorkingPaperDashboard::class, 'addRentalProperty'])
            ->name('rental-property.store');
        Route::delete('/rental-property/{rentalProperty}', [WorkingPaperDashboard::class, 'deleteRentalProperty'])
            ->name('rental-property.destroy');
        
        // Income items
        Route::post('/working-paper/{workingPaper}/income', [WorkingPaperDashboard::class, 'addIncome'])
            ->name('income.store');
        Route::patch('/income/{income}', [WorkingPaperDashboard::class, 'updateIncome'])
            ->name('income.update');
        Route::delete('/income/{income}', [WorkingPaperDashboard::class, 'deleteIncome'])
            ->name('income.destroy');
        
        // Expense items
        Route::post('/working-paper/{workingPaper}/expense', [WorkingPaperDashboard::class, 'addExpense'])
            ->name('expense.store');
        Route::patch('/expense/{expense}', [WorkingPaperDashboard::class, 'updateExpense'])
            ->name('expense.update');
        Route::delete('/expense/{expense}', [WorkingPaperDashboard::class, 'deleteExpense'])
            ->name('expense.destroy');
        
        // Submit working paper
        Route::post('/working-paper/{workingPaper}/submit', [WorkingPaperDashboard::class, 'submit'])
            ->name('working-paper.submit');

        // Export Working Paper as PDF
        Route::get('/working-paper/{workingPaper}/export-pdf', [WorkingPaperDashboard::class, 'exportPdf'])
        ->name('working-paper.export-pdf');    
    });

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        // Admin dashboard - list all working papers
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
        
        // View specific working paper for review
        Route::get('/working-paper/{workingPaper}', [AdminDashboard::class, 'show'])->name('working-paper.show');
        
        // Approve working paper
        Route::post('/working-paper/{workingPaper}/approve', [AdminDashboard::class, 'approve'])->name('working-paper.approve');
        
        // Reject working paper
        Route::post('/working-paper/{workingPaper}/reject', [AdminDashboard::class, 'reject'])->name('working-paper.reject');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';