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

// Route::fallback(function(){
//     return response()->json([
//         'message' => 'Данные не найдены',
//         'success' => false,
//     ], 404);
// });

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('time', function() {
    return response()->json([
        'datetime' => date('Y-m-d\TH:i'),
    ]);
});

Route::prefix('auth')->namespace('\App\Http\Controllers\Api\v1\Auth')->group(function() {
    Route::get('login', function () {
        return response()->json([
            'success' =>true,
            'message' => 'Открывай форму логина'
        ]);
    })->name('login');
    Route::post('login', 'LoginController@index');
    Route::get('logout', 'LogoutController@index');
    Route::get('check', 'LoginController@check')->middleware(['corsing','userfromtoken']);
    Route::post('register', 'RegisterController@register');
});



Route::get('exit', function() {
    Auth::logout();
    return response()->json([
        'message' => 'Выход успешен'
    ]);
});

//CRUD
Route::prefix('')->namespace('\App\Http\Controllers\Api\v1\CMS')->group(function () {
    Route::resource('brands',  BrandController::class);
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
    //Route::resource('clients', Client\ClientController::class);
});

Route::prefix('forms')->group(function() {
    Route::get('sections', [\App\Http\Controllers\Api\v1\Back\Form\FormSectionController::class,'index']);
    Route::post('sections', [\App\Http\Controllers\Api\v1\Back\Form\FormSectionController::class,'store']);
    Route::put('sections/{formsection}', [\App\Http\Controllers\Api\v1\Back\Form\FormSectionController::class,'update']);
    Route::get('sections/{formsection}', [\App\Http\Controllers\Api\v1\Back\Form\FormSectionController::class,'edit']);
    Route::delete('sections/{section}', [\App\Http\Controllers\Api\v1\Back\Form\FormSectionController::class,'destroy']);
    Route::post('formcreate', [\App\Http\Controllers\Api\v1\Back\Form\FormController::class,'store']);
    Route::get('formedit/{form}', [\App\Http\Controllers\Api\v1\Back\Form\FormController::class,'edit']);
    Route::patch('formupdate/{form}', [\App\Http\Controllers\Api\v1\Back\Form\FormController::class,'update']);
    Route::get('controlls', [\App\Http\Controllers\Api\v1\Back\Form\FormController::class, 'controlls']);
    Route::delete('formdelete/{form}',[\App\Http\Controllers\Api\v1\Back\Form\FormController::class,'destroy']);
});


Route::get('moderator/complectation/{complectation}', [\App\Http\Controllers\Api\v1\Complectation\LastModeratorComplectationController::class, 'index']);




Route::prefix('services')->group(function () {
    //IMAGES
    Route::prefix('images')->group(function() {
        Route::get('devices', [\App\Http\Controllers\Api\v1\Services\Images\DeviceImageController::class, 'index']);
    });

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
        Route::patch('forms', [\App\Http\Controllers\Api\v1\Services\Sort\Form\FormSortController::class,'index']);
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
           Route::get('groupdevices/', [\App\Http\Controllers\Api\v1\Services\Html\Select\DeviceGroupController::class, 'index']);
           Route::get('transmissiontypes', [\App\Http\Controllers\Api\v1\Services\Html\Select\TransmissionTypeSelectController::class, 'index']);
           Route::get('drivertypes', [\App\Http\Controllers\Api\v1\Services\Html\Select\DriverTypeSelectController::class, 'index']);
           Route::get('deliverytypes', [\App\Http\Controllers\Api\v1\Services\Html\Select\DeliveryTypeSelectController::class, 'index']);
           Route::get('deliverystages', [\App\Http\Controllers\Api\v1\Services\Html\Select\DeliveryStageSelectController::class, 'index']);
           Route::get('markers', [\App\Http\Controllers\Api\v1\Services\Html\Select\MarkerSelectController::class, 'index']);
           Route::get('users', [\App\Http\Controllers\Api\v1\Services\Html\Select\UserSelectController::class, 'index']);
           Route::get('formevents',[\App\Http\Controllers\Api\v1\Services\Html\Select\FormEventSelectController::class, 'index']);
           Route::get('test',[\App\Http\Controllers\Api\v1\Services\Html\Select\CarSelectController::class, 'index']);
           Route::get('servicejob',[\App\Http\Controllers\Api\v1\Services\Html\Select\ServiceJobSelectController::class, 'index']);
           Route::get('forms', [\App\Http\Controllers\Api\v1\Services\Html\Select\FormSelectController::class, 'index']);
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
    Route::get('motors/title', [\App\Http\Controllers\Api\v1\BreadCrumbs\MotorBreadCrumbsController::class, 'title']);
    Route::get('clients/title', [\App\Http\Controllers\Api\v1\BreadCrumbs\ClientBreadCrumbsController::class, 'title']);
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


Route::prefix('listing')->middleware(['corsing','userfromtoken'])->namespace('\App\Http\Controllers\Api\v1\Listing')->group(function() {
    Route::get('users', 'UserController@index');
    Route::get('zones', 'ZoneController@index');
    Route::get('chanels', 'ChanelController@index');
    Route::get('structures', 'StructureController');
    Route::get('appeals', 'AppealController');
});

//
Route::middleware(['corsing','userfromtoken'])->group(function() {
    //middleware сделан в контролере
    Route::resource('serviceproducts', '\App\Http\Controllers\Api\v1\Back\ServiceProduct\ServiceProductController')
        ->except(['edit','create']);
    //middleware сделан в контролере
    Route::resource('productgroups', '\App\Http\Controllers\Api\v1\Back\ServiceProduct\ProductGroupController')
        ->except(['edit','create']);
});

//СИСТЕМНЫЕ КОНТРОЛЛЕРЫ
Route::middleware(['corsing','userfromtoken'])->group(function(){
    //middleware сделан в контролере
    Route::resource('users', '\App\Http\Controllers\Api\v1\Back\User\UserController')
        ->except(['edit','create']);
    //middleware сделан в контролере
    Route::resource('roles', '\App\Http\Controllers\Api\v1\Back\User\RoleController')
        ->except(['edit','create']);
    Route::resource('permissions', '\App\Http\Controllers\Api\v1\Back\User\PermissionController')
        ->except(['edit','create','store', 'delete']);
});

/**
 * ЭКСПОРТЫ
 */
Route::prefix('export')->middleware(['userfromtoken'])->group(function(){
    Route::get('trafic', '\App\Http\Controllers\Api\v1\Back\Trafic\TraficExportController@export')
        ->middleware(['permission.trafic.list:trafic_export']);
    //Экспорт клиентов в excel
    Route::get('client','\App\Http\Controllers\Api\v1\Back\Client\ClientExportController')
        ->middleware(['permission.trafic.list:client_export']);;
});

/**
 * ТРАФИК
 */
Route::prefix('trafic')->middleware(['corsing','userfromtoken'])->namespace('\App\Http\Controllers\Api\v1\Back\Trafic')->group(function() {
    //получить список зон трафика
    Route::get('zones', 'TraficZoneController@index');
    //получить список каналов трафика
    Route::get('chanels', 'TraficChanelController@index');
    //получить список полов объекта трафика
    Route::get('sexlist', 'TraficSexController@index');
    //получить список компаний трафика
    Route::get('companies', 'TraficCompanyController@index');
    //получить список структур выбраной компании трафика, brand_id это id компании
    Route::get('structures/{brand_id}', 'TraficStructureController@index');
    //получить список обращений указаной структуры компании трафика, id - структура
    Route::get('appeals/{id}', 'TraficAppealController@index');
    //получить список товаров и услуг, в зависимости от выбранного обращения трафика, id - обращения
    Route::get('needs/{company_id?}', 'TraficNeedController@index');
    //получить список моделей по id компании
    Route::get('models/{company_id}','TraficNeedController@models');
    //Получить список товаров по обращению
    Route::get('appealneeds/{trafic_appeal_id}', 'TraficNeedController@appealneed');
    //получить список задач трафика - !!!с 14-02-23 не используется!!!
    /**/Route::get('tasks', 'TraficTaskController@index');
    //получить список пользователей являющихся сотрудниками указанной структуры трафика и
    //обрабатывающими указанное обращение, structure_id - структура, appeal_id - обращение
    Route::get('users/{structure_id}/{appeal_id}', 'TraficUserController@index');
    //получить список статусов трафика
    Route::get('statuses', 'TraficStatusController@index');
    //получить список типов аудита трафика
    Route::get('standarts', 'StandartController');
    //Получить список статусов аудита пройден\не пройден\отсутсвует
    Route::get('auditresults', 'AuditStatusListController');

    //кол-во всех трафикаов +
    Route::get('count', 'TraficCountController@index')
        ->middleware(['permission.trafic.list:trafic_list']);

    //список всех трафиков
    Route::get('list','TraficController@index')
        ->middleware(['permission.trafic.list:trafic_list']);

    //создание трафика
    Route::post('create', 'TraficController@store')
        ->middleware(['permission.trafic.create:trafic_add']);

    //упустить трафик
    Route::patch('close/{trafic}', 'TraficController@close')
        ->middleware([
            'permission.trafic.show:trafic_close',
            'permission.trafic.showalien:trafic_close_alien'
        ]);

    //загрузить загруженные аудиты трафика
    Route::post('audit/{trafic}', 'TraficAuditController@load')
        ->middleware(['permission.trafic.show:trafic_files_load',]);

    //Изменить загруженные аудиты трафика
    Route::patch('audit/{trafic_processing}', 'TraficAuditController@update')
        ->middleware(['permission.trafic.show:trafic_files_load',]);

    //показать фаилы аудит трафика, id = конкретного аудита
    Route::get('audit/{trafic_processing}', 'TraficAuditController@show')
        ->middleware(['permission.trafic.show:trafic_files_show',]);

    Route::post('files/{trafic}', 'TraficFileController');

    //пометить трафик как удаленный
    Route::delete('{trafic}', 'TraficController@delete')
        ->middleware([
            'permission.trafic.show:trafic_softdelete',
            'permission.trafic.showalien:trafic_softdelete_alien'
        ]);

    //просмотр трафика
    Route::get('{trafic}', 'TraficController@edit')
        ->middleware([
            'permission.trafic.show:trafic_show',
            'permission.trafic.showalien:trafic_show_alien'
        ]);

    //изменение трафика
    Route::patch('{trafic}', 'TraficController@update')
        ->middleware([
            'permission.trafic.show:trafic_update',
            'permission.trafic.showalien:trafic_update_alien'
        ]);
});

/**
 * КЛИЕНТЫ
 */
Route::prefix('client')->middleware(['corsing','userfromtoken'])->namespace('\App\Http\Controllers\Api\v1\Back\Client')->group(function() {
    //список типов клиентов физ/юр
    Route::get('types', 'ClientTypeController');
    //список типов лояльности
    Route::get('loyalties', 'LoyaltyController@list');
    //Количество клиентов
    Route::get('count', 'ClientCountController');
    //cписок клиентов
    Route::get('list', 'ClientController@index')
        ->middleware(['permission.trafic.list:client_list']);
    //создать клиента
    Route::post('create', 'ClientController@store')
        ->middleware(['permission.trafic.list:client_add']);
    //получить конкретного клиента
    Route::get('{client}', 'ClientController@edit')
        ->middleware(['permission.trafic.list:client_show']);
    //изменить конкретного клиента
    Route::patch('{client}', 'ClientController@update')
        ->middleware(['permission.trafic.list:client_edit']);
    //удалить клиента
    Route::delete('{client}', 'ClientController@destroy')
        ->middleware(['permission.trafic.list:client_delete']);

    Route::prefix('car')->middleware('permission.trafic.list:client_add')->group(function(){
        //список брендов
        Route::get('brands', 'BrandCarController');
        //количество машин клиента
        Route::get('amount/{client}', 'ClientCarController@amount');
        //список марок по бренду
        Route::get('marks/{brand}', 'MarkCarController');
        //список кузовов
        Route::get('bodies', 'BodyCarController');
        //добавить машину в клиента
        Route::post('{client}', 'ClientCarController@store');
        //список машин конкретного клиета
        Route::get('/list/{client}', 'ClientCarController@index');
        //Получить конкретную машину
        Route::get('{car}', 'ClientCarController@show');
        //изменить машину
        Route::patch('{car}', 'ClientCarController@update');
        //удалить машину
        Route::delete('{car}', 'ClientCarController@destroy');
    });

});

Route::prefix('worksheet')->middleware(['corsing','userfromtoken'])->namespace('\App\Http\Controllers\Api\v1\Back\Worksheet')->group(function() {
    Route::post('create', 'WorksheetController@store')
        ->middleware([
            'worksheet.create.base',
            'permission.worksheet.create'
        ]);
});


