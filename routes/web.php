<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\SeserahanController;
use App\Http\Controllers\KuaDocumentController;
use App\Http\Controllers\GuestController;

Route::get('/', fn() => redirect('/dashboard'));

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Checklist
Route::patch('/checklist/reorder', [ChecklistController::class, 'reorder'])->name('checklist.reorder');
Route::resource('checklist', ChecklistController::class)->only(['index','store','update','destroy']);
Route::patch('/checklist/{checklist}/toggle', [ChecklistController::class, 'toggle'])->name('checklist.toggle');

// Budget
Route::patch('/budget/reorder', [BudgetController::class, 'reorder'])->name('budget.reorder');
Route::resource('budget', BudgetController::class)->only(['index','store','update','destroy']);

// Seserahan
Route::patch('/seserahan/reorder', [SeserahanController::class, 'reorder'])->name('seserahan.reorder');
Route::resource('seserahan', SeserahanController::class)->only(['index','store','update','destroy']);
Route::patch('/seserahan/{seserahan}/toggle', [SeserahanController::class, 'toggle'])->name('seserahan.toggle');

// KUA Documents
Route::patch('/dokumen-kua/reorder', [KuaDocumentController::class, 'reorder'])->name('kua.reorder');
Route::resource('dokumen-kua', KuaDocumentController::class)->only(['index','store','update','destroy']);
Route::patch('/dokumen-kua/{kuaDocument}/toggle-cpw', [KuaDocumentController::class, 'toggleCpw'])->name('kua.toggle-cpw');
Route::patch('/dokumen-kua/{kuaDocument}/toggle-cpp', [KuaDocumentController::class, 'toggleCpp'])->name('kua.toggle-cpp');

// Daftar Tamu
Route::patch('/tamu/reorder', [GuestController::class, 'reorder'])->name('tamu.reorder');
Route::resource('tamu', GuestController::class)->only(['index','store','update','destroy']);
