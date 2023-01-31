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
// Route::get( '/{any}', function() {
//     return view('layouts.admin');
// })->where('any', '.*');
