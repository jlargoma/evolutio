<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;

class UsersExport implements FromCollection {

  public function collection() {

    global $filterStatus;

    $array_excel = [];
    $array_excel[] = [
        'Nombre',
        'Email',
        'Telefono',
        'Estado',
        'Servicios'
    ];
    
    $aRates = \App\Models\Rates::getByTypeRate('pt')->pluck('name', 'id')->toArray();
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

    $users = $sqlUsers->where('role', 'user')->get();
    foreach ($users as $user) {
        $array_excel[] = [
            $user->name,
            $user->email,
            $user->telefono,
            $user->status ? 'ACTIVO' : 'NO ACTIVO',
            isset($aUserRates[$user->id]) ? $aUserRates[$user->id] : '-'
        ];
    }
    
    $collection = collect($array_excel);

    return $collection;
  }

}
