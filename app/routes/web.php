<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('admin.brand.index');
// });





//Auth::routes();

// Страница создания токена
// Route::get('dashboard', function () {
//     if(Auth::check() && Auth::user()->role === 1){
//         return auth()
//             ->user()
//             ->createToken('auth_token', ['admin'])
//             ->plainTextToken;
//     }
//     return redirect("/home");
// })->middleware('auth');

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('phpinfo', function() {
    phpinfo();
});

Route::get('/doc', function(){
    $data = \App\Models\ApiDescription::get()->groupBy('title');
    return view('admin.doc', ['data' => $data]);
});

Route::get('hash', function(Request $request){
    echo Hash::make($request->get('string'));
});

// Route::prefix('export')->middleware(['corsing','userfromtoken'])->group(function () {
//     Route::get('trafics', '\App\Http\Controllers\Api\v1\Back\Trafic\TraficExportController');
// });

Route::prefix('pdf')->group(function () {
    Route::get('trafics/{trafic}', '\App\Http\Controllers\Api\v1\Back\Trafic\TraficPDFController');
});

// Route::get( '/cms', function() {
//     return view('layouts.admin');
// })->where('any', '.*');

Route::get('test', '\App\Http\Controllers\HomeController@test');
