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
    Route::get('list', [PeopleController::class, 'listPeople'])->name('people.list');
    Route::get('new', [PeopleController::class, 'create'])->name('people.add');
    Route::post('insert', [PeopleController::class, 'insert']);

    Route::get('list-human-relations', [PeopleController::class, 'listHumanRelations']);
    
    
    // Route::get('{people}', [PeopleController::class, 'showAddPeopleInformationForm']);
    Route::get('{people}/add', [PeopleController::class, 'showAddPeopleInformationForm'])->name('people.info');
    Route::get('{people}/delete', [PeopleController::class, 'deletePeople'])->name('people.delete');

    Route::get('{people}/edit/{value}', [PeopleController::class, 'showEditPeopleInformationForm']);
    Route::post('{people}/update/{value}', [PeopleController::class, 'updatePeoplePropertyValue']);
    Route::get('{people}/delete/{value}', [PeopleController::class, 'deletePeoplePropertyValue']);

    Route::get('listproperties', [PeopleController::class, 'listproperties']);
    Route::post('addinfo', [PeopleController::class, 'addInfo']);
});