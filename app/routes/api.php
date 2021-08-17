<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::resource('brands', \App\Http\Controllers\Api\v1\BrandController::class);

Route::resource('devicetypes', \App\Http\Controllers\Api\v1\DeviceTypeController::class);

Route::resource('devicefilters', \App\Http\Controllers\Api\v1\DeviceFilterController::class);

Route::resource('devices', \App\Http\Controllers\Api\v1\DeviceController::class);

Route::resource('properties', \App\Http\Controllers\Api\v1\PropertyController::class);

Route::resource('motortypes', \App\Http\Controllers\Api\v1\MotorTypeController::class);

Route::resource('motortransmissions', \App\Http\Controllers\Api\v1\MotorTransmissionController::class);

Route::resource('motordrivers', \App\Http\Controllers\Api\v1\MotorDriverController::class);

Route::resource('motors', \App\Http\Controllers\Api\v1\MotorController::class);

Route::resource('colors', \App\Http\Controllers\Api\v1\ColorController::class);

Route::resource('packs', \App\Http\Controllers\Api\v1\PackController::class);

Route::resource('bodyworks', \App\Http\Controllers\Api\v1\BodyWorkController::class);

Route::resource('countryfactories', \App\Http\Controllers\Api\v1\CountryFactoryController::class);

Route::resource('marks', \App\Http\Controllers\Api\v1\MarkController::class);

Route::resource('complectations', \App\Http\Controllers\Api\v1\ComplectController::class);
Route::get('markcolors', [\App\Http\Controllers\Api\v1\MarkColorController::class, 'index']);
Route::get('complectcolors', [\App\Http\Controllers\Api\v1\ComplectColorController::class, 'colorpack']);

Route::resource('cars', \App\Http\Controllers\Api\v1\CarController::class);
