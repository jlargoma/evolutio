<?php

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

Route::group(['middleware' => ['auth','role:admin|limpieza|subadmin|recepcionista']], function () {
  Route::get('/', 'HomeController@index')->name('home');
  
  /**
 * USER 
 */
  Route::group(['prefix' => 'user'], function () {
    Route::get('/edit/{id}', 'UsersController@edit')->name('user.edit');
    Route::post('/update/{id}', 'UsersController@update')->name('user.update');
    Route::DELETE('/destroy/{id}', 'UsersController@destroy')->name('users.destroy');
    Route::get('/', 'UsersController@index')->name('users.index');
  });
  /**
 * CONTABILIDAD 
 */
  Route::group(['prefix' => 'contabilidad'], function () {
    Route::get('/ventas', 'ContableController@index')->name('contabl.ventas');
    Route::get('/salarios', 'ContableController@index')->name('contabl.salarios');
    Route::get('/', 'ContableController@index')->name('contabl');
  });
  
  
  /* CSV */
  Route::get('/importar', 'DocumentImportsController@index');
  Route::post('/importar/clientes', 'DocumentImportsController@inportSales');
  Route::post('/importar/instructor', 'DocumentImportsController@inportInstructor');
  
});
Auth::routes();
Route::get('/no-allowed','AppController@no_allowed');
Route::get('403','AppController@no_allowed');
