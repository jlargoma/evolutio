<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DistribBenef extends Model
{
  
  protected $table = 'distrib_benef';
  
  static function getConcepto(){
    return [
      'inver_evolutio' => 'INVERSION EVOLUTIO',
      'repart_carlos' => 'REPART BENEF CARLOS',
      'repart_jorge' => 'REPART BENEF JORGE',
      'repart_wiwi' => 'REPART BENEF WIWI',
  ];
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
    
    
}
