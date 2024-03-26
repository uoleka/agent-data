<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

Route::get('/', function () {
    return view('upload');
});

Route::get('/upload', function () {
    return view('upload');
});

Route::post('/upload', [FileController::class, 'uploadFile'])->name('upload.file');

