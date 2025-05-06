<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MasterBrandController;
use App\Http\Controllers\MasterCategoryController;
use App\Http\Controllers\MasterItemController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});



Route::middleware(['auth'])->group(function () {


    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth'])->name('dashboard');
    Route::get('/profile',       [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile',      [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/users',         [UserController::class, 'index'])->name('users.index');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/brands', [MasterBrandController::class, 'index'])->name('brands.index');
    Route::get('/brands/create', [MasterBrandController::class, 'create'])->name('brands.create');
    Route::post('/brands', [MasterBrandController::class, 'store'])->name('brands.store');
    Route::get('/brands/{id}/edit', [MasterBrandController::class, 'edit'])->name('brands.edit');
    Route::put('/brands/{id}', [MasterBrandController::class, 'update'])->name('brands.update');
    Route::delete('/brands/{id}', [MasterBrandController::class, 'destroy'])->name('brands.destroy');

    Route::get('/categories', [MasterCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [MasterCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [MasterCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}/edit', [MasterCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{id}', [MasterCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [MasterCategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/items', [MasterItemController::class, 'index'])->name('items.index');
    Route::get('/items/create', [MasterItemController::class, 'create'])->name('items.create');
    Route::post('/items', [MasterItemController::class, 'store'])->name('items.store');
    Route::get('/items/{id}/edit', [MasterItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{id}', [MasterItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{id}', [MasterItemController::class, 'destroy'])->name('items.destroy');

    Route::get('/brands/export/csv',  [MasterBrandController::class, 'exportCsv'])
        ->name('brands.export.csv');
    Route::get('/brands/export/xlsx', [MasterBrandController::class, 'exportXlsx'])
        ->name('brands.export.xlsx');
    Route::get('/brands/export/pdf',  [MasterBrandController::class, 'exportPdf'])
        ->name('brands.export.pdf');

    Route::get('/categories/export/csv',  [MasterCategoryController::class, 'exportCsv'])
        ->name('categories.export.csv');
    Route::get('/categories/export/xlsx', [MasterCategoryController::class, 'exportXlsx'])
        ->name('categories.export.xlsx');
    Route::get('/categories/export/pdf',  [MasterCategoryController::class, 'exportPdf'])
        ->name('categories.export.pdf');

    Route::get('/items/export/csv',   [MasterItemController::class, 'exportCsv'])->name('items.export.csv');
    Route::get('/items/export/xlsx',  [MasterItemController::class, 'exportXlsx'])->name('items.export.xlsx');
    Route::get('/items/export/pdf',   [MasterItemController::class, 'exportPdf'])->name('items.export.pdf');
});

require __DIR__ . '/auth.php';
