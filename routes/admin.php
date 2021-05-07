<?php

Auth::routes();
Route::get('', function () {
    return redirect('admin/clientes');
});
/* Admin routes */
Route::get('/unauthorized', 'AdminController@unauthorized');
Route::post('/changeActiveYear', 'HomeController@changeActiveYear')->name('years.change');


Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {

    /* Clientes */
    Route::get('/clientes/generar-cobro/{rate}', 'UsersController@clienteRateCharge');
    Route::get('/clientes/{month?}', 'UsersController@clientes');
    Route::get('/get-mail/{id?}', 'UsersController@getMail');
    Route::get('/get-rates/{id?}', 'UsersController@getRates');
//    Route::get('/get-list', 'UsersController@getList');
    Route::get('/clientes-export', 'UsersController@exportClients');
    Route::get('/clientes-unassigned/{idUserRate}', 'RatesController@unassignedRate');
    Route::post('/add-subscr', 'UsersController@addSubscr');
    Route::post('/change-subscr-price', 'UsersController@changeSubscr');
    Route::get('/clientes-unsubscr/{uID}/{id}', 'UsersController@rmSubscr');
    Route::get('/usuarios/informe/{id}', 'UsersController@informe');
    Route::post('/usuarios/notes', 'UsersController@addNotes');
    Route::post('/usuarios/del-note', 'UsersController@delNotes');
    Route::post('/usuarios/sign', 'UsersController@addSign');
    Route::get('/usuarios/sign/{uid?}', 'UsersController@getSign');
//    Route::get('/usuarios/informe/{year}/{id}', 'UsersController@informe');
    Route::get('/usuarios/informe/{id}/{tab?}', 'UsersController@informe');
    
    Route::get('/usuarios/nuevo', 'UsersController@newCustomer');
    Route::post('/usuarios/nuevo', 'UsersController@saveCustomer');
     
    /* Entrenadores */
    Route::get('/sendEmail/trainer/{id}', 'UsersController@sendEmailEntrenadores');
    Route::get('/horariosEntrenador/{id?}', 'UsersController@horarios');
    Route::post('/horariosEntrenador', 'UsersController@updHorarios');
    Route::get('/actualizarEntrenador/{id}', 'UsersController@updEntrenador');
    Route::get('/liquidacion-Entrenador/{id}/{date?}', 'CoachLiquidationController@liquidEntrenador');
    Route::get('/enviar-liquidacion-Entrenador/{id}/{date?}', 'CoachLiquidationController@enviarEmailLiquidacion');
    Route::get('/payment-Entrenador/', 'CoachLiquidationController@store');
    Route::get('/entrenadores/liquidacion-entrenador/', 'CoachLiquidationController@coachLiquidation');
    Route::get('/entrenadores/{type?}', 'UsersController@entrenadores');

    
    /* Usuarios */
    Route::get('/usuarios', 'UsersController@index');
    
    Route::get('/usuarios/new/{role?}', 'UsersController@nueva');
    Route::post('/usuarios/create', 'UsersController@create');
    Route::get('/usuarios/actualizar/{id}', 'UsersController@actualizar');
    Route::any('/usuarios/delete/{id}', 'UsersController@delete');
    Route::get('/usuarios/actualizarUsuario/{id}', 'UsersController@actualizarUsuario');
    Route::get('/usuarios/disable/{id}', 'UsersController@disable');
    Route::get('/usuarios/activate/{id}', 'UsersController@activate');
    Route::post('/usuarios/update', 'UsersController@update');
    Route::get('/usuarios/cobrar/tarifa/', 'UsersController@rateCharge');
    Route::post('usuarios/newInforme', 'UsersController@newInforme');
    
    
    

    /* Citas */

    Route::get('/citas/create/createSchedules', 'DatesController@createSchedules');
    Route::get('/citas/delete/{id}', 'DatesController@delete');
    Route::post('/citas/create', 'DatesController@create');
    Route::post('/citas/createAdvanced', 'DatesController@createAdvanced');
    Route::post('/citas/chargeAdvanced', 'DatesController@chargeAdvanced');
    Route::get('/citas/charge', 'DatesController@chargeDate');
    /* Citas fisioterapia */
    Route::get('/citas-fisioterapia/listado/{coach?}/{type?}', 'FisioController@listado');
    Route::get('/citas-fisioterapia/create/{date?}/{time?}', 'FisioController@create');
    Route::get('/citas-fisioterapia/informe/{id}', 'FisioController@informe');
    Route::get('/citas-fisioterapia/edit/{id}', 'FisioController@edit');
    Route::get('/citas-fisioterapia/{month?}/{coach?}/{type?}', 'FisioController@index');
    /* Citas Nutrición */
    Route::get('/citas-nutricion/listado/{coach?}/{type?}', 'NutriController@listado');
    Route::get('/citas-nutricion/create/{date?}/{time?}', 'NutriController@create');
    Route::get('/citas-nutricion/informe-nutricion/{id}', 'NutriController@informe');
    Route::post('/nutricion/nutri/upload', 'NutriController@uploadFile');
    Route::get('/citas-nutricion/edit/{id}', 'NutriController@edit');
    Route::get('/citas-nutricion/{month?}/{coach?}/{type?}', 'NutriController@index');
    /* Citas personalTrainer */
    Route::get('/citas-pt/listado/{coach?}/{type?}', 'PTController@listado');
    Route::get('/citas-pt/create/{date?}/{time?}', 'PTController@create');
//    Route::get('/citas-pt/informe-nutricion/{id}', 'NutriController@informe');
    Route::get('/citas-pt/edit/{id}', 'PTController@edit');
    Route::get('/citas-pt/{month?}/{coach?}/{type?}', 'PTController@index');

    /* Tarifas */
    Route::get('/tarifas/listado', 'RatesController@index');
    Route::get('/tarifas/new', 'RatesController@newRate');
    Route::post('/tarifas/create', 'RatesController@create');
    Route::get('/tarifas/actualizar/{id}', 'RatesController@actualizar');
    Route::get('/tarifas/update', 'RatesController@update');
    Route::get('/tarifas/delete/{id}', 'RatesController@delete');
    Route::get('/tarifas/stripe/{id}', 'RatesController@createStripe');
    Route::get('/rates/unassigned/{idUserRate}', 'RatesController@unassignedRate');
    
    
    /* Facturacion */
  Route::get('/facturacion/entrenadores', 'UsersController@entrenadores');

  //Facturas
  Route::get('/facturas/ver/{id}', 'InvoicesController@view')->name('invoice.view');
  Route::get('/facturas/editar/{id}', 'InvoicesController@update')->name('invoice.edit');
  Route::post('/facturas/guardar', 'InvoicesController@save')->name('invoice.save');
  Route::post('/facturas/enviar', 'InvoicesController@sendMail')->name('invoice.sendmail');
  Route::get('/facturas/modal/editar/{id}', 'InvoicesController@update_modal');
  Route::post('/facturas/modal/guardar', 'InvoicesController@save_modal');
  Route::delete('/facturas/borrar', 'InvoicesController@delete')->name('invoice.delete');
  Route::get('/facturas/descargar/{id}', 'InvoicesController@download')->name('invoice.downl');
  Route::get('/facturas/descargar-todas', 'InvoicesController@downloadAll');
  Route::get('/facturas/solicitudes/{year?}', 'InvoicesController@solicitudes');
  Route::get('/facturas/{order?}', 'InvoicesController@index');
  
  /* Cobros */
  Route::get('/generar/cobro', 'ChargesController@generarCobro');
  Route::get('/update/cobro/{id}', 'ChargesController@updateCobro');
  Route::post('/send/cobro-mail', 'ChargesController@sendCobroMail');
  Route::post('/send/cobro-gral', 'ChargesController@sendCobroGral');
  Route::post('/cobros/cobrar', 'ChargesController@cobrar');
  Route::post('/cobros/cobrar/{id}', 'ChargesController@updateCharge');
  Route::get('/cobros/getPriceTax', 'ChargesController@getPriceTax');
  Route::post('/cobros/cobrar-usuario', 'ChargesController@chargeUser');
  Route::get('/informes/cuotas-mes/{month?}/{day?}', 'InformesController@informeCuotaMes');
  Route::get('/informes/cliente-mes/{month?}/{day?}', 'InformesController@informeClienteMes');
  Route::post('/informes/search/{month?}', 'InformesController@searchClientInform');
  Route::get('/informes/cierre-diario/{month?}/{day?}', 'InformesController@informeCierreDiario');
  Route::get('/informes/cajas', 'InformesController@informeCaja');
  
  Route::get('', function () {
    return redirect('admin/clientes');
});
});

Route::group(['middleware' => ['auth','admin'], 'prefix' => 'admin'], function () {

  /* Ingresos  rutas basicas */
  Route::get('/nuevo/ingreso', 'IncomesController@nuevo');
  Route::post('/ingresos/create', 'IncomesController@create');
  Route::get('/ingresos/{date?}', 'IncomesController@index');
  Route::get('/ingreso-by-rate/{rateID}', 'IncomesController@byRate');
  
  Route::get('/ingresos', 'IncomesController@index');

  /* Gastos  rutas basicas */
//  Route::get('/nuevo/gasto', 'ExpensesController@nuevo');
//  Route::get('/gastos/{date?}', 'ExpensesController@index');
      
  Route::post('/gastos/create', 'ExpensesController@create');
  Route::post('/gastos/importar', 'ExpensesController@gastos_import');
  Route::post('/gastos/gastosLst', 'ExpensesController@getTableGastos');
  Route::post('/gastos/update', 'ExpensesController@updateGasto');
  Route::get('/gastos/getHojaGastosByRoom/{year?}/{id}', 'ExpensesController@getHojaGastosByRoom');
  Route::get('/gastos/containerTableExpensesByRoom/{year?}/{id}', 'ExpensesController@getTableExpensesByRoom');
  Route::post('/gastos/del', 'ExpensesController@gastosDel');
  Route::get('/gastos-by-byType/{typeID}', 'ExpensesController@byType');
  Route::get('/gastos/{year?}', 'ExpensesController@gastos');
  
  
  Route::get('/perdidas-ganancias', 'PyGController@index');
  
  /* Pendiente  rutas basicas */
  Route::get('/nuevo/pending', 'PendingController@nuevo');
  Route::post('/pending/create', 'PendingController@create');

  /* Gastos */
  Route::get('/gastos', 'ExpensesController@index');
  Route::post('/gastos/import/csv/', 'ExpensesController@importCsv');

  /* Pendiente */
  Route::get('/pending/{year?}', 'PendingController@index');
  Route::post('/pending/import/csv/', 'PendingController@importCsv');
  Route::get('/pending/migrate/gasto', 'PendingController@banco');
  Route::get('/pending/delete/{id}', 'PendingController@delete');
  Route::get('/pending/{account?}/{year?}/{month?}', 'PendingController@index');

  /* caja */
  Route::post('/cashbox/import/csv/', 'CashBoxController@importCsv');
  Route::get('/nuevo/addCashBox', 'CashBoxController@nuevo');
  Route::post('/cashbox/create', 'CashBoxController@create');
  Route::get('/cashbox/migrate/gasto', 'CashBoxController@cashbox');
  Route::get('/cashbox/{year?}', 'CashBoxController@index');
  Route::get('/cashbox/{year?}/{type?}', 'CashBoxController@index');

    /* Contabilidad */

  Route::get('/cuenta-socios/', 'ContabilidadController@socios');
  Route::get('/salario-mes/', 'ContabilidadController@salarioMes');
  Route::get('/ventas-mes/', 'ContabilidadController@ventasMes');
});


Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {
  Route::get('/userRates', 'FunctionalControler@userRates');
  Route::post('/userRates', 'FunctionalControler@save_userRates');
});