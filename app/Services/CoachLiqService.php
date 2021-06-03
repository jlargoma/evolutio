<?php

namespace App\Services;

use App\Models\CoachLiquidation;
use App\Models\User;

class CoachLiqService {

  static function liqByMonths($year,$type=null) {
    
    $months = lstMonthsSpanish();
    unset($months[0]);
    $sql = User::whereIn('role', [
                'teach',
                'teacher',
                'fisio',
                'nutri'
    ]);
    if ($type == 'activos')
      $sql->where('status', 1);
    if ($type == 'desactivados')
      $sql->where('status', 0);
    
    $users = $sql->orderBy('status', 'DESC')->get();
    $aLiq = [];
    $aLiqTotal = [];
    $oLiquidations = CoachLiquidation::whereYear('date_liquidation', '=', $year)->get();
    if ($oLiquidations) {
      $aux = [];
      for ($i = 1; $i < 13; $i++)
        $aux[$i] = [0 => -1, 1 => 0];

      foreach ($oLiquidations as $liq) {
        if (!isset($aLiq[$liq->id_coach]))
          $aLiq[$liq->id_coach] = $aux;

        $aLiq[$liq->id_coach][date('n', strtotime($liq->date_liquidation))] = [$liq->id, intval($liq->total)];
        if (!isset($aLiqTotal[$liq->id_coach]))
          $aLiqTotal[$liq->id_coach] = 0;
        $aLiqTotal[$liq->id_coach] += intval($liq->total);
      }
    }

    return [
        'months' => $months,
        'year' => $year,
        'users' => $users,
        'aLiq' => $aLiq,
        'aLiqTotal' => $aLiqTotal,
    ];
  }

}
