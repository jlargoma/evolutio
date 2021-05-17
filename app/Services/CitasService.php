<?php

namespace App\Services;

use App\Models\Dates;
use App\Models\Rates;
use App\Models\User;

class CitasService {

  static function get_edit($id) {
    $oDate = Dates::find($id);
    if ($oDate) {
      $date = explode(' ', $oDate->date);
      $uRates = $oDate->uRates;
      if (!$uRates){
        $oDate->delete();
        die('Servicio eliminado');
      }
      $oUser = $uRates->user;
      if (!$oUser){
        $uRates->delete();
        $oDate->delete();
        die('Usuario eliminado');
      }
      $oServicios = Rates::getByTypeRate($oDate->date_type);
      $price = $uRates->price;
      $card = null;
      $paymentMethod = $oUser->getPayCard();
      if ($paymentMethod) {
        $aux = $paymentMethod->toArray();
        $card['brand'] = $aux['card']['brand'];
        $card['exp_month'] = $aux['card']['exp_month'];
        $card['exp_year'] = $aux['card']['exp_year'];
        $card['last4'] = $aux['card']['last4'];
      }
      
      return [
          'date' => date('d-m-Y', strtotime($date[0])),
          'time' => intval($date[1]),
          'id_serv' => $uRates->id_rate,
          'id_user' => $oUser->id,
          'id_coach' => $oDate->id_coach,
          'email' => $oUser->email,
          'phone' => $oUser->telefono,
          'price' => $price,
          'card' => $card,
          'id' => $oDate->id,
          'charge' => $uRates->charges,
          'services' => $oServicios,
          'users' => User::where('role', 'user')->where('status', 1)->orderBy('name', 'ASC')->get(),
          'coachs' => self::getCoachs($oDate->date_type)
      ];
    }
    return null;
  }

  static function get_create($date,$time,$type) {
    if (!$date) $date = time();

    return [
      'date' => date('d-m-Y', $date),
      'time' => $time,
      'id_serv' => -1,
      'id_user' => -1,
      'id_coach' => -1,
      'email' => '',
      'phone' => '',
      'card' => null,
      'id' => -1,
      'charge' => null,
      'price' => 0,
      'services' => Rates::getByTypeRate($type),
      'users' => User::where('role', 'user')->where('status', 1)->orderBy('name', 'ASC')->get(),
      'coachs' => self::getCoachs($type)
     ];
  }
  
  static function get_calendars($start,$finish,$serv,$coach,$type) {
        
    $times = [];    
    /**************************************************** */
    $servLst = Rates::getByTypeRate($type)->pluck('name', 'id');
    /**************************************************** */
    $aLst = [];
    $sql = Dates::where('date_type', $type)
            ->where('date', '>=', date('Y-m-d', $start))
            ->where('date', '<=', date('Y-m-d', $finish));
    if ($serv && $serv != 0)
        $sql->where('id_rate', $serv);
    if ($coach && $coach > 0){
      $sql->where('id_coach', $coach);
      $coachTimes = \App\Models\CoachTimes::where('id_coach',$coach)->first(); 
      if ($coachTimes){
          $times = json_decode($coachTimes->times,true);
          if (!is_array($times)) $times = [];
      }
    }

    $oLst = $sql->get();
    $detail = [];
    if ($oLst) {
        foreach ($oLst as $item) {
            $time = strtotime($item->date);
            $hour = date('G', $time);
            $date = date('Y-m-d', $time);
            $time = strtotime($date);

            if (!isset($aLst[$time]))
                $aLst[$time] = [];
            if (!isset($aLst[$time][$hour]))
                $aLst[$time][$hour] = [];

            $u_name = '';
            $uRates = $item->uRates;
            $charge = null;
            if ($uRates){
              $u_name = ($uRates->user) ? $uRates->user->name : null;
              $charge = $uRates->charges;
            }

            $aLst[$time][$hour][] = [
                'id' => $item->id,
                'charged' => ($charge) ? 1 : 0,
                'type' => $item->id_rate,
                'coach' => $item->id_coach,
                'name' => $u_name,
            ];
            $detail[$item->id] = [
                'n' => $u_name,
                'p'=>($uRates) ? moneda($uRates->price): '--',
                's'=> ($item->service) ? $item->service->name : '-',
                'mc'=>'', //Metodo pago
                'dc'=>'', // fecha pago
            ];

            if ($charge){
              $detail[$item->id]['mc'] = payMethod($charge->type_payment);
              $detail[$item->id]['dc'] = dateMin($charge->date_payment);
            }
        }
    }
    /**************************************************** */
    $lstMonts = lstMonthsSpanish();
    $aMonths = [];
    $year = getYearActive();
    foreach ($lstMonts as $k => $v) {
        if ($k > 0)
            $aMonths[$year . '-' . str_pad($k, 2, "0", STR_PAD_LEFT)] = $v;
    }
    /**************************************************** */
    $coachs = self::getCoachs($type);
    $tColors = [];
    if ($coachs) {
        $auxColors = colors();
        $i = 0;
        foreach ($coachs as $item) {
            if (!isset($auxColors[$i]))
                $i = 0;
            $tColors[$item->id] = $auxColors[$i];
            $i++;
        }
    }


    if (count($detail)>0){
      $aux = '';
      foreach ($detail as $k=>$d){
        $aux .= $k.':{';
        foreach ($d as $k2=>$i2){
          $aux .= "$k2: '$i2',";
        }
        $aux .= '},';
      }
      $detail = "{ $aux }";
    } else {
      $detail = null;
    }
    return  [
        'servLst' => $servLst,
        'serv' => $serv,
        'aLst' => $aLst,
        'aMonths'=> $aMonths,
        'year'   => $year,
        'tColors'=> $tColors,
        'coachs' => $coachs,
        'coach'  => $coach,
        'times'  => $times,
        'detail' => $detail,
    ];
  }
  
  static function getCoachs($type) {
    if ($type == 'pt') 
      return User::where('role', 'teach')->where('status', 1)->get();
    
    return User::where('role', $type)->where('status', 1)->get();
  }
}
