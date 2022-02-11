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
Route::get('transmissions/type', [\App\Http\Controllers\Api\v1\MotorTransmissionController::class, 'getTypes']);

Route::resource('motordrivers', \App\Http\Controllers\Api\v1\MotorDriverController::class);
Route::get('drivers/type', [\App\Http\Controllers\Api\v1\MotorDriverController::class, 'getTypes']);

Route::resource('motors', \App\Http\Controllers\Api\v1\MotorController::class);

Route::resource('colors', \App\Http\Controllers\Api\v1\ColorController::class);

Route::resource('packs', \App\Http\Controllers\Api\v1\PackController::class);

Route::resource('bodyworks', \App\Http\Controllers\Api\v1\BodyWorkController::class);

Route::resource('countryfactories', \App\Http\Controllers\Api\v1\CountryFactoryController::class);

Route::resource('marks', \App\Http\Controllers\Api\v1\MarkController::class);

Route::resource('complectations', \App\Http\Controllers\Api\v1\ComplectController::class);
Route::get('markcolors', [\App\Http\Controllers\Api\v1\MarkColorController::class, 'index']);
Route::get('complectcolors', [\App\Http\Controllers\Api\v1\ComplectColorController::class, 'colorpack']);
Route::get('complectprice/{complectation}', [\App\Http\Controllers\Api\v1\ComplectPriceController::class, 'price']);

Route::resource('cars', \App\Http\Controllers\Api\v1\CarController::class);

Route::resource('credits', \App\Http\Controllers\Api\v1\CreditController::class);

Route::resource('banners', \App\Http\Controllers\Api\v1\BannerController::class);

Route::resource('shortcuts', \App\Http\Controllers\Api\v1\ShortcutController::class);

Route::resource('sectionpages', \App\Http\Controllers\Api\v1\SectionPageController::class);

Route::resource('pages', \App\Http\Controllers\Api\v1\PageController::class);



Route::group(['prefix' => 'front'], function() {
	Route::get('marks/name', [\App\Http\Controllers\Api\v1\Front\MarkController::class, 'getMarksName']);
	Route::get('marks/list', [\App\Http\Controllers\Api\v1\Front\MarkController::class, 'list']);
	Route::get('marks/view/{slug}', [\App\Http\Controllers\Api\v1\Front\MarkController::class, 'get']);
	Route::get('complectations/list', [\App\Http\Controllers\Api\v1\Front\ComplectationController::class, 'get']);
	Route::get('complectations/show/{id}', [\App\Http\Controllers\Api\v1\Front\ComplectationController::class, 'show']);
	Route::get('complectations/image/{id}', [\App\Http\Controllers\Api\v1\Front\ComplectationController::class, 'image']);

	Route::get('packs/complectation/{id}', [\App\Http\Controllers\Api\v1\Front\Pack\PackComplectationController::class, 'get']);
	Route::get('packs/car/{id}', [\App\Http\Controllers\Api\v1\Front\Pack\PackCarController::class, 'get']);

	Route::get('devices/complectation/{id}',[\App\Http\Controllers\Api\v1\Front\Device\DeviceComplectationController::class, 'get']);

	Route::get('credits', [\App\Http\Controllers\Api\v1\Front\CreditController::class, 'get']);
	Route::get('cars', [\App\Http\Controllers\Api\v1\Front\CarController::class, 'get']);
	Route::get('car', [\App\Http\Controllers\Api\v1\Front\CarController::class, 'show']);
	Route::get('car/head', [\App\Http\Controllers\Api\v1\Front\CarController::class, 'head']);
	Route::get('car/image', [\App\Http\Controllers\Api\v1\Front\CarController::class, 'image']);
	Route::get('cars/count', [\App\Http\Controllers\Api\v1\Front\CarController::class, 'count']);
	
	Route::get('transmissions/type', [\App\Http\Controllers\Api\v1\Front\TransmissionController::class, 'getTypes']);
	Route::get('drivers/type', [\App\Http\Controllers\Api\v1\Front\DriverController::class, 'getTypes']);

	Route::get('filters/device', [\App\Http\Controllers\Api\v1\Front\DeviceFilterController::class, 'get']);

	Route::get('car/compare', [\App\Http\Controllers\Api\v1\Front\CarCompareController::class, 'compare']);

	Route::get('sections/page/list', [\App\Http\Controllers\Api\v1\Front\PageController::class, 'sections']);
	Route::get('page', [\App\Http\Controllers\Api\v1\Front\PageController::class, 'page']);
});
