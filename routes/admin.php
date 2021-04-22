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
//    Route::get('/get-list', 'UsersController@getList');
    Route::get('/clientes-export', 'UsersController@exportClients');
    Route::get('/clientes-unassigned/{idUserRate}', 'RatesController@unassignedRate');
    Route::post('/add-service', 'UsersController@addRate');
    Route::get('/usuarios/informe/{id}', 'UsersController@informe');
    Route::post('/usuarios/notes', 'UsersController@addNotes');
    Route::post('/usuarios/del-note', 'UsersController@delNotes');
    Route::post('/usuarios/sign', 'UsersController@addSign');
    Route::get('/usuarios/sign/{uid?}', 'UsersController@getSign');
//    Route::get('/usuarios/informe/{year}/{id}', 'UsersController@informe');
    Route::get('/usuarios/informe/{id}/{tab?}', 'UsersController@informe');
    
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
//    Route::get('/citas/create/createSchedules', 'DatesController@createSchedules');
//    Route::post('/citas/charged/charge', 'DatesController@charge');
//    Route::get('/citas/crear/nuevo', 'DatesController@nueva');
    Route::get('/citas/delete/{id}', 'DatesController@delete');
    Route::post('/citas/create', 'DatesController@create');
    Route::post('/citas/createAdvanced', 'DatesController@createAdvanced');
    Route::post('/citas/chargeAdvanced', 'DatesController@chargeAdvanced');
    Route::get('/citas/charge', 'DatesController@chargeDate');
//    Route::get('/citas/getForm/cita/{id}', 'DatesController@getForm');
//    Route::get('/citas/_dates/{week?}', 'DatesController@dates');
//    Route::get('/citas/{month?}', 'DatesController@index');
//    Route::get('/citas/form/inform/create/{id}/{type}', 'DatesController@informeCreateFrom');

    /* Citas fisioterapia */
    Route::get('/citas-fisioterapia/listado/{coach?}/{type?}', 'FisioController@listado');
    Route::get('/citas-fisioterapia/create/{date?}/{time?}', 'FisioController@create');
    Route::get('/citas-fisioterapia/informe/{id}', 'FisioController@informe');
    Route::get('/citas-fisioterapia/edit/{id}', 'FisioController@edit');
    Route::get('/citas-fisioterapia/{month?}/{coach?}/{type?}', 'FisioController@index');
    /* Citas Nutrición */
    Route::get('/citas-nutricion/listado/{coach?}/{type?}', 'NutriController@listado');
    Route::get('/citas-nutricion/create/{date?}/{time?}', 'NutriController@create');
    Route::get('/citas-nutricion/informe-nutricion/{id}', 'NutriController@informeNutricion');
    Route::post('/nutricion/nutri/upload', 'NutriController@uploadFile');
    Route::get('/citas-nutricion/edit/{id}', 'NutriController@edit');
    Route::get('/citas-nutricion/{month?}/{coach?}/{type?}', 'NutriController@index');

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
//  Route::get('/facturacion/entrenadores/new', 'FacturacionController@nueva');
//  Route::post('/facturacion/create', 'FacturacionController@create');
//  Route::get('/facturacion/entrenador/actualizar/{id}', 'FacturacionController@actualizar');
//  Route::post('/facturacion/entrenador/update', 'FacturacionController@update');
//  Route::get('/facturacion/entrenador/delete/{id}', 'FacturacionController@delete');
//  Route::get('/facturacion/generar-liquidacion/{id}', 'FacturacionController@liquidacion');
//  Route::get('/facturacion/generar-liquidacion/{id}/{date?}', 'FacturacionController@liquidacion');
//  Route::get('/facturacion/getDesgloceClase', 'FacturacionController@getDesgloceClase');
//  Route::get('/facturacion/enviar-liquidacion/{id}/{date?}', 'FacturacionController@enviarEmailLiquidacion');

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

Route::group(['middleware' => ['auth','admin'], 'prefix' => ''], function () {

  /* Ingresos  rutas basicas */
  Route::get('/admin/ingresos/{year?}', 'IncomesController@index');
  Route::get('/admin/nuevo/ingreso', 'IncomesController@nuevo');
  Route::post('/admin/ingresos/create', 'IncomesController@create');
  Route::get('/admin/ingresos/{date?}', 'IncomesController@index');

  /* Gastos  rutas basicas */
  Route::get('/admin/nuevo/gasto', 'ExpensesController@nuevo');
  Route::post('/admin/gastos/create', 'ExpensesController@create');
  Route::get('/admin/gastos/{date?}', 'ExpensesController@index');

  /* Pendiente  rutas basicas */
  Route::get('/admin/nuevo/pending', 'PendingController@nuevo');
  Route::post('/admin/pending/create', 'PendingController@create');

  /* Gastos */
  Route::get('/admin/gastos', 'ExpensesController@index');
  Route::post('/admin/gastos/import/csv/', 'ExpensesController@importCsv');

  /* Pendiente */
  Route::get('/admin/pending/{year?}', 'PendingController@index');
  Route::post('/admin/pending/import/csv/', 'PendingController@importCsv');
  Route::get('/admin/pending/migrate/gasto', 'PendingController@banco');
  Route::get('/admin/pending/delete/{id}', 'PendingController@delete');
  Route::get('/admin/pending/{account?}/{year?}/{month?}', 'PendingController@index');

  /* caja */
  Route::post('/admin/cashbox/import/csv/', 'CashBoxController@importCsv');
  Route::get('/admin/nuevo/addCashBox', 'CashBoxController@nuevo');
  Route::post('/admin/cashbox/create', 'CashBoxController@create');
  Route::get('/admin/cashbox/migrate/gasto', 'CashBoxController@cashbox');
  Route::get('/admin/cashbox/{year?}', 'CashBoxController@index');
  Route::get('/admin/cashbox/{year?}/{type?}', 'CashBoxController@index');

    /* Contabilidad */

  Route::get('/admin/cuenta-socios/', 'ContabilidadController@socios');
  Route::get('/admin/salario-mes/', 'ContabilidadController@salarioMes');
  Route::get('/admin/ventas-mes/', 'ContabilidadController@ventasMes');
});
