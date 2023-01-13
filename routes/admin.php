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
  Route::get('/clientes/lstByBonos', 'UsersController@lstByBonos');
  Route::get('/clientes/generar-cobro/{rate}', 'UsersController@clienteRateCharge');
  Route::get('/clientes/{month?}', 'UsersController@clientes');
  Route::post('/clientes/update', 'UsersController@updateCli');
  Route::post('/clientes/setValora', 'UsersController@setValora');
  Route::post('/clientes/saveFiles', 'UsersController@saveFiles');
  Route::post('/clientes/delFiles', 'UsersController@delFiles');
  Route::get('/photo/{id}', 'CustomerController@getPhoto');
  Route::post('/clientes/autosaveValora', 'UsersController@autosaveValora');
  Route::post('/clientes/sendFileTo', 'UsersController@sendFileTo');
  Route::get('/get-mail/{id?}', 'UsersController@getMail');
  Route::get('/get-rates/{id?}', 'UsersController@getRates');
  Route::get('/clientes-export/{status?}', 'UsersController@exportClients');
  Route::get('/clientes-unassigned/{idUserRate}', 'RatesController@unassignedRate');
  Route::post('/add-subscr', 'UsersController@addSubscr');
  Route::post('/change-subscr-price', 'UsersController@changeSubscr');
  Route::get('/clientes-unsubscr/{uID}/{id}', 'UsersController@rmSubscr');
  Route::get('/usuarios/informe/{id}', 'UsersController@informe');
  Route::post('/usuarios/notes', 'UsersController@addNotes');
  Route::post('/usuarios/del-note', 'UsersController@delNotes');
  Route::post('/usuarios/sign', 'UsersController@addSign');
  Route::get('/usuarios/sign/{file?}', 'UsersController@getSign');
  Route::post('/usuarios/send-consent', 'UsersController@sendConsent');
  Route::post('/usuarios/send-valoracion', 'UsersController@sendValoracion');
  Route::get('/see-consent/{id}/{type}', 'UsersController@seeConsent');
  Route::get('/downl-consent/{id}/{type}', 'UsersController@downlConsent');
  Route::get('/autorizacion/{id}/{type?}', 'UsersController@downlAutorizacion');
  Route::get('/usuarios/informe/{id}/{tab?}', 'UsersController@informe');
  Route::get('/usuarios/disable/{id}', 'UsersController@disable');
  Route::get('/usuarios/activate/{id}', 'UsersController@activate');
  Route::get('/usuarios/nuevo', 'UsersController@newCustomer');
  Route::post('/usuarios/nuevo', 'UsersController@saveCustomer');
  Route::get('/see-contrato/{id}/{type}', 'CustomerController@seeContracts');
  Route::post('/usuarios/remove-contrato', 'CustomerController@rmContracts');
  Route::post('/usuarios/linkContratos', 'UsersController@getLinkContracts');

  /* Encuestas */
  Route::post('/clientes/setNutri', 'PollController@setEncNutri_Admin');
  Route::post('/clientes/autosaveNutri', 'PollController@autosaveNutri');
  Route::get('/ver-encuesta/{token}/{control}', 'PollController@seeEncuestaNutri');
  Route::get('/editar-encuesta/{uID}', 'PollController@editEncuestaNutri');
  Route::post('/clearEncuesta', 'PollController@clearEncuestaNutri');
  Route::post('/sendEncuesta', 'PollController@sendEncuestaNutri');
  Route::post('/sendClinicHist', 'PollController@sendClinicHist');
  Route::get('/ver-historia-clinica/{token}/{control}', 'PollController@seeClinicHist');
  Route::get('/editar-historia-clinica/{uID}', 'PollController@editClinicHist');
  Route::post('/clearClinicHist', 'PollController@clearClinicHist');
  Route::post('/autosaveClinicHist', 'PollController@autosaveClinicHist');
  Route::post('/setClinicHist', 'PollController@setCliHistory_Admin');
  
  Route::post('/sendClinicHistSPelv', 'PollController@sendClinicHistSPelv');
  Route::get('/ver-historia-clinica-suelo-pelvico/{token}/{control}', 'PollController@seeClinicHistSPelv');
  Route::get('/editar-historia-clinica-suelo-pelvico/{uID}', 'PollController@editClinicHistSPelv');
  Route::post('/clearClinicHistSPelv', 'PollController@clearClinicHistSPelv');
  Route::post('/autosaveClinicHistSPelv', 'PollController@autosaveClinicHistSPelv');
  Route::post('/setClinicHistSPelv', 'PollController@setCliHistorySPelv_Admin');
  
  
  
  
  
  /* Citas */
  Route::post('/citas/checkDisp', 'DatesController@checkDateDisp');
  Route::post('/citas/checkDispCoaches', 'DatesController@checkDispCoaches');
  //Route::get('/citas/checkDispCoaches', 'DatesController@checkDispCoaches');
  Route::get('/citas/duplicar/{id}', 'DatesController@cloneDates');
  Route::post('/citas/duplicar/{id}', 'DatesController@cloneDatesSave');
  Route::get('/citas/bloqueo-horarios/{type}', 'DatesController@blockDates');
  Route::post('/citas/bloqueo-horarios', 'DatesController@blockDatesSave');

  Route::get('/citas/create/createSchedules', 'DatesController@createSchedules');
  Route::get('/citas/delete/{id}', 'DatesController@delete');
  Route::post('/citas/create', 'DatesController@create');
  Route::post('/citas/createAdvanced', 'DatesController@createAdvanced');
  Route::post('/citas/chargeAdvanced', 'DatesController@chargeAdvanced');
  Route::get('/citas/charge', 'DatesController@chargeDate');
  Route::get('/clientes/cobro-cita/{id}', 'DatesController@openChargeDate');
  /* Citas fisioterapia */
  Route::get('/citas-fisioterapia/listado/{coach?}/{type?}', 'FisioController@listado');
  Route::get('/citas-fisioterapia/create/{date?}/{time?}', 'FisioController@create');
  Route::get('/citas-fisioterapia/informe/{id}', 'FisioController@informe');
  Route::get('/citas-fisioterapia/edit/{id}', 'FisioController@edit');
  Route::get('/citas-fisioterapia/{month?}/{coach?}/{type?}', 'FisioController@index');
  Route::get('/citas-fisioterapia-week/{week?}/{coach?}/{type?}', 'FisioController@indexWeek');
  Route::post('/toggleEcogr', 'FisioController@toggleEcogr');
  Route::post('/toggleIndiba', 'FisioController@toggleIndiba');
  /* Citas Nutrición */
  Route::get('/citas-nutricion/listado/{coach?}/{type?}', 'NutriController@listado');
  Route::get('/citas-nutricion/create/{date?}/{time?}', 'NutriController@create');
  Route::get('/citas-nutricion/informe-nutricion/{id}', 'NutriController@informe');
  Route::post('/nutricion/nutri/upload', 'NutriController@uploadFile');
  Route::get('/citas-nutricion/edit/{id}', 'NutriController@edit');
  Route::get('/citas-nutricion/{month?}/{coach?}/{type?}', 'NutriController@index');
  Route::get('/citas-nutricion-week/{week?}/{coach?}/{type?}', 'NutriController@indexWeek');
  /* Citas personalTrainer */
  Route::get('/citas-pt/listado/{coach?}/{type?}', 'PTController@listado');
  Route::get('/citas-pt/create/{date?}/{time?}', 'PTController@create');
  Route::get('/citas-pt/edit/{id}', 'PTController@edit');
  Route::get('/citas-pt/{month?}/{coach?}/{type?}', 'PTController@index');
  Route::get('/citas-pt-week/{week?}/{coach?}/{type?}', 'PTController@indexWeek');
   /* Citas estetica */
   Route::get('/citas-estetica/listado/{coach?}/{type?}', 'EstheticController@listado');
   Route::get('/citas-estetica/create/{date?}/{time?}', 'EstheticController@create');
   Route::get('/citas-estetica/informe/{id}', 'EstheticController@informe');
   Route::get('/citas-estetica/edit/{id}', 'EstheticController@edit');
   Route::get('/citas-estetica/{month?}/{coach?}/{type?}', 'EstheticController@index');
   Route::get('/citas-estetica-week/{week?}/{coach?}/{type?}', 'EstheticController@indexWeek');
   Route::post('/toggle/esthetic', 'EstheticController@toggleEquipment');

  //Facturas
  Route::get('/facturas/ver/{id}', 'InvoicesController@view')->name('invoice.view');
  Route::get('/facturas/nueva/{id}', 'InvoicesController@create')->name('invoice.create');
  Route::get('/facturas/editar/{id}', 'InvoicesController@update')->name('invoice.edit');
  Route::post('/facturas/guardar', 'InvoicesController@save')->name('invoice.save');
  Route::post('/facturas/enviar', 'InvoicesController@sendMail')->name('invoice.sendmail');
  Route::get('/facturas/modal/editar/{charge}/{id?}', 'InvoicesController@update_modal')->name('invoice.updModal');
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
  Route::post('/send/cobro-bono', 'ChargesController@sendCobroBono');
  Route::post('/send/cobro-gral', 'ChargesController@sendCobroGral');
  Route::post('/cobros/cobrar', 'ChargesController@cobrar');
  Route::post('/cobros/cobrar/{id}', 'ChargesController@updateCharge');
  Route::get('/cobros/getPriceTax', 'ChargesController@getPriceTax');
  Route::post('/cobros/cobrar-usuario', 'ChargesController@chargeUser');
  Route::get('/usuarios/cobrar/tarifa/', 'UsersController@rateCharge');
  
    /* Tarifas */
  Route::get('/tarifas/listado', 'RatesController@index');
  Route::get('/tarifas/new', 'RatesController@newRate');
  Route::post('/tarifas/create', 'RatesController@create');
  Route::get('/tarifas/actualizar/{id}', 'RatesController@actualizar');
  Route::get('/tarifas/update', 'RatesController@update');
  Route::post('/tarifas/upd_fidelity', 'RatesController@upd_fidelity');
  Route::get('/tarifas/delete/{id}', 'RatesController@delete');
  Route::get('/tarifas/stripe/{id}', 'RatesController@createStripe');
  Route::get('/rates/unassigned/{idUserRate}', 'RatesController@unassignedRate');

  /* Bonos */
  Route::get('/bonos/listado', 'BonosController@index');
  Route::post('/bonos/create', 'BonosController@create');
  Route::get('/bonos/update', 'BonosController@update');
  Route::post('/bonos/upd_fidelity', 'BonosController@upd_fidelity');
  Route::get('/bonos/delete/{id}', 'BonosController@delete');
  Route::get('/bonos/sharedBono/{id}/{serv_id}', 'BonosController@sharedBono');
  Route::get('/bonos/sharedBono-get/{id}/{serv_id}', 'BonosController@sharedBono_getlst');
  Route::get('/bonos/comprar/{uid}/{type?}/{id?}', 'BonosController@show_purcharse');
  Route::post('/bonos/comprar', 'BonosController@purcharse');
  Route::post('/bonos/sharedBono', 'BonosController@sharedBono_save');
  Route::get('/bonologs/{id}', 'BonosController@printBonologs');
  Route::post('/bonos/updCant', 'BonosController@updBonologs');
  Route::get('/bonos-clientes', 'BonosController@getByUsers');
  Route::get('/bonos/{type}/{id}', 'BonosController@disableEnable');

  /* informes */
  Route::get('/informes/cajaDiaria', 'InformesController@informeCajaDiaria');
  
  /* Entrenadores */
  Route::get('/horariosEntrenador/{id?}', 'UsersController@horarios');
  Route::post('/horariosEntrenador', 'UsersController@updHorarios');
  
  /* CAJA */
  Route::get('/caja-diaria/{month?}/{day?}', 'CashBoxsController@getToUser');
  Route::post('/caja-diaria-cierre', 'CashBoxsController@close');
  Route::post('/gastos/create', 'ExpensesController@create');

  Route::get('', function () {
    return redirect('admin/clientes');
  });
});

Route::group(['middleware' => ['admin'], 'prefix' => 'admin'], function () {

  /* Ingresos  rutas basicas */
  Route::get('/nuevo/ingreso', 'IncomesController@nuevo');
  Route::post('/ingresos/create', 'IncomesController@create');
  Route::get('/ingresos/{date?}', 'IncomesController@index');
  Route::get('/ingreso-by-rate/{rateID}', 'IncomesController@byRate');
  Route::get('/ingresos', 'IncomesController@index');

  /* Gastos  rutas basicas */

  Route::post('/gastos/importar', 'ExpensesController@gastos_import');
  Route::post('/gastos/gastosLst', 'ExpensesController@getTableGastos');
  Route::post('/gastos/update', 'ExpensesController@updateGasto');
  Route::get('/gastos/getHojaGastosByRoom/{year?}/{id}', 'ExpensesController@getHojaGastosByRoom');
  Route::get('/gastos/containerTableExpensesByRoom/{year?}/{id}', 'ExpensesController@getTableExpensesByRoom');
  Route::post('/gastos/del', 'ExpensesController@gastosDel');
  Route::get('/gastos-by-byType/{typeID}', 'ExpensesController@byType');
  Route::get('/gastos/{year?}', 'ExpensesController@gastos');

  Route::get('/perdidas-ganancias', 'PyGController@index');

  /* distr-beneficios */
  Route::get('/distr-beneficios', 'PyGController@distrBeneficios');
  Route::post('/distr-beneficios/create', 'PyGController@distrBeneficiosStore');
  Route::post('/distr-beneficios/delete', 'PyGController@distrBeneficiosDelete');

  /* Gastos */
  Route::get('/gastos', 'ExpensesController@index');
  Route::post('/gastos/import/csv/', 'ExpensesController@importCsv');

  /* informes */
  Route::get('/informes/caja/{month?}/{day?}', 'InformesController@informeCajaMes');
  Route::get('/informes/cuotas-mes/{month?}/{day?}', 'InformesController@informeCuotaMes');
  Route::get('/informes/cobros-mes/{month?}/{day?}', 'InformesController@informeCobrosMes');
  Route::get('informes/cliente-mes/{month?}/{month2?}/{f_rate?}/{f_method?}/{f_coach?}', 'InformesController@informeClienteMes');
  Route::get('informes/servicios-mes/{f_month?}/{f_rate?}/{f_method?}/{f_coach?}', 'InformesController@informeServiciosMes');
  Route::post('/informes/search/{month?}', 'InformesController@searchClientInform');
  Route::get('/informes/cierre-diario/{month?}/{day?}', 'InformesController@informeCierreDiario');

  /* Entrenadores */
  Route::get('/sendEmail/trainer/{id}', 'UsersController@sendEmailEntrenadores');
  Route::get('/actualizarEntrenador/{id}', 'UsersController@updEntrenador');
  Route::get('/paymentsEntrenador/{id}', 'CoachLiquidationController@paymentsEntrenador');
  Route::get('/liquidacion-Entrenador/{id}/{date?}', 'CoachLiquidationController@liquidEntrenador');
  Route::get('/enviar-liquidacion-Entrenador/{id}/{date?}', 'CoachLiquidationController@enviarEmailLiquidacion');
  Route::get('/imprimir-liquidacion-Entrenador/{id}/{date?}', 'CoachLiquidationController@printEmailLiquidacion');
  Route::post('/payment-Entrenador/', 'CoachLiquidationController@store');
  Route::get('/entrenadores/liquidacion-entrenador/', 'CoachLiquidationController@coachLiquidation');
  Route::get('/entrenadores/{type?}', 'UsersController@entrenadores');

  /* Usuarios */
  Route::get('/usuarios', 'UsersController@index');
  Route::get('/usuarios/new/{role?}', 'UsersController@nueva');
  Route::post('/usuarios/create', 'UsersController@create');
  Route::get('/usuarios/actualizar/{id}', 'UsersController@actualizar');
  Route::any('/usuarios/delete/{id}', 'UsersController@delete');
  Route::get('/usuarios/actualizarUsuario/{id}', 'UsersController@actualizarUsuario');
  Route::post('/usuarios/update', 'UsersController@update');
  Route::post('/usuarios/updateColor', 'UsersController@updateColor');
  Route::post('usuarios/newInforme', 'UsersController@newInforme');

  /* Facturacion */
  Route::get('/facturacion/entrenadores', 'UsersController@entrenadores');
  
  
  Route::get('/manual/bonos', 'ManualController@bonos');
  Route::get('/manual/citas', 'ManualController@citas');
  
  /* Text emails */
  Route::get('/settings_msgs/{key?}', 'SettingsController@messages')->name('settings.msgs');
  Route::post('/settings_msgs/{key?}', 'SettingsController@messages_upd')->name('settings.msgs.upd');

  Route::get('/importarRegistro', 'FunctionalControler@importarRegistro');

  /* departamento */
  Route::get('/dpto/contabilidad/{dpto?}', 'DptoController@perdidas_ganacias'); // 'PyGController@index');
  
});

Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {
  Route::get('/userRates', 'FunctionalControler@userRates');
  Route::post('/userRates', 'FunctionalControler@save_userRates');
});

//Route::get('/checkcrom/{command}/{param}', function ($command, $param) {
//  $artisan = \Artisan::call($command . ":" . $param);
//  $output = \Artisan::output();
//  return $output;
//}); //->middleware('admin');

  
Route::group(['middleware' => 'superAdmin'], function () {
  Route::get('/control-contabilidad', 'ControlsControler@contabilidad');
});
Route::group(['middleware' => 'admin'], function () {
  Route::get('/departamento/gastos-by-byType/{typeID}', 'DptoController@ExpensesbyType');
});

Route::group(['middleware' => ['Administrativo'], 'prefix' => 'departamento'], function () {
  Route::get('informes-cliente/{month?}/{f_rate?}/{f_method?}/{f_coach?}', 'DptoController@informeClienteMes'); //'InformesController@informeClienteMes');
  Route::get('/contabilidad', 'DptoController@perdidas_ganacias'); // 'PyGController@index');
  Route::get('/gastos-by-byType/{typeID}', 'DptoController@ExpensesbyType');
});