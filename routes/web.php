<?php

use App\Http\Controllers\PeopleController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::prefix('people')->group(function(){
    Route::get('list-human-relations', [PeopleController::class, 'listHumanRelations']);
    Route::get('listproperties', [PeopleController::class, 'listproperties']);
    Route::post('addinfo', [PeopleController::class, 'addInfo']);
});

Route::resource('people', PeopleController::class);