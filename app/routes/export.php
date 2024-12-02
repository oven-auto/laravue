<?php

use App\Http\Controllers\Api\v1\Back\Car\CarExcelController;
use App\Http\Controllers\Api\v1\Back\Trafic\TraficExportController;
use Illuminate\Support\Facades\Route;

Route::prefix('')->middleware(['userfromtoken'])->group(function () {
    Route::get('trafic',   [TraficExportController::class,     'index']);
    Route::get('cars',      [CarExcelController::class,         'index']);
});