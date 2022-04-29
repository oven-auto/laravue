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

//CRUD
Route::prefix('')->namespace('\App\Http\Controllers\Api\v1\Back')->group(function () {
    Route::resource('brands', BrandController::class);
    Route::resource('devicetypes', DeviceTypeController::class);
    Route::resource('devicefilters', DeviceFilterController::class);
    Route::resource('devices', DeviceController::class);
    Route::resource('properties', PropertyController::class);
    Route::resource('motortypes', MotorTypeController::class);
    Route::resource('motortransmissions', MotorTransmissionController::class);
    Route::resource('motordrivers', MotorDriverController::class);
    Route::resource('motors', MotorController::class);
    Route::resource('colors', ColorController::class);
    Route::resource('packs', PackController::class);
    Route::resource('bodyworks', BodyWorkController::class);
    Route::resource('countryfactories', CountryFactoryController::class);
    Route::resource('marks', MarkController::class);
    Route::resource('complectations', ComplectController::class);
    Route::resource('cars', CarController::class);
    Route::resource('credits', CreditController::class);
    Route::resource('banners', BannerController::class);
    Route::resource('shortcuts', ShortcutController::class);
    Route::resource('sectionpages', SectionPageController::class);
    Route::resource('pages', PageController::class);
});

Route::prefix('forms')->group(function() {
    Route::get('sections', [\App\Http\Controllers\Api\v1\Back\Form\FormSectionController::class,'index']);
    Route::post('sections', [\App\Http\Controllers\Api\v1\Back\Form\FormSectionController::class,'store']);
    Route::put('sections/{formsection}', [\App\Http\Controllers\Api\v1\Back\Form\FormSectionController::class,'update']);
    Route::get('sections/{formsection}', [\App\Http\Controllers\Api\v1\Back\Form\FormSectionController::class,'edit']);
    Route::post('formcreate', [\App\Http\Controllers\Api\v1\Back\Form\FormController::class,'store']);
    Route::get('formedit/{form}', [\App\Http\Controllers\Api\v1\Back\Form\FormController::class,'edit']);
    Route::patch('formupdate/{form}', [\App\Http\Controllers\Api\v1\Back\Form\FormController::class,'update']);
    Route::get('controlls', [\App\Http\Controllers\Api\v1\Back\Form\FormController::class, 'controlls']);
});







Route::prefix('services')->group(function () {

    //FILES
    Route::delete('marks/document', [\App\Http\Controllers\Api\v1\Services\Files\MarkDocument\DeleteMarkDocumentController::class, 'index']);


   //МАРШРУТЫ ЦЕНЫ
   Route::prefix('price')->group(function () {
        Route::patch('pack', [\App\Http\Controllers\Api\v1\Services\Price\PackPriceController::class, 'index']);
        Route::patch('complectation', [\App\Http\Controllers\Api\v1\Services\Price\ComplectationPriceController::class, 'set']);
        Route::get('complectation/{complectation}', [\App\Http\Controllers\Api\v1\Services\Price\ComplectationPriceController::class, 'get']);
        Route::patch('complectation/pricestatus', [\App\Http\Controllers\Api\v1\Services\Price\ComplectationPriceController::class, 'pricestatus']);
   });

   //МАРШРУТЫ ИЗМЕНЕНИЯ СОРТИРОВКИ
   Route::prefix('sort')->group(function () {
        Route::patch('complectations', [\App\Http\Controllers\Api\v1\Services\Sort\Complectation\ComplectationSortController::class, 'index']);
        Route::patch('marks',[\App\Http\Controllers\Api\v1\Services\Sort\Mark\MarkSortController::class, 'index']);
        Route::patch('devicetypes', [\App\Http\Controllers\Api\v1\Services\Sort\Device\DeviceTypeSortController::class, 'index']);
        Route::patch('devicefilters', [\App\Http\Controllers\Api\v1\Services\Sort\Device\DeviceFilterSortController::class, 'index']);
        Route::patch('properties', [\App\Http\Controllers\Api\v1\Services\Sort\Property\PropertySortController::class,'index']);
        Route::patch('banners', [\App\Http\Controllers\Api\v1\Services\Sort\Banner\BannerSortController::class,'index']);
   });

   //МАРШРУТЫ ПОЛУЧЕНИЯ КОЛИЧЕСТВА
   Route::prefix('count')->group(function () {
       Route::get('cars', [\App\Http\Controllers\Api\v1\Services\Count\CarComplectCountController::class,'index']);
       Route::get('devicefilters', [\App\Http\Controllers\Api\v1\Services\Count\DeviceFilterCountController::class, 'index']);
       Route::get('devicetypes', [\App\Http\Controllers\Api\v1\Services\Count\DeviceTypeCountController::class, 'index']);
   });

    //МАРШРУТЫ ПОЛУЧЕНИЯ СПИСКОВ ДЛЯ HTML
   Route::prefix('html')->group(function () {
       Route::prefix('select')->group(function () {
           Route::get('brands', [\App\Http\Controllers\Api\v1\Services\Html\Select\BrandSelectController::class, 'index']);
           Route::get('devicetypes', [\App\Http\Controllers\Api\v1\Services\Html\Select\DeviceTypeSelectController::class, 'index']);
           Route::get('devicefilters', [\App\Http\Controllers\Api\v1\Services\Html\Select\DeviceFilterSelectController::class, 'index']);
           Route::get('marks', [\App\Http\Controllers\Api\v1\Services\Html\Select\MarkSelectController::class, 'index']);
           Route::get('motortransmissions', [\App\Http\Controllers\Api\v1\Services\Html\Select\MotorTransmissionSelectController::class, 'index']);
           Route::get('motordrivers', [\App\Http\Controllers\Api\v1\Services\Html\Select\MotorDriverSelectController::class, 'index']);
           Route::get('motortypes', [\App\Http\Controllers\Api\v1\Services\Html\Select\MotorTypeSelectController::class, 'index']);
           Route::get('motors', [\App\Http\Controllers\Api\v1\Services\Html\Select\MotorSelectController::class, 'index']);
           Route::get('toxic', [\App\Http\Controllers\Api\v1\Services\Html\Select\MotorToxicController::class, 'index']);
           Route::get('complectations', [\App\Http\Controllers\Api\v1\Services\Html\Select\ComplectationSelectController::class, 'index']);
           Route::get('bodyworks', [\App\Http\Controllers\Api\v1\Services\Html\Select\BodyWorkSelectController::class, 'index']);
           Route::get('countryfactories', [\App\Http\Controllers\Api\v1\Services\Html\Select\CountryFactorySelectController::class, 'index']);
           Route::get('devices/', [\App\Http\Controllers\Api\v1\Services\Html\Select\DeviceNameController::class, 'index']);
           Route::get('transmissiontypes', [\App\Http\Controllers\Api\v1\Services\Html\Select\TransmissionTypeSelectController::class, 'index']);
           Route::get('drivertypes', [\App\Http\Controllers\Api\v1\Services\Html\Select\DriverTypeSelectController::class, 'index']);
           Route::get('deliverytypes', [\App\Http\Controllers\Api\v1\Services\Html\Select\DeliveryTypeSelectController::class, 'index']);
           Route::get('deliverystages', [\App\Http\Controllers\Api\v1\Services\Html\Select\DeliveryStageSelectController::class, 'index']);
           Route::get('markers', [\App\Http\Controllers\Api\v1\Services\Html\Select\MarkerSelectController::class, 'index']);
           Route::get('users', [\App\Http\Controllers\Api\v1\Services\Html\Select\UserSelectController::class, 'index']);
           Route::get('formevents',[\App\Http\Controllers\Api\v1\Services\Html\Select\FormEventSelectController::class, 'index']);
       });
       Route::prefix('color')->group(function () {
            Route::get('mark', [\App\Http\Controllers\Api\v1\Services\Html\Color\ColorMarkController::class, 'index']);
            Route::get('complectation', [\App\Http\Controllers\Api\v1\Services\Html\Color\ColorComplectationController::class, 'index']);
       });
   });
});

Route::prefix('breadcrumbs')->group(function(){
    Route::get('packs/title', [\App\Http\Controllers\Api\v1\BreadCrumbs\PackBreadCrumbsController::class, 'title']);
    Route::get('devices/title', [\App\Http\Controllers\Api\v1\BreadCrumbs\DeviceBreadCrumbsController::class, 'title']);
    Route::get('colors/title', [\App\Http\Controllers\Api\v1\BreadCrumbs\ColorBreadCrumbsController::class, 'title']);
    Route::get('complectations/title', [\App\Http\Controllers\Api\v1\BreadCrumbs\ComplectationBreadCrumbsController::class, 'title']);
    Route::get('cars/title', [\App\Http\Controllers\Api\v1\BreadCrumbs\CarBreadCrumbsController::class, 'title']);
});

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

  Route::group(['prefix'=>'forms'], function(){
    Route::get('/get', [\App\Http\Controllers\Api\v1\Front\Form\FormController::class, 'get']);
  });
});
