<?php

use App\Http\Controllers\PGPController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailController;


Route::post('/send-email', [EmailController::class, 'sendEmail'])->name('send-email');
Route::get('/', [EmailController::class, 'index'])->name('emailForm');
Route::get('/oauth2callback', [EmailController::class, 'oauthCallback'])->name('oauthCallback');

Route::get('/generate-pgp-keys', [PGPController::class, 'generateKeys']);
