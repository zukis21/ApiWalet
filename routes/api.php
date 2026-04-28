<?php

use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('wallet')
    ->name('wallet.')
    ->group(function () {
        Route::get('/inquiry/{memberId}', [WalletController::class, 'inquiry'])
            ->name('inquiry');

        Route::post('/deposit', [WalletController::class, 'deposit'])
            ->name('deposit');

        Route::post('/withdraw', [WalletController::class, 'withdraw'])
            ->name('withdraw');
    });
