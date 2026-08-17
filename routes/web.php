<?php

use ComposerRumus\Http\Controllers\InvoiceCashReportController;
use ComposerRumus\Http\Controllers\InvoiceReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('composer-rumus.middleware'))
    ->prefix(config('composer-rumus.route_prefix'))
    ->as('composer-rumus.')
    ->group(function (): void {
        Route::get('/invoice', [InvoiceReportController::class, 'index'])->name('invoice.index');
        Route::get('/invoice/search', [InvoiceReportController::class, 'search'])->name('invoice.search');
        Route::get('/invoice-cash', [InvoiceCashReportController::class, 'index'])->name('cash.index');
        Route::get('/invoice-cash/search', [InvoiceCashReportController::class, 'search'])->name('cash.search');
        Route::get('/invoice-cash/expenses/{method}', [InvoiceCashReportController::class, 'expenseDetail'])
            ->name('cash.expenses');
    });
