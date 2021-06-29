<?php

namespace App\Services;

use App\Models\CoachLiquidation;
use App\Models\User;
use App\Models\CoachRates;
use App\Models\Dates;

class CoachLiqService {

  static function liqByMonths($year, $type = null) {

    $aLiq = $CommLst = $liqLst = $aLiqTotal = [];
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

    $aux = [];
    for ($i = 1; $i < 13; $i++)
      $aux[$i] = 0;

    foreach ($users as $u) {
      $aLiqTotal[$u->id] = 0;
      $liqLst[$u->id] = $aux;
      $CommLst[$u->id] = $aux;
      $aLiq[$u->id] = $aux;
    }
    //---------------------------------------------------------------//
    // Get Saved liquidations
    $oLiquidations = CoachLiquidation::whereYear('date_liquidation', '=', $year)->get();
    if ($oLiquidations) {


      foreach ($oLiquidations as $liq) {
        $aux2 = intval(substr($liq->date_liquidation, 5, 2));
        if ($liq->salary)
          $liqLst[$liq->id_coach][$aux2] = $liq->salary;
        $CommLst[$liq->id_coach][$aux2] = $liq->commision;
      }
    }

    //---------------------------------------------------------------//
    // calculate commision
    $now = date('m');
    $sCoachLiq = new CoachLiqService();
    foreach ($users as $u) {
      $uID = $u->id;
      foreach ($months as $k => $v) {
        if (!($CommLst[$uID][$k] > 0)) {
          if ($k > $now)
            continue;
          $aux2 = $sCoachLiq->liqMensualBasic($uID, $year, $k);
          $CommLst[$uID][$k] = array_sum($aux2['totalClase']);
        }
      }
    }
    //---------------------------------------------------------------//
    // Calculate total
    foreach ($users as $u) {
      $uID = $u->id;
      foreach ($months as $k => $v) {
        $val = $liqLst[$uID][$k] + $CommLst[$uID][$k];
        $aLiq[$uID][$k] = $val;
        $aLiqTotal[$uID] += $val;
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

  function liqMensualBasic($id, $year, $month) {
    $lstMonts = lstMonthsSpanish();
    $typePT = 2;

    $taxCoach = CoachRates::where('id_user', $id)->first();

    $ppc = $salary = $comm = 0;
    if ($taxCoach) {
      $ppc = $taxCoach->ppc;
      $comm = $taxCoach->comm / 100;
      $salary = $taxCoach->salary;
    }
    //---------------------------------------------------------------//

    $oLiq = CoachLiquidation::where('id_coach', $id)
            ->whereYear('date_liquidation', '=', $year)
            ->whereMonth('date_liquidation', '=', $month)
            ->first();
    if ($oLiq) {
      if ($oLiq->salary)
        $salary = $oLiq->salary;
    }
    //---------------------------------------------------------------//
    /** @ToDo ver si es sólo citas o todos los cobros */
    $oTurnos = Dates::where('id_coach', $id)
            ->whereMonth('date', '=', $month)
            ->whereYear('date', '=', $year)
            ->join('users_rates', 'users_rates.id', '=', 'id_user_rates')
            ->whereNotNull('users_rates.id_charges')
            ->with('user')->with('service')->with('uRates')
            ->orderBy('date')
            ->get();

    $totalClase = array();
    $pagosClase = array();
    $classLst = [];

    if ($oTurnos) {
      foreach ($oTurnos as $item) {
        $key = $item->service->id;
        if (!isset($classLst[$key])) {
          $classLst[$key] = $item->service->name;
          $pagosClase[$key] = [];
          $totalClase[$key] = 0;
        }

        $import = 0;
        if ($item->uRates && $item->uRates->charges)
          $import = $item->uRates->charges->import;

        $totalClase[$key] += $import * $comm;

        if ($item->service->type == $typePT) {
          /* 50€ precio de entrenamiento personal */
          $totalClase[$key] += 50;
        } else {
          $totalClase[$key] += $ppc;
        }

        $time = strtotime($item->date);
        $className = date('d', $time) . ' de ' . $lstMonts[date('n', $time)];
        $className .= ' a las ' . date('h a', $time);
        $className .= ' (cliente : ' . $item->user->name . ')';
        $pagosClase[$key][] = $className;
      }
    }
    return compact('pagosClase', 'totalClase', 'classLst', 'ppc', 'salary');
  }

  function liquMensual($id, $year, $month) {

    $data = $this->liqMensualBasic($id, $year, $month);
    //-----------------------------------------------------------//
    $oExpenses = \App\Models\Expenses::where('to_user', $id)
            ->whereMonth('date', '=', $month)
            ->whereYear('date', '=', $year)
            ->orderBy('date')
            ->get();
    $lstExpType = \App\Models\Expenses::getTypes();
    $totalExtr = $nExtr = [];
    if ($oExpenses) {
      foreach ($oExpenses as $item) {
        $key = $item->type;
        if (!isset($totalExtr[$key])) {
          $totalExtr[$key] = 0;
          $nExtr[$key] = $lstExpType[$key];
        }
        $totalExtr[$key] += $item->import;
      }
    }

    $data['totalExtr'] = $totalExtr;
    $data['nExtr'] = $nExtr;
    return $data;
  }

}
