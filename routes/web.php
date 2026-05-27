<?php

use App\Http\Controllers\WallSyncController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WallSyncController::class, 'index'])->name('home');
Route::get('/api/auth-check',     [WallSyncController::class, 'checkAuth'])->name('api.auth-check');
Route::post('/api/setup',         [WallSyncController::class, 'setup'])->name('api.setup');
Route::post('/api/logout',        [WallSyncController::class, 'logout'])->name('api.logout');
Route::post('/api/spend',         [WallSyncController::class, 'spend'])->name('api.spend');
Route::post('/api/income',        [WallSyncController::class, 'income'])->name('api.income');
Route::get('/api/history',        [WallSyncController::class, 'historyData'])->name('api.history');
Route::delete('/api/transaction/{id}', [WallSyncController::class, 'deleteTransaction'])->name('api.transaction.delete');
Route::get('/api/chart/category', [WallSyncController::class, 'chartCategory'])->name('api.chart.category');
Route::get('/api/chart/cashflow', [WallSyncController::class, 'chartCashflow'])->name('api.chart.cashflow');
Route::get('/api/chart/wallets',  [WallSyncController::class, 'chartWallets'])->name('api.chart.wallets');
