<?php

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

include_once 'admin.php';
include_once 'customer.php';

Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {
  Route::get('/import/{tipe}', function($tipe){
    $oServc = new \App\Services\temps\ImportCustomers();
    $oServc->import($tipe);
  });
  
});

