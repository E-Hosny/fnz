<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelController;

Route::get('/', [ExcelController::class, 'index'])->name('home');

Auth::routes();

Route::post('/excel/upload', [ExcelController::class, 'upload'])->name('excel.upload');
Route::delete('/excel/delete/{id}', [ExcelController::class, 'delete'])->name('excel.delete');
