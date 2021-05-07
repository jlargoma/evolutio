<?php

Route::get('/pago-simple/{type}/{token}/{control}', 'CustomerController@pagoSimple');
Route::post('/pago', 'CustomerController@pagar');

Route::get('/agregar-tarjeta/{token}/{control}', 'CustomerController@paymentMethod');
Route::get('/factura/{id}/{num}/{emial}', 'InvoicesController@donwload_external');