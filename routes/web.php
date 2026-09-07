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
    return inertia('Welcome');
})->name('home');

Route::get('/budget-estimator', function () {
    return inertia('BudgetEstimator');
})->name('budget-estimator');

Route::get('/onboarding-preview', function () {
    return inertia('Onboarding/Index', ['preview' => true]);
})->name('onboarding.preview');

// Autentikasi rute-rute menggunakan middleware auth
Route::middleware(['auth'])->group(function () {
    
    // Onboarding Routes
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

    // Main App Routes (membutuhkan data Wedding)
    Route::middleware([\App\Http\Middleware\EnsureUserHasWedding::class])->group(function () {
        Route::get('/ringkasan', [DashboardController::class, 'index'])->name('dashboard');

        // Budgets
        Route::get('/anggaran', [BudgetController::class, 'index'])->name('budget.index');
        Route::post('/anggaran', [BudgetController::class, 'store'])->name('budget.store');
        Route::patch('/anggaran/reorder', [BudgetController::class, 'reorder'])->name('budget.reorder');
        Route::patch('/anggaran/{budget}', [BudgetController::class, 'update'])->name('budget.update');
        Route::delete('/anggaran/{budget}', [BudgetController::class, 'destroy'])->name('budget.destroy');

        // Checklists
        Route::get('/daftar-persiapan', [ChecklistController::class, 'index'])->name('checklist.index');
        Route::post('/daftar-persiapan', [ChecklistController::class, 'store'])->name('checklist.store');
        Route::patch('/daftar-persiapan/reorder', [ChecklistController::class, 'reorder'])->name('checklist.reorder');
        Route::patch('/daftar-persiapan/{checklist}', [ChecklistController::class, 'update'])->name('checklist.update');
        Route::delete('/daftar-persiapan/{checklist}', [ChecklistController::class, 'destroy'])->name('checklist.destroy');
        Route::patch('/daftar-persiapan/{checklist}/toggle', [ChecklistController::class, 'toggle'])->name('checklist.toggle');

        // Seserahan
        Route::get('/seserahan', [SeserahanController::class, 'index'])->name('seserahan.index');
        Route::post('/seserahan', [SeserahanController::class, 'store'])->name('seserahan.store');
        Route::patch('/seserahan/reorder', [SeserahanController::class, 'reorder'])->name('seserahan.reorder');
        Route::patch('/seserahan/{seserahan}', [SeserahanController::class, 'update'])->name('seserahan.update');
        Route::delete('/seserahan/{seserahan}', [SeserahanController::class, 'destroy'])->name('seserahan.destroy');
        Route::patch('/seserahan/{seserahan}/toggle', [SeserahanController::class, 'toggle'])->name('seserahan.toggle');

        // KUA
        Route::get('/dokumen-kua', [KuaDocumentController::class, 'index'])->name('dokumen-kua.index');
        Route::post('/dokumen-kua', [KuaDocumentController::class, 'store'])->name('dokumen-kua.store');
        Route::patch('/dokumen-kua/reorder', [KuaDocumentController::class, 'reorder'])->name('dokumen-kua.reorder');
        Route::patch('/dokumen-kua/{dokumen_kua}', [KuaDocumentController::class, 'update'])->name('dokumen-kua.update');
        Route::delete('/dokumen-kua/{dokumen_kua}', [KuaDocumentController::class, 'destroy'])->name('dokumen-kua.destroy');
        Route::patch('/dokumen-kua/{dokumen_kua}/status-cpw', [KuaDocumentController::class, 'toggleCpw'])->name('kua.toggle-cpw');
        Route::patch('/dokumen-kua/{dokumen_kua}/status-cpp', [KuaDocumentController::class, 'toggleCpp'])->name('kua.toggle-cpp');

        // Guests
        Route::get('/daftar-undangan', [GuestController::class, 'index'])->name('tamu.index');
        Route::post('/daftar-undangan', [GuestController::class, 'store'])->name('tamu.store');
        Route::patch('/daftar-undangan/reorder', [GuestController::class, 'reorder'])->name('tamu.reorder');
        Route::patch('/daftar-undangan/{tamu}', [GuestController::class, 'update'])->name('tamu.update');
        Route::delete('/daftar-undangan/{tamu}', [GuestController::class, 'destroy'])->name('tamu.destroy');
    });

    Route::get('/paket-langganan', function () {
        return inertia('Subscription/Index');
    })->name('subscription.index');

    Route::get('/pusat-bantuan', function () {
        return inertia('Help/Index');
    })->name('help.index');

    Route::get('/hubungi-dukungan', function () {
        return inertia('Support/Index');
    })->name('support.index');

    // Profile Routes
    Route::get('/pengaturan', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/pengaturan', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/pengaturan/pernikahan', [ProfileController::class, 'updateWedding'])->name('profile.wedding.update');
    Route::delete('/pengaturan', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
