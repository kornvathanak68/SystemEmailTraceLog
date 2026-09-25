<?php

use App\Http\Controllers\ReportRunController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/upload');

Route::get('/upload', [ReportRunController::class, 'uploadForm'])->name('runs.upload.form');
Route::post('/upload', [ReportRunController::class, 'upload'])->name('runs.upload');
