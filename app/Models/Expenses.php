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
        'publicidad' => 'MARKETING Y PUBLICIDAD',
        'suministros' => 'SUMINISTROS',
        'varios' => 'VARIOS',
    ];
  }
  static function getTypesGroup(){
    return [
            'names'=> [
              'comisiones' => 'COMSIONES',
              'otros' => 'RESTO GASTOS',
              'suministros' => 'SUMINISTROS',
              'impuestos' => 'IMPUESTOS',
              'varios' => 'VARIOS',
            ],
            'groups' => [
                'comisiones' => 'comisiones',
                'equip_deco' => 'comisiones',
                'bancario' => 'comisiones',
                'comision_tpv' => 'comisiones',
                'suministros'=> 'suministros',
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
"SUELDOS Y SEG SOCIAL 
% COMISION GYM
% COMISION FISIO
% COMISION COMERCIALES
ALQUILER NAVE  Y COMUNIDAD
SUMINISTROS
RENTING FISIOTERAPIA
GTO MAT. FISIO
GTO MAT DEPORTIVO
GTO BANCARIOS
GTO TARJETA VISA
DECORACION
SOFTWARE
GESTORÍA
SEGUROS  
GASTOS REPRESENTACION
MARKETING Y PUBLICIDAD
MENSAJERIA
OTROS"
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
