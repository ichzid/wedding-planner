<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\KuaDocumentController;
use App\Http\Controllers\SeserahanController;
use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Autentikasi rute-rute menggunakan middleware auth
Route::middleware(['auth'])->group(function () {
    
    // Onboarding Routes
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

    // Main App Routes (membutuhkan data Wedding)
    Route::middleware([\App\Http\Middleware\EnsureUserHasWedding::class])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Checklists
        Route::get('/checklist', [ChecklistController::class, 'index'])->name('checklist.index');
        Route::post('/checklist', [ChecklistController::class, 'store'])->name('checklist.store');
        Route::patch('/checklist/reorder', [ChecklistController::class, 'reorder'])->name('checklist.reorder');
        Route::patch('/checklist/{id}/toggle', [ChecklistController::class, 'toggleStatus'])->name('checklist.toggle');

        // Budgets
        Route::get('/budget', [BudgetController::class, 'index'])->name('budget.index');
        Route::post('/budget', [BudgetController::class, 'store'])->name('budget.store');
        Route::patch('/budget/reorder', [BudgetController::class, 'reorder'])->name('budget.reorder');
        Route::put('/budget/{id}', [BudgetController::class, 'update'])->name('budget.update');

        // Seserahan
        Route::get('/seserahan', [SeserahanController::class, 'index'])->name('seserahan.index');
        Route::post('/seserahan', [SeserahanController::class, 'store'])->name('seserahan.store');
        Route::patch('/seserahan/reorder', [SeserahanController::class, 'reorder'])->name('seserahan.reorder');

        // KUA
        Route::get('/dokumen-kua', [KuaDocumentController::class, 'index'])->name('dokumen-kua.index');
        Route::post('/dokumen-kua', [KuaDocumentController::class, 'store'])->name('dokumen-kua.store');
        Route::patch('/dokumen-kua/reorder', [KuaDocumentController::class, 'reorder'])->name('dokumen-kua.reorder');
        Route::patch('/dokumen-kua/{id}/toggle-cpw', [KuaDocumentController::class, 'toggleCpw'])->name('dokumen-kua.toggle-cpw');
        Route::patch('/dokumen-kua/{id}/toggle-cpp', [KuaDocumentController::class, 'toggleCpp'])->name('dokumen-kua.toggle-cpp');

        // Guests
        Route::get('/tamu', [GuestController::class, 'index'])->name('tamu.index');
        Route::post('/tamu', [GuestController::class, 'store'])->name('tamu.store');
        Route::patch('/tamu/reorder', [GuestController::class, 'reorder'])->name('tamu.reorder');
    });

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/wedding', [ProfileController::class, 'updateWedding'])->name('profile.wedding.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
