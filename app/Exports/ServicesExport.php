<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use App\Models\Bonos;

class ServicesExport implements FromCollection
{

  public function collection()
  {

    global $filterStatus;

    $array_excel = [];
    $array_excel[] = [
      'Cliente',
      'Servicio',
      'Familia',
      'Coach',
      'Mes',
      'Valor',
      'Cobrado',
      'descuento',
      'Fecha Cobro',
      'Tipo Cobro',
      'Nota',
    ];


    $year = getYearActive();

    $sql = 'SELECT UR.id_user,UR.id_rate,UR.rate_month,UR.id_appointment,UR.coach_id,UR.price,UR.tarifa, CH.date_payment,CH.type_payment,CH.import,CH.discount,CH.bono_id,CH.note
    FROM `users_rates` as UR 
    LEFT JOIN `charges` as CH
    ON UR.id_charges = CH.id 
    WHERE `rate_year` = 2023 AND UR.`deleted_at` is null AND CH.`deleted_at` is null';

    $uRates = DB::select($sql);
    $aCoachs = User::getCoachs()->pluck('name', 'id');
    $aCustomers = User::pluck('name', 'id')->toArray();
    $oRates = \App\Models\Rates::all();
    $aBonos = Bonos::all()->pluck('name','id');
    $aRType = \App\Models\TypesRate::all()->pluck('name', 'id')->toArray();
    $aRates = [];
    $aMonths = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sept', 'Oct', 'Nov', 'Dic'];
    $subfamily = \App\Models\TypesRate::subfamily();
    foreach ($oRates as $r) {
      $aux = isset($subfamily[$r->subfamily]) ? $subfamily[$r->subfamily] : '';
      $aRates[$r->id] = [$r->name, $aux];
    }
    foreach ($uRates as $ur) {
      $rateAux = isset($aRates[$ur->id_rate]) ? $aRates[$ur->id_rate] : [' -- ', '--'];
      $array_excel[] = [
        (isset($aCustomers[$ur->id_user]) ? $aCustomers[$ur->id_user] : ' -- '),
        $rateAux[0],
        $rateAux[1],
        (isset($aCoachs[$ur->coach_id]) ? $aCoachs[$ur->coach_id] : ' -- '),
        $aMonths[$ur->rate_month],
        $ur->price,
        $ur->import,
        $ur->discount,
        $ur->date_payment,
        $ur->type_payment,
        $ur->note
      ];
    }


    //BONOS

    
    $sql = 'SELECT `bonos`.*,CH.id_user,CH.date_payment,CH.type_payment,CH.import,CH.discount,CH.bono_id,CH.note 
    FROM `charges` AS CH INNER JOIN `bonos` ON `bonos`.`id` = CH.`bono_id` 
    WHERE year(`date_payment`) = 2023 AND CH.`deleted_at` is null;';
    $oLstBonos = DB::select($sql);

    foreach($oLstBonos as $ur){
      $family = null;
      if($ur->rate_subf){
        $family = isset($subfamily[$ur->rate_subf]) ? $subfamily[$ur->rate_subf] : '';
      }
      if($ur->rate_type){
        $family = isset($aRType[$ur->rate_type]) ? $aRType[$ur->rate_type] : '';
      }
      $month = intVal(substr($ur->date_payment,5,2));
      $array_excel[] = [
      (isset($aCustomers[$ur->id_user]) ? $aCustomers[$ur->id_user] : ' -- '),
      (isset($aBonos[$ur->bono_id]) ? $aBonos[$ur->bono_id] : ' -- '),
      $family,
      ' -- ',
      $aMonths[$month],
      $ur->price,
      $ur->import,
      $ur->discount,
      $ur->date_payment,
      $ur->type_payment,
      $ur->note
      ];

    }



    $collection = collect($array_excel);

    return $collection;
  }
}
