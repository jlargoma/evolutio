<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
  
  protected $table = 'expenses';
  
  static function getTypes(){
    return [
        'alquiler_nave_y_comunidad' => 'ALQUILER NAVE Y COMUNIDAD',
        'decoracion' => 'DECORACION',
        'gastos_representacion' => 'GASTOS REPRESENTACION',
        'gestorÍa' => 'GESTORÍA',
        'gto_bancarios' => 'GTO BANCARIOS',
        'gto_mat_deportivo' => 'GTO MAT DEPORTIVO',
        'gto_mat_fisio' => 'GTO MAT. FISIO',
        'gto_tarjeta_visa' => 'GTO TARJETA VISA',
        'marketing_y_publicidad' => 'MARKETING Y PUBLICIDAD',
        'mensajeria' => 'MENSAJERIA',
        'otros' => 'OTROS',
        'renting_fisioterapia' => 'RENTING FISIOTERAPIA',
        'seguros' => 'SEGUROS',
        'software' => 'SOFTWARE',
        'suministros' => 'SUMINISTROS',
    ];
  }
  static function getTypesGroup(){
    return [
            'names'=> [
              'alquiler' => 'ALQUILER',
              'comisiones' => 'COMSIONES',
              'impuestos' => 'IMPUESTOS',
              'suministros' => 'SUMINISTROS',
              'servicios' => 'SERVICIOS',
              'otros' => 'VARIOS',
            ],
            'groups' => [
                'alquiler_nave_y_comunidad' => 'alquiler',
                'decoracion' => 'otros',
                'gastos_representacion' => 'servicios',
                'gestorÍa' => 'servicios',
                'gto_bancarios' => 'comisiones',
                'gto_mat_deportivo' => 'suministros',
                'gto_mat_fisio' => 'suministros',
                'gto_tarjeta_visa' => 'comisiones',
                'marketing_y_publicidad' => 'otros',
                'mensajeria' => 'servicios',
                'otros' => 'otros',
                'renting_fisioterapia' => 'alquiler',
                'seguros' => 'servicios',
                'software' => 'servicios',
                'suministros' => 'suministros',
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
'ALQUILER NAVE Y COMUNIDAD','SUMINISTROS','RENTING FISIOTERAPIA','GTO MAT. FISIO','GTO MAT DEPORTIVO','GTO BANCARIOS','GTO TARJETA VISA','DECORACION','SOFTWARE','GESTORÍA','SEGUROS','GASTOS REPRESENTACION','MARKETING Y PUBLICIDAD','MENSAJERIA','OTROS'
    ];
    
//    $aux = [];
//    foreach ($types as $k=>$v){
//      $aux[] = $k;
//    }
    
    sort($types);
    
    foreach ($types as $v){
      echo "'". strtolower(str_replace(' ','_', $v))."' => '".strtolower(str_replace(' ','_', $v))."',<br/>";
    }
    die;
  }
  
  
  
  static function getByTypes(){
    $types = self::getTypes();
    $groups = self::getTypesGroup();
    
    $n = $groups['names'];
    $g = $groups['groups'];
//    sort($n);
    foreach ($n as $k=>$v){
      echo "<h1>$v:</h1>";
      foreach ($g as $k1=>$v1){
        if ($v1 == $k){
          foreach ($types as $k2=>$v2){
            if ($k1 == $k2){
              echo '&nbsp&nbsp   - '.ucfirst(strtolower($v2)).'<br>';
            }
          }
        }
      }
    }
    die;
  }
    
}
