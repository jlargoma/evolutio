<?php

Route::get('/pago-simple/{type}/{token}/{control}', 'CustomerController@pagoSimple');
Route::post('/pago', 'CustomerController@pagar');
Route::post('/stripe_charge', 'CustomerController@stripeCharge');

Route::get('/agregar-tarjeta/{token}/{control}', 'CustomerController@paymentMethod');
Route::get('/factura/{id}/{num}/{emial}', 'InvoicesController@donwload_external');

Route::get('/firmar-consentimiento/{token}/{control}', 'CustomerController@signConsent');
Route::post('/firmar-consentimiento/{token}/{control}', 'CustomerController@signConsentSave');

Route::get('/resultado', 'CustomerController@showResult');
Route::get('/cobro-completado', 'CustomerController@paymentSuccess')->name('customer.pay.success');
Route::get('/cobro-cancelado', 'CustomerController@paymentCancel')->name('customer.pay.cancel');