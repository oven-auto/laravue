<?php

use App\Http\Controllers\Api\v1\Back\Car\CarExcelController;
use App\Http\Controllers\Api\v1\Back\Trafic\TraficExportController;
use App\Http\Controllers\Api\v1\Back\Worksheet\Modules\Reserve\ReserveExcelController;
use Illuminate\Support\Facades\Route;

Route::prefix('')->middleware(['userfromtoken'])->group(function () {
    Route::get('trafic',   [TraficExportController::class,     'index']);
    Route::get('cars',      [CarExcelController::class,         'index']);
    Route::get('reserves', [ReserveExcelController::class, 'index']);
});