<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [ExcelController::class, 'index'])->name('home');
Route::post('/excel/upload', [ExcelController::class, 'upload'])->name('excel.upload');
Route::delete('/excel/delete/{id}', [ExcelController::class, 'delete'])->name('excel.delete');
