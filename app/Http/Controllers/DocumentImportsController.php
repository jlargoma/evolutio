<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Auth;
use App\User;
use App\Models\Entrenadores;
use URL;
use Illuminate\Support\Facades\Validator;

class DocumentImportsController extends Controller
{
  function index(){
     return view('backend.import.index');
  }
    /**
     * 
     * @param Request $request
     * @return type
     */
    function inportSales(Request $request){
      if ($request->hasFile('fileToUpload')){
         
        //BEGIN: control
        $file = $request->file('fileToUpload');
        if (strtolower($file->getClientOriginalExtension()) != 'csv'){
           return redirect()->back()->with('error','El archivo debe ser de formato CSV.');
        }
        if (strtolower($file->getMimeType()) != 'text/plain'){
           return redirect()->back()->with('error','El archivo debe ser de formato CSV.');
        }
        //END: control
        
        $obj = new \App\Models\DocumentImports();
        $path = $file->move('informe_de_ventas');
        $obj->type_file = 'vtas';
        $obj->file = $path;
        $obj->save();
        
        $total = $this->importCSVVentas($path,$obj->id);
        return redirect()->back()->with('success','Importación completada: '.$total.' Registros');
      }
      return redirect()->back()->with('error','No se pudo completar la importación');
    
    }
    /**
     * 
     * @param Request $request
     * @return type
     */
    function inportInstructor(Request $request){
      if ($request->hasFile('fileInstructor')){
         
        //BEGIN: control
        $file = $request->file('fileInstructor');
        if (strtolower($file->getClientOriginalExtension()) != 'csv'){
           return redirect()->back()->with('error','El archivo debe ser de formato CSV.');
        }
        if (strtolower($file->getMimeType()) != 'text/plain'){
           return redirect()->back()->with('error','El archivo debe ser de formato CSV.');
        }
        //END: control
        
        $obj = new \App\Models\DocumentImports();
        $path = $file->move('asistencias');
        $obj->type_file = 'instruc';
        $obj->file = $path;
        $obj->save();
        
        $total = $this->importCSVAsistencias($path,$obj->id);
        return redirect()->back()->with('success','Importación completada: '.$total.' Registros');
      }
      return redirect()->back()->with('error','No se pudo completar la importación');
    }
    
    
     private function importCSVAsistencias($file,$idDocument) {
      
    /*array:17 
    0 => "﻿"Día""
    1 => "Hora"
    2 => "Instructor"
    3 => "Clase"
    4 => "Reservas"
    5 => "Cancelaciones"
  ]*/
    $row = 1;
    $insert = [];
    $to_insert = [];
    $clases = [];
    $start = true;
    setlocale(LC_TIME, "spanish");
    if (($handle = fopen(public_path($file), "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
          if ($start){
            $start = false;
            continue;
          }
                  
          $day = isset($data[0]) ? $data[0] : null;
          
          if ($day){
            $day = convertSpanishDate($day);
          }
          $instructor = $class = null;
          $class = isset($data[3]) ? formatToImport($data[3]) : null;
          if (isset($data[2])){
            $instructor = formatToImport($data[2]);
          }
          if (isset($data[3])){
            $class = formatToImport($data[3]);
            $clases[$class] = null;
          }
          
               
          
          $aux = [
            'day' => $day,   
            'hour'  => isset($data[1]) ? formatToImport($data[1]) : null,  
            'instructor' => $instructor,   
            'class' => trim($class),   
            'numb'  => isset($data[4]) ? intval($data[4]) : null,  
            'cancels'  => isset($data[5]) ? intval($data[5]) : null,  
          ];
        
          if (isset($insert[$instructor])) $insert[$instructor][] = $aux;
          else $insert[$instructor] = [$aux];

        }
        fclose($handle);
    }

    $rateLst = $this->getRates();
    
    foreach ($insert as $inst => $data){
      /**
       * Get or Create user instuctor
       */
      $oUser = Entrenadores::where('name',$inst)->first();
      if (!$oUser){
        $oUser = new Entrenadores();
        $oUser->name = $inst;
        $oUser->email = str_replace(' ','',$inst).'@evolutio';
        $oUser->role = 'teacher';
        $oUser->password = rand().time();
        $oUser->save();
      }
      
      
      $uID = $oUser->id;
      foreach ($data as $items){
        $rID = null;
        $rPrice = 0;
        if (isset($rateLst[$items['class']])){
          $rID = $rateLst[$items['class']][0];
          $rPrice = $rateLst[$items['class']][1];
        }else{
          
          $new_rate = $this->setRates($items['class']);
          $rateLst[$items['class']] = $new_rate;
          $rID = $new_rate[0];
          $rPrice = $new_rate[1];
          dd($rID,$rPrice,$items['class'],$rateLst);
        }
        $to_insert[] = [
            'id_user' => $uID,
            'id_rate' => $rID,
            'id_document' => $idDocument,
            'name' => $inst,
            'class' => $items['class'],
            'date' => $items['day'],
            'time' => $items['hour'],
            'numb' => $items['numb'],
            'cancels' => $items['cancels'],
            'cost' => $rPrice,
            'total' => $rPrice*$items['numb'],
        ];
      }
      
    }
    
    /**
     * Insert rows
     */
    \Illuminate\Support\Facades\DB::table('coach_sessions')->insert($to_insert);
    return count($to_insert);
    //DELETE FROM `coach_sessions` WHERE `coach_sessions`.`id_document` = 41
    }
    
    private function importCSVVentas($file,$idDocument) {
      
    /*array:17 
  0 => "﻿"Emitido el""
  1 => "Pagado el"
  2 => "Concepto"
  3 => "Tipo de venta"
  4 => "Tarifa"
  5 => "Grupo de tarifas"
  6 => "Base imponible"
  7 => "% Impuestos"
  8 => "IVA Repercutido"
  9 => "Total"
  10 => "Forma de pago"
  11 => "Nombre y apellidos"
  12 => "Teléfonos"
  13 => "Fecha de alta"
  14 => "Correo electrónico"
  15 => "Forma de pago"
  16 => "Categoría"
]*/
    $row = 1;
    $insert = [];
    $start = true;
    if (($handle = fopen(public_path($file), "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
//          $aux = $fields;
          if ($start){
            $start = false;
            continue;
          }
          
          $email =  isset($data[14]) ? formatToImport($data[14]) : null;
          
                  
                  
          $emmit = isset($data[0]) ? $data[0] : null;
          if ($emmit){
            $aux = explode('/', $emmit);
            if (count($aux) == 3){
              $emmit = $aux[2].'-'.$aux[1].'-'.$aux[0];
            }
          }
          $pay = isset($data[1]) ? $data[1] : null;
          $pay_hour = '00:00';
          if ($pay){
            $aux1 = explode(' ', $pay);
            if (count($aux1) == 2){
              $aux = explode('/', $aux1[0]);
              if (count($aux) == 3){
                $pay = $aux[2].'-'.$aux[1].'-'.$aux[0];
              }
              $pay_hour = $aux1[1];
            }
          }
          
          
          $aux = [
            'name'  => isset($data[11]) ? formatToImport($data[11]) : null,  
            'phone' => isset($data[12]) ? formatToImport($data[12]) : null,  
            'email' => $email,   
            'emmit' => $emmit,   
            'pay' => $pay,   
            'pay_hour' => $pay_hour,   
            'concept' => isset($data[2]) ? formatToImport($data[2]) : null,  
            'type'    => isset($data[3]) ? formatToImport($data[3]) : null,  
            'tarifa'  => isset($data[4]) ? formatToImport($data[4]) : null,  
            'tarifa_g'=> isset($data[5]) ? formatToImport($data[5]) : null,  
            'amount'  => isset($data[9]) ? formatToImport($data[9]) : null,  
            'pay_from'=> isset($data[10]) ? formatToImport($data[10]) : null,  
            
          ];
        
          if (isset($insert[$email])) $insert[$email][] = $aux;
          else $insert[$email] = [$aux];

        }
        fclose($handle);
    }
    
    $rateLst = $this->getRates();
    
    foreach ($insert as $email => $data){
    
      /**
       * Get or Create user
       */
      $oUser = Entrenadores::where('email',$email)->first();
      if (!$oUser){
        $oUser = new Entrenadores();
        $oUser->name = $data[0]['name'];
        $oUser->telefono = $data[0]['phone'];
        $oUser->email = $email;
        $oUser->role = 'user';
        $oUser->password = rand().time();
        $oUser->save();
        
      }
      $uID = $oUser->id;
      foreach ($data as $items){
        $rID = null;
        $rPrice = 0;
        if (isset($rateLst[$items['tarifa_g']])){
          $rID = $rateLst[$items['tarifa_g']][0];
        }
        
        $to_insert[] = [
            'id_user' => $uID,
            'id_rate' => $rID,
            'id_document' => $idDocument,
            'name' => $items['name'],
            'class' => $items['tarifa'],
            'date_emmit' => $items['emmit'],
            'date_pay' => $items['pay'],
            'time_pay' => $items['pay_hour'],
            'concept' => $items['concept'],
            'type' => $items['type'],
            'tarifa' => $items['tarifa'],
            'tarifa_g' => $items['tarifa_g'],
            'pay_from' => $items['pay_from'],
            'total' => floatval($items['amount']),
        ];
      }
      
    }
  
    /**
     * Insert rows
     */
    \Illuminate\Support\Facades\DB::table('sales')->insert($to_insert);
    return count($to_insert);
    }
    
    
    private function getRates() {
      $rates = \App\Models\Rates::all();
      $rateLst = [];
      foreach ($rates as $i){
        $rateLst[$i->name] = [$i->id,$i->cost];
      }
      
      return $rateLst;
    }
    
    private function setRates($name) {
      $rate = new \App\Models\Rates();
      $rate->name = trim($name);
      $rate->price = 99;
      $rate->cost = 99;
      $rate->max_pax = 99;
      $rate->type = 1;
      $rate->mode = 1;
      $rate->status = 1;
      $rate->order = 0;
      
      $rate->save();
      return [$rate->id,$rate->cost];
    }
   
}