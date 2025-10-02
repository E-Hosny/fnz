<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelController;

Auth::routes(['register' => false]);

Route::get('/', [ExcelController::class, 'index'])->name('home')->middleware('auth');

Route::post('/excel/upload', [ExcelController::class, 'upload'])->name('excel.upload')->middleware('auth');
Route::delete('/excel/delete/{id}', [ExcelController::class, 'delete'])->name('excel.delete')->middleware('auth');
