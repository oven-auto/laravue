<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

// Route::prefix('export')->middleware(['corsing','userfromtoken'])->group(function () {
//     Route::get('trafics', '\App\Http\Controllers\Api\v1\Back\Trafic\TraficExportController');
// });

Route::prefix('pdf')->group(function () {
    Route::get('trafics/{trafic}', '\App\Http\Controllers\Api\v1\Back\Trafic\TraficPDFController');
});

// Route::get( '/{any}', function() {
//     return view('layouts.admin');
// })->where('any', '.*');

Route::get('test', function() {
    $value = [1,3,7];
    $chanels = \App\Models\TraficChanel::select('*')
        ->leftJoin('trafic_chanels as jtc', function($join){
            $join->on('jtc.parent','=','trafic_chanels.id');
            $join->orWhere(function($query) {
                $query->where('trafic_chanels.parent', \DB::raw('0'));
                $query->where('trafic_chanels.id', \DB::raw('jtc.id'));
            });
        })
        ->whereIn('trafic_chanels.id', $value)
        ->groupBy('jtc.id')
        ->groupBy('trafic_chanels.id')
        ->pluck('id');
    return response()->json([
        'data' => $chanels
    ]);
});
