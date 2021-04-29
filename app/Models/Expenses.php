<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
  
  protected $table = 'expenses';
  
  static function getTypes(){
    return [
        'comisiones' => 'COMISIONES COMERCIALES',
        'comision_tpv' => 'COMSION TPV',
        'equip_deco' => 'EQUIPACION Y DECORACION',
        'bancario' => 'GASTOS BANCARIOS',
        'impuestos' => 'IMPUESTOS',
        'limpieza' => 'LIMPIEZA',
        'publicidad' => 'MARKETING Y PUBLICIDAD',
        'seg_social' => 'SEG SOCIALES',
        'serv_prof' => 'SERVICIOS PROF INDEPENDIENTES',
        'suministros' => 'SUMINISTROS',
        'varios' => 'VARIOS',
    ];
  }
  static function getTypesGroup(){
    return [
            'names'=> [
              'alquiler' => 'ALQUILER INMUEBLES',
              'comisiones' => 'COMSIONES',
              'limpieza' => 'LAVANDERIA Y LIMPIEZA',
              'otros' => 'RESTO GASTOS',
              'empleados' => 'EMPLEADOS',
              'suministros' => 'SUMINISTROS',
              'impuestos' => 'IMPUESTOS',
              'varios' => 'VARIOS',
            ],
            'groups' => [
                'alquiler' => 'alquiler',
                'comisiones' => 'comisiones',
                'equip_deco' => 'comisiones',
                'bancario' => 'comisiones',
                'comision_tpv' => 'comisiones',
                'lavanderia' => 'limpieza',
                'limpieza'   => 'limpieza',
                'seg_social' => 'empleados',
                'serv_prof' => 'empleados',
                'suministros'=> 'suministros',
                'equip_deco'=> 'suministros',
                'varios'=> 'varios',
                'publicidad'=> 'varios',
                'otros'=> 'otros',
                'impuestos'=> 'impuestos',
            ]];
        
  }
  
  //Para poner nombre al tipo de cobro//
  static function getTypeCobro($typePayment=NULL) {
    $array = [
        0 => "Tarjeta visa",//"Metalico Jorge",
        2 => "CASH",// "Metalico Jaime",
        3 => "Banco",//"Banco Jorge",
    ];

    if (!is_null($typePayment)) return $typePayment = $array[$typePayment];
    
    return $array;
  }
    
    
  static function getTypesOrderned(){
    $types =  [
      'alquiler'=>"ALQUILER INMUEBLES",
      'comisiones'=>"COMISIONES COMERCIALES", // COMISIONES COMERCIALES</option>
      'comision_tpv'=>"COMSION TPV",
      'equip_deco' => 'EQUIPACION Y DECORACION',
//      'decoracion'=>"DECORACIóN", // DECORACION</option>
//      'equi_vivienda'=>"EQUIPAMIENTO VIVIENDA", // EQUIPAMIENTO VIVIENDA</option>
      'bancario'=>"GASTOS BANCARIOS", // GASTOS BANCARIOS</option>
      'impuestos'=>"IMPUESTOS", // IMPUESTOS</option>
      'lavanderia'=>"LAVANDERIA", // LAVANDERIA</option>
      'limpieza'=>"LIMPIEZA", // LIMPIEZA</option>
      'publicidad'=>"MARKETING Y PUBLICIDAD", // MARKETING Y PUBLICIDAD</option>
//      'mensaje'=>"MENSAJERIA", // MENAJE</option>
      'prop_pay'=>"PAGO PROPIETARIOS", //PAGO PROPIETARIO</option>
      'regalo_bienv'=>"AMENITIES", // REGALO BIENVENIDA</option>
      'mantenimiento'=>"REPARACION Y CONSERVACION", // REPARACION Y CONSERVACION</option>
      'sabana_toalla'=>"TEXTIL Y  MENAJE", // SABANAS Y TOALLAS</option>
      'seg_social'=>"SEG SOCIALES", // SEG SOCIALES</option>
      'serv_prof'=>"SERVICIOS PROF INDEPENDIENTES", // SERVICIOS PROF INDEPENDIENTES</option>
      'sueldos'=>"SUELDOS Y SALARIOS", // SUELDOS Y SALARIOS</option>
      'suministros'=>"SUMINISTROS", 
      'seguros'=>"PRIMAS SEGUROS", 
      'representacion'=>"GASTOS REPRESENTACION", 
      'amenities'=>"AMENITIES", 
      'varios'=>"VARIOS", // VARIOS</option>
      'excursion' => 'PROVEEDORES EXCURSIÓN',
        
    ];
    
    $aux = [];
    foreach ($types as $k=>$v){
      $aux[] = $k;
    }
    
    sort($aux);
    
    foreach ($aux as $k=>$v){
      echo "'".$v."' => '".$types[$v]."',<br/>";
    }
    die;
  }
  
    
}
