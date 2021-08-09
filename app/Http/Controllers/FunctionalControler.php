<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Models\User;
use App\Models\Rates;
use App\Models\TypesRate;

class FunctionalControler extends Controller {

  function userRates() {

    $oTRates = TypesRate::all();

    $aRates = [];
    foreach ($oTRates as $i) {
      $aux = [];
      $aRates[] = [
          'lst' => $oRates = Rates::where('type', $i->id)->orderBy('name')->get(),
          'name' => $i->name
      ];
    }

    $aUsers = User::where('role', 'user')->orderBy('name')->pluck('name', 'id');

    return view('functional.user-rates', [
        'aRates' => $aRates,
        'aUsers' => $aUsers
    ]);
  }

  function dateDB($d) {
    $date = trim($d);
    $aDate = explode('/', $date);
    if (count($aDate) != 3) return null;
    return '20' . $aDate[2] . '-' . $aDate[1] . '-' . $aDate[0];
  }

  
  function importarRegistro() {
    
    $archivo = fopen(public_path() . "/docs/bonos.csv", "r");
    $data = [];
    $start = true;

    $bono_sp = \App\Models\Bonos::find(9);
    $bono_fisio = \App\Models\Bonos::find(8);
    $bono_f10 = \App\Models\Bonos::find(11);

    $oServ = new \App\Services\BonoService();

    while (($datos = fgetcsv($archivo, ",")) == true) {
      if (empty($datos[0])){
        continue;
      }
      $create = $this->dateDB($datos[0]);
      $name = trim($datos[2]);

      switch ($datos[3]) {
        case 'sp':
          $oBono = $bono_sp;
          break;
        case 'fisio':
          $oBono = $bono_fisio;
          break;
        case 'f10':
          $oBono = $bono_f10;
          break;
      }
      switch ($datos[5]) {
        case 't':
          $tpay = 'card';
          break;
        case 'b':
          $tpay = 'banco';
          break;
        case 'e':
          $tpay = 'cash';
          break;
      }

      $oUser = User::where('status', 1)->where('name', $name)->first();
      if (!$oUser) {
        die($name);
      }
      
      $dates = [];
      for($i=6;$i<15;$i++){
        $aux = $this->dateDB($datos[$i]);
        if ($aux) $dates[] = $aux;
      }
    
//      var_dump($name,$dates);
      

      $resp = $oServ->asignBonoAuto($oUser, $oBono, $tpay,$create,$datos[4],$dates);
      echo $oUser->id.', '.$name.','.$oBono->name.'<br>';
    }
  }

  function importarRegistro2() {
    $v = [
        'NAOMI PALACIOS', 'SILVIA DEL AMO', 'ANA CUERVO RODRIGUEZ',
        'BEATRIZ LAGOS PANTOJA', 'ANDREA MARQUES GARCIA', 'AINHOA BARROS',
        'PILAR RIVAS VARGAS', 'ALMUDENA PEREZ DE CARLOS', 'AGUSTINA MORENO',
        'SONIA MUÑOZ BENITEZ', 'ANA MEDINA SANCHEZ MEDALLON', 'ANDREA DIEZ GIL',
        'mada diaz', 'RUTH SANCHEZ CABEZUDO', 'LAURA BLANCO MONTERO', 'MARTA RODRIGO',
        'ainhoa gil', 'ana galvez', 'Sandra Rubio Corgo', 'MARIA DE LOS ANGELES MORALES',
        'sara carmona', 'MONICA GARCÍA', 'CECILIA SESMA NAVARRO',
        'ana maldonado lopez', 'ANA ALVIR', 'Almudena Rubio Calero',
        'ANA NUÑEZ CASAS', 'VICTORIA BENITO DANES', 'Sara Galán', 'jose vela nuñez'];
    $v = ['ANGELES RODRIGUEZ', 'LUCIA MASO RUBIO', 'SERGIO SANCHEZ DE TORRES', 'NURIA SEVILLA', 'MONICA HERNANDEZ', 'JAVIER RODRIGUEZ', 'JUAN JOSE RUIZ DEL CASTILLO', 'noelia pinilla', 'javier morcillo', 'PILAR PEREZ CASTILLO', 'rosa alfaro', 'ANDREA COTOBAL GONZALEZ', 'OSCAR ALISES SERRANO', 'Marta Ferrer López', 'LETICIA ALADREN', 'MARIA LUZ LOPEZ', 'RUTH ZAMORANO', 'JUAN JOSE SANCHEZ', 'MIGUEL MALDONADO LOPEZ', 'ANA CUERVO RODRIGUEZ', 'Sandra Campillo Sánchez', 'SILVIA DEL AMO', 'Mar Tercero Cortés'];
    $v = ['ESTHER VEGA', 'ISA RASPEÑO'];

    $v = array_map('strtolower', $v);
    $result = [];
    $user = User::where('status', 1)->whereIn('name', $v)->pluck('name', 'id');
//    dd($user);
    foreach ($user as $id => $name) {
      $n = strtolower($name);
      $k = array_search($n, $v);
      if ($k > -1) {
        unset($v[$k]);
      } else {
        echo '-' . $n . '<br>';
      }
    }
    foreach ($v as $id => $name) {
      echo $name . '<br>';
    }
    echo count($v);
    die;
  }

}
