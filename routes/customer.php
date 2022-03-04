<?php

Route::get('/pago-simple/{type}/{token}/{control}', 'CustomerController@pagoSimple');
Route::get('/copra-de-bonos/{token}/{control}', 'CustomerController@comprarBonos');
Route::post('/pago', 'CustomerController@pagar');
Route::post('/stripe_charge', 'CustomerController@stripeCharge');

Route::get('/agregar-tarjeta/{token}/{control}', 'CustomerController@paymentMethod');
Route::get('/factura/{id}/{num}/{emial}', 'InvoicesController@donwload_external');

Route::get('/firmar-consentimiento/{token}/{control}', 'CustomerController@signConsent');
Route::post('/firmar-consentimiento/{token}/{control}', 'CustomerController@signConsentSave');
Route::get('/valoracion/{token}/{control}', 'CustomerController@seeValoracion');
Route::post('/firmar-valoracion/{token}/{control}', 'CustomerController@signValoracion');
Route::get('/descargar-valoracion/{token}/{control}', 'CustomerController@downlValoracion');
Route::get('/public-sign/{file?}', 'CustomerController@getSign');
Route::get('/firmar-contrato/{token}/{control}', 'CustomerController@signContrato');
Route::post('/firmar-contrato/{token}/{control}', 'CustomerController@signContratoSave');
Route::get('/descargrar-contrato/{token}/{control}', 'CustomerController@downlContract');

Route::get('/resultado', 'CustomerController@showResult');
Route::get('/cobro-completado', 'CustomerController@paymentSuccess')->name('customer.pay.success');
Route::get('/cobro-cancelado', 'CustomerController@paymentCancel')->name('customer.pay.cancel');


Route::get('/archivo-nutricion/{token}/{control}', 'CustomerController@getFile');

Route::get('/encuesta-nutricion/{token}/{control}', 'PollController@formEncuestaNutri');
Route::post('/encuesta-nutricion', 'PollController@setEncNutri');

Route::get('/historia-clinica/{token}/{control}', 'PollController@formCliHistory');
Route::post('/historia-clinica', 'PollController@setCliHistory');
Route::get('/seeImg/{path}/{file}', 'FunctionalControler@seeImg');