<?php

namespace App\Exports;

use App\Models\Rates;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use App\Models\TypesRate;

class UsersExport implements FromCollection {

  public function collection() {

    global $filterStatus;
    global $filterMonth;
    global $filterFamily;
    $year = getYearActive();
    
    $aRates = \App\Models\Rates::getByTypeRate('pt')->pluck('name', 'id')->toArray();
    $allRates = Rates::all()->pluck('name', 'id')->toArray();
    $oUserRates = \App\Models\UserRates::whereIn('id_rate',array_keys($aRates))->orderBy('id','desc')->get();
    $aUserRates = [];
    if ($oUserRates) {
      foreach ($oUserRates as $i) {
        if (!isset($aUserRates[$i->id_user]))
            $aUserRates[$i->id_user] = $aRates[$i->id_rate];
      }
    }
    
    $sqlUsers = null;
    if ($filterStatus == 'all') {
      $sqlUsers = User::where('role', 'user');
    } else if ($filterStatus == 'new_unsubscribeds') {
      $sqlUsers = User::altaBajas($year, $filterMonth, $filterFamily);
    } else {
      if ($filterStatus == 2){
        $uPlan = DB::table('user_meta')
                ->where('meta_key','plan')
                ->where('meta_value','fidelity')
                ->pluck('user_id');
        
        $sqlUsers = User::select('users.*')->where('role', 'user')
              ->where('status', 1)
              ->whereIn('id',$uPlan);
                
      } else {
        $sqlUsers = User::where('role', 'user')
              ->where('status', $filterStatus);
      }
    }

    if ($filterFamily && $filterStatus != 'new_unsubscribeds') {

      $lstIDs = User::usersRatesFamilyMonths($year, $filterMonth, $filterFamily);
      $sqlUsers->whereIN('users.id', $lstIDs);
    } 

    $users = $sqlUsers->where('role', 'user')->get();

    $array_excel = [];

    if($filterStatus == 'new_unsubscribeds') {

      $aUsers = [];

      foreach($users as $user){
        $aUsers[$user->id] = $user; 
      }
      

      $userData = User::altaBajasData($year, $filterMonth, $filterFamily);
      
      $rateTypes = TypesRate::all()->pluck('name', 'id')->toArray();
      
      $array_excel[] = [''];

      $array_excel[] = [
        'Período',
        lstMonths(false)[$filterMonth] . ' ' . $year
      ];

      if($filterFamily){

        $array_excel[] = [
          'Familia',
          $rateTypes[$filterFamily]
        ];

        $array_excel[] = [''];
  
        $array_excel[] = [
          'Nombre',
          'Email',
          'Telefono',
          'Estado',
          'Servicios',
          'Alta/Baja'
        ];  
  
        foreach ($userData as $userD) {
            $array_excel[] = [
              $aUsers[$userD->id_user]->name,
              $aUsers[$userD->id_user]->email,
              $aUsers[$userD->id_user]->telefono,
              $aUsers[$userD->id_user]->status ? 'ACTIVO' : 'NO ACTIVO',
              isset($allRates[$userD->id_rate]) ? $allRates[$userD->id_rate] : '-',
              isset($userD->deleted_at) ? 'BAJA' : 'ALTA'
            ];
        }
      } else {

        $ratesRateType = Rates::all()->pluck('type', 'id' ); 

        $array_excel[] = [''];
  
        $array_excel[] = [
          'Nombre',
          'Email',
          'Telefono',
          'Estado',
          'Servicios',
          'Familia',
          'Alta/Baja'
        ];  
  
        foreach ($userData as $userD) {
            $array_excel[] = [
              $aUsers[$userD->id_user]->name,
              $aUsers[$userD->id_user]->email,
              $aUsers[$userD->id_user]->telefono,
              $aUsers[$userD->id_user]->status ? 'ACTIVO' : 'NO ACTIVO',
              isset($allRates[$userD->id_rate]) ? $allRates[$userD->id_rate] : '-',
              $rateTypes[$ratesRateType[$userD->id_rate]],
              isset($userD->deleted_at) ? 'BAJA' : 'ALTA'
            ];
        }
      }

    } else {
      $array_excel[] = [
          'Nombre',
          'Email',
          'Telefono',
          'Estado',
          'Servicios'
      ];
      
      foreach ($users as $user) {
          $array_excel[] = [
              $user->name,
              $user->email,
              $user->telefono,
              $user->status ? 'ACTIVO' : 'NO ACTIVO',
              isset($aUserRates[$user->id]) ? $aUserRates[$user->id] : '-'
          ];
      }

    }
    
    
    $collection = collect($array_excel);

    return $collection;
  }

}
