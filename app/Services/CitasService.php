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
      $id_serv = $oDate->id_rate;
      $card    = null;
      $price   = 0; 
      $id_user = -1;
      $email   = null;
      $phone   = null;
      $charge  = null;
      $oUser  = null;
      if ($uRates){
        $price   = $uRates->price;
        $id_serv = $uRates->id_rate;
        $oUser = $uRates->user;
        if ($oUser){
          $id_user = $oUser->id;
          $email = $oUser->email;
          $phone = $oUser->telefono;
          $charge = $uRates->charges;
          
          $paymentMethod = $oUser->getPayCard();
          if ($paymentMethod) {
            $aux = $paymentMethod->toArray();
            $card['brand'] = $aux['card']['brand'];
            $card['exp_month'] = $aux['card']['exp_month'];
            $card['exp_year'] = $aux['card']['exp_year'];
            $card['last4'] = $aux['card']['last4'];
          }
      
        }
      }
      $oServicios = Rates::getByTypeRate($oDate->date_type);
      return [
          'date' => date('d-m-Y', strtotime($date[0])),
          'time' => intval($date[1]),
          'id_serv' => $id_serv,
          'id_user' => $id_user,
          'id_coach' => $oDate->id_coach,
          'customTime' => $oDate->customTime,
          'email' => $email,
          'phone' => $phone,
          'price' => $price,
          'card' => $card,
          'id' => $oDate->id,
          'type' => $oDate->date_type,
          'charge' => $charge,
          'services' => $oServicios,
          'oUser' => $oUser,
          'users' => User::where('role', 'user')->where('status', 1)->orderBy('name', 'ASC')->get(),
          'coachs' => self::getCoachs($oDate->date_type),
          'blocked' => $oDate->blocked
      ];
    }
    return null;
  }

  static function get_create($date,$time,$type) {
    if (!$date) $date = time();

    if($time>0) $time = '0'.$time;
    return [
      'date' => date('d-m-Y', $date),
      'time' => $time,
      'id_serv' => -1,
      'id_user' => -1,
      'id_coach' => -1,
      'customTime' => $time.':00',
      'email' => '',
      'phone' => '',
      'card' => null,
      'id' => -1,
      'charge' => null,
      'price' => 0,
      'type' => $type,
      'services' => Rates::getByTypeRate($type),
      'users' => User::where('role', 'user')->where('status', 1)->orderBy('name', 'ASC')->get(),
      'coachs' => self::getCoachs($type),
      'blocked' => false
     ];
  }
  
  static function get_calendars($start,$finish,$serv,$coach,$type) {
        
    $times = [];    
    /**************************************************** */
    $servLst = Rates::getByTypeRate($type)->pluck('name', 'id');
    /**************************************************** */
    $coachs = self::getCoachs($type);
    $tColors = [];
    $cNames = [];
    if ($coachs) {
        $auxColors = colors();
        $i = 0;
        foreach ($coachs as $item) {
            if (!isset($auxColors[$i]))
                $i = 0;
            $tColors[$item->id] = $auxColors[$i];
            $cNames[$item->id] = $item->name;
            $i++;
        }
    }

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
    $days = listDaysSpanish();
    $months = lstMonthsSpanish();
    $sValora = new ValoracionService();
    if ($oLst) {
        foreach ($oLst as $item) {
            $time = strtotime($item->date);
            $hour = date('G', $time);
            $date = date('Y-m-d', $time);
            $time = strtotime($date);

            $dTime = $item->getHour();
            $dTime .= ' '.$days[date('w',$time)];
            $dTime .= ' '.date('d',$time).' '.$months[date('n',$time)];
            
            if (!isset($aLst[$time]))
                $aLst[$time] = [];
            if (!isset($aLst[$time][$hour]))
                $aLst[$time][$hour] = [];
            
            if ($item->blocked){
              $aLst[$time][$hour][] = [
                'id' => $item->id,
                'charged' => 2,
                'type' => $item->id_rate,
                'coach' => $item->id_coach,
                'name' => 'bloqueo',
              ];
              $detail[$item->id] = [
                  'n' => 'bloqueo',
                  'p'=> '',
                  's'=> '-',
                  'cn' => isset($cNames[$item->id_coach]) ? $cNames[$item->id_coach] : '-',
                  'mc'=>'', //Metodo pago
                  'dc'=>'', // fecha pago
                  'd'=>$dTime, // fecha 
              ];
              continue;
            }

            $u_name = '';
            $uRates = $item->uRates;
            $charge = null;
            if ($uRates){
              $u_name = ($uRates->user) ? $uRates->user->name : null;
              $charge = $uRates->charges;
            }
            if ($type == 'pt' && !$sValora->isRate($uRates->id_rate))
              $charged = 1;
            else $charged = ($charge) ? 1 : 0;
            //------------------------------------
            $halfTime = false;
            if ($item->customTime){
              $dateTime = explode(' ', $item->date);
              $halfTime = ($dateTime[1] != $item->customTime);
            }
            //------------------------------------
            $aLst[$time][$hour][] = [
                'id' => $item->id,
                'charged' => $charged,
                'type' => $item->id_rate,
                'coach' => $item->id_coach,
                'name' => $u_name,
                'halfTime'=>$halfTime
            ];
            $detail[$item->id] = [
                'n' => $u_name,
                'p'=>($uRates) ? moneda($uRates->price): '--',
                's'=> ($item->service) ? $item->service->name : '-',
                'cn' => isset($cNames[$item->id_coach]) ? $cNames[$item->id_coach] : '-',
                'mc'=>'', //Metodo pago
                'dc'=>'', // fecha pago
                'd'=>$dTime, // fecha 
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
