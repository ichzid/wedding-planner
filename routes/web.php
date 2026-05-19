<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\SeserahanController;
use App\Http\Controllers\KuaDocumentController;

Route::get('/', fn() => redirect('/dashboard'));

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Checklist
Route::resource('checklist', ChecklistController::class)->only(['index','store','update','destroy']);
Route::patch('/checklist/{checklist}/toggle', [ChecklistController::class, 'toggle'])->name('checklist.toggle');

// Budget
Route::resource('budget', BudgetController::class)->only(['index','store','update','destroy']);

// Seserahan
Route::resource('seserahan', SeserahanController::class)->only(['index','store','update','destroy']);
Route::patch('/seserahan/{seserahan}/toggle', [SeserahanController::class, 'toggle'])->name('seserahan.toggle');

// KUA Documents
Route::resource('dokumen-kua', KuaDocumentController::class)->only(['index','store','update','destroy']);
Route::patch('/dokumen-kua/{kuaDocument}/toggle-cpw', [KuaDocumentController::class, 'toggleCpw'])->name('kua.toggle-cpw');
Route::patch('/dokumen-kua/{kuaDocument}/toggle-cpp', [KuaDocumentController::class, 'toggleCpp'])->name('kua.toggle-cpp');
