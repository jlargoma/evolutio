<?php

namespace App\Services;

use App\Models\Dates;
use App\Models\Rates;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CitasService {

  static function get_edit($id) {
    $oDate = Dates::find($id);
    if ($oDate) {
      $date = explode(' ', $oDate->date);
      $uRates = $oDate->uRates;
      $id_serv = $oDate->id_rate;
      $card = null;
      $price = $oDate->price;
      $id_user = -1;
      $email = null;
      $phone = null;
      $charge = null;
      $oUser = null;
      if ($uRates) {
        $price = $uRates->price;
        $id_serv = $uRates->id_rate;
        $oUser = $uRates->user;
        if ($oUser) {
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
      $ecogr = $oDate->getMetaContent('ecogr');
      $indiba = $oDate->getMetaContent('indiba');
      $motive = $oDate->getMetaContent('motive');
      $id_room = $oDate->getMetaContent('room');

      $equip_a = $equip_b = $equip_c = null;
      if ($oDate->date_type == 'esthetic'){
        $equip_a = $oDate->getMetaContent('equip_a');
        $equip_b = $oDate->getMetaContent('equip_b');
        $equip_c = $oDate->getMetaContent('equip_c');
      }


      $coachs = User::whereCoachs($oDate->date_type)->where('status', 1)->get();
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
      $extrs = $oDate->getMetaContent('extrs');
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
          'blocked' => $oDate->blocked,
          'isGroup' => $oDate->is_group,
          'urlBack' => self::get_urlBack($oDate->date_type, $date[0]),
          'ecogr' => $ecogr,
          'indiba' => $indiba,
          'equip_a' => $equip_a,
          'equip_b' => $equip_b,
          'equip_c' => $equip_c,
          'motive' => $motive,
          'tColors' => $tColors,
          'id_room' => $id_room,
          'extrs' => $extrs,
      ];
    }
    return null;
  }

  static function get_create($date, $time, $type) {
    if (!$date)
      $date = time();

    if ($time > 0 && $time < 10)
      $time = '0' . $time;

    $coachs = User::whereCoachs($type)->where('status', 1)->get();
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

    return [
        'date' => date('d-m-Y', $date),
        'time' => $time,
        'id_serv' => -1,
        'id_user' => -1,
        'id_coach' => -1,
        'customTime' => $time . ':00',
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
        'blocked' => false,
        'urlBack' => self::get_urlBack($type, date('Y-m-d', $date)),
        'tColors' => $tColors,
        'extrs'   => ''
    ];
  }

  static function get_calendars($start, $finish, $serv, $coach, $type, $lstDays = null) {

    $times = [];
    /*  ---------------------------   */
    $servLst = Rates::getByTypeRate($type)->pluck('name', 'id');
    /*  ---------------------------   */
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

    /*  ---------------------------   */
    $aLst = [];
    $sql = Dates::where('date_type', $type)
            ->where('date', '>=', date('Y-m-d', $start))
            ->where('date', '<=', date('Y-m-d', $finish));
    if ($serv && $serv != 0)
      $sql->where('id_rate', $serv);
    if ($coach && $coach > 0) {
      $sql->where('id_coach', $coach);
      $coachTimes = \App\Models\CoachTimes::where('id_coach', $coach)->first();
      if ($coachTimes) {
        $times = json_decode($coachTimes->times, true);
        if (!is_array($times))
          $times = [];
      }
    }
   
    $sql2 = $sql->clone();
    $sql3 = $sql->clone();
    $sql4 = $sql->clone();
    $oLst = $sql->get();
    /*  ---------------------------  */
    $ecogrs = $indiba = $equip_a = $equip_b = $equip_c = $rooms = [];
    $oEqup = $sql2->leftJoin('appointment_meta', function ($join) {
      $join->on('appointment.id', '=', 'appointment_meta.appoin_id');
    }) ->where('meta_value', 1)->whereIn('meta_key', ['ecogr', 'indiba', 'equip_a', 'equip_b', 'equip_c'])->get();

    if ($oEqup){
      foreach ($oEqup as $item){
        switch($item->meta_key){
          case 'ecogr':
            $ecogrs[] = $item->appoin_id;
            break;
          case 'indiba':
            $indiba[] = $item->appoin_id;
            break;
          case 'equip_a':
            $equip_a[] = $item->appoin_id;
            break;
          case 'equip_b':
            $equip_b[] = $item->appoin_id;
            break;
          case 'equip_c':
            $equip_c[] = $item->appoin_id;
            break;
        }
      }
    }


    $motives = $sql3->leftJoin('appointment_meta', function ($join) {
      $join->on('appointment.id', '=', 'appointment_meta.appoin_id');
    })->where('meta_key', 'motive')->pluck('meta_value', 'appoin_id')->toArray();

    $rooms = $sql4->leftJoin('appointment_meta', function ($join) {
      $join->on('appointment.id', '=', 'appointment_meta.appoin_id');
    })->where('meta_key', 'room')->pluck('meta_value', 'appoin_id')->toArray();

    /*  ---------------------------  */

    $detail = [];
    $days = listDaysSpanish();
    $months = lstMonthsSpanish();
    $sValora = new ValoracionService();
    $daysCoatch = [];
    global $countByCoah;
    $countByCoah = [];
    if ($oLst) {
      foreach ($oLst as $item) {
        $time = strtotime($item->date);
        $hour = date('G', $time);
        $date = date('Y-m-d', $time);
        $time = strtotime($date);
        $month = date('Y-m', $time);

        $dTime = $hTime = $item->getHour();
        $dTime .= ' ' . $days[date('w', $time)];
        $dTime .= ' ' . date('d', $time) . ' ' . $months[date('n', $time)];

        $hTime = substr($hTime, 3, 2);

        if (!isset($aLst[$time])) {
          $aLst[$time] = [];
          $daysCoatch[$time] = [];
        }
        if (!isset($aLst[$time][$hour])) {
          $aLst[$time][$hour] = [];
          $daysCoatch[$time][$hour] = [];
        }
        $daysCoatch[$time][$hour][$item->id_coach] = 1;
        if ($item->blocked) {
          $mtv = isset($motives[$item->id]) ? $motives[$item->id] : '';
          $aLst[$time][$hour][] = [
              'id' => $item->id,
              'charged' => ($item->is_group) ? 3 : 2,
              'type' => $item->id_rate,
              'coach' => $item->id_coach,
              'h' => $hTime,
              'name' => ($item->is_group) ? 'grupo' : 'bloqueo',
              'ecogr' => false,
              'mtv' => $mtv,
              'room' => ''
          ];
          $detail[$item->id] = [
              'n' => ($item->is_group) ? 'Cita Grupal' : 'bloqueo',
              'p' => '',
              's' => ($item->service) ? $item->service->name : '-',
              'cn' => isset($cNames[$item->id_coach]) ? $cNames[$item->id_coach] : '-',
              'mtv' => $mtv,
              'mc' => '', //Metodo pago
              'dc' => '', // fecha pago
              'd' => $dTime, // fecha 
              'room' => ''
          ];
          if (($item->is_group)) {
            self::countByCoah($item->id_coach, $month);
          }
          continue;
        }

        $u_name = '';
        $uRates = $item->uRates;
        $charge = null;
        if ($uRates) {
          $u_name = ($uRates->user) ? $uRates->user->name : null;
          $charge = $uRates->charges;
        }
        if ($type == 'pt' && !$sValora->isRate($uRates->id_rate))
          $charged = 1;
        else
          $charged = ($charge) ? 1 : 0;
        //------------------------------------
        $halfTime = false;
        if ($item->customTime) {
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
            'halfTime' => $halfTime,
            'h' => $hTime,
            'ecogr' => (in_array($item->id, $ecogrs)),
            'indiba' => (in_array($item->id, $indiba)),
            'equip_a' => (in_array($item->id, $equip_a)),
            'equip_b' => (in_array($item->id, $equip_b)),
            'equip_c' => (in_array($item->id, $equip_c)),
            'mtv' => '',
            'room' => isset($rooms[$item->id]) ? $rooms[$item->id] : ''
        ];
        $detail[$item->id] = [
            'n' => $u_name,
            'p' => ($uRates) ? moneda($uRates->price) : '--',
            's' => ($item->service) ? $item->service->name : '-',
            'cn' => isset($cNames[$item->id_coach]) ? $cNames[$item->id_coach] : '-',
            'mc' => '', //Metodo pago
            'dc' => '', // fecha pago
            'd' => $dTime, // fecha 
            'mtv' => isset($motives[$item->id]) ? $motives[$item->id] : '',
            'room' => isset($rooms[$item->id]) ? $rooms[$item->id] : ''
        ];

        if ($charge) {
          $detail[$item->id]['mc'] = payMethod($charge->type_payment);
          $detail[$item->id]['dc'] = dateMin($charge->date_payment);
        }

        self::countByCoah($item->id_coach, $month);
      }
    }
    /*  ---------------------------   */
    $lstMonts = lstMonthsSpanish();
    $aMonths = [];
    $year = getYearActive();
    foreach ($lstMonts as $k => $v) {
      if ($k > 0)
        $aMonths[$year . '-' . str_pad($k, 2, "0", STR_PAD_LEFT)] = $v;
    }
    /*  ---------------------------   */


    if (count($detail) > 0) {
      $aux = '';
      foreach ($detail as $k => $d) {
        $aux .= $k . ':{';
        foreach ($d as $k2 => $i2) {
          $aux .= "$k2: '$i2',";
        }
        $aux .= '},';
      }
      $detail = "{ $aux }";
    } else {
      $detail = null;
    }
    if ($type == 'pt')
      $avails = [];
    else
      $avails = self::timeAvails($daysCoatch, $coachs, $lstDays, $coach);

    return [
        'servLst' => $servLst,
        'serv' => $serv,
        'aLst' => $aLst,
        'aMonths' => $aMonths,
        'year' => $year,
        'tColors' => $tColors,
        'coachs' => $coachs,
        'coach' => $coach,
        'cNames' => $cNames,
        'times' => $times,
        'detail' => $detail,
        'avails' => $avails,
        'countByCoah' => $countByCoah,
    ];
  }

  static function countByCoah($cID, $month) {
    global $countByCoah;

    if (!isset($countByCoah[$month]))
      $countByCoah[$month] = [];
    if (!isset($countByCoah[$month][$cID]))
      $countByCoah[$month][$cID] = 0;
    $countByCoah[$month][$cID]++;

    if (!isset($countByCoah['w']))
      $countByCoah['w'] = [];
    if (!isset($countByCoah['w'][$cID]))
      $countByCoah['w'][$cID] = 0;
    $countByCoah['w'][$cID]++;
  }

  static function getCoachs($type) {
    if ($type == 'pt')
      $type = 'teach';
    return User::whereCoachs($type)->where('status', 1)->get();
  }

  static function timeAvails($daysCoatch, $coachs, $lstDays, $coachID = null) {
    $tCoach = [];
    if ($coachID) {
      $tCoach[$coachID] = 1;
    } else {
      foreach ($coachs as $i)
        $tCoach[$i->id] = 1;
    }

    $disponibles = [];
    for ($i = 1; $i < 7; $i++) {
      $aux = [];
      for ($j = 8; $j < 23; $j++) {
        $aux[$j] = $tCoach;
      }
      $disponibles[$i] = $aux;
    }

    $coachTimes = \App\Models\CoachTimes::whereIn('id_coach', array_keys($tCoach))->pluck('times', 'id_coach');
    if ($coachTimes) {
      foreach ($coachTimes as $idCoach => $t) {
        $times = json_decode($t, true);
        if (is_array($times)) {
          foreach ($times as $d => $hs) {
            foreach ($hs as $h => $enable) {
              $disponibles[$d][$h][$idCoach] = $enable;
            }
          }
        }
      }
    }
    $wDay = listDaysSpanish(true);
    $used = [];
    if ($lstDays) {
      foreach ($lstDays as $k => $days) {

        foreach ($days as $k => $d) {
          $time = $d['time'];
          $wID = array_search($d['day'], $wDay);

          /////////////////////
          $aux = [];
          for ($h = 8; $h < 23; $h++) {
            $aux2 = [];
            foreach ($disponibles[$wID][$h] as $cID => $cAvail) {
              if ($cAvail == 1)
                $aux2[] = $cID;
            }
            $aux[$h] = $aux2;
          }
          /////////////////////
          if (isset($daysCoatch[$time])) {
            foreach ($daysCoatch[$time] as $h => $item) {
              $aux4 = isset($aux[$h]) ? $aux[$h] : [];
              foreach ($item as $cID => $u) {
                $aux3 = array_search($cID, $aux4);
                if ($aux3 !== false)
                  unset($aux[$h][$aux3]);
              }
            }
          }
          /////////////////////

          $used[$time] = $aux;
        }
      }
    }
    return $used;
  }

  static function get_urlBack($type, $date) {
    $urlBack = '/admin';
    if (isset($_GET['weekly'])) {
      switch ($type) {
        case 'nutri':
          $urlBack = '/admin/citas-nutricion-week/';
          break;
        case 'fisio':
          $urlBack = '/admin/citas-fisioterapia-week/';
          break;
        case 'pt':
          $urlBack = '/admin/citas-pt-week/';
          break;
      }

      $week = date('W', strtotime($date));
      if (date('W') != $week) {
        $urlBack .= $week;
      }
      return $urlBack;
    }


    $date = substr($date, 0, 7);

    switch ($type) {
      case 'nutri':
        $urlBack = '/admin/citas-nutricion/';
        break;
      case 'fisio':
        $urlBack = '/admin/citas-fisioterapia/';
        break;
      case 'pt':
        $urlBack = '/admin/citas-pt/';
        break;
    }

    if (date('Y-m') != $date)
      $urlBack .= $date;
    return $urlBack;
  }

  static function datesAvails($type, $start, $end, $ecograf = false) {

    $hours = [];
    for ($i = 8; $i < 20; $i++) {
      $hours[$i] = 0;
      $enambleds[$i] = 1;
    }


    $lstDays = arrayDays($start, $end, 'Y-m-d', $hours);
    $lstDaysEmpty = arrayDays($start, $end, 'Y-m-d', $enambleds);
    $lstDaysW = arrayDays($start, $end, 'Y-m-d');

    $aCoachs = User::whereCoachs($type)->where('status', 1)->pluck('id');
    foreach ($aCoachs as $cID) {
      $aux = $lstDaysEmpty;

      /* -------------------------------------------------------- */
      /* --   GET COACHs hours    ------------------------------ */
      $coachTimes = \App\Models\CoachTimes::where('id_coach', $cID)->first();
      if ($coachTimes) {
        $times = json_decode($coachTimes->times, true);
        if (is_array($times)) {
          foreach ($aux as $d => $hs) {
            $dw = $lstDaysW[$d];
            if (isset($times[$dw])) {
              foreach ($times[$dw] as $h => $enable) {
                $aux[$d][$h] = $enable;
              }
            }
          }
        }
      }
      /* -------------------------------------------------------- */
      /* ---- GET COACHs APPOINTMENTS         ------------------ */

      $oDates = Dates::where('date_type', $type)
                      ->where('date', '>=', $start)
                      ->where('date', '<=', $end)
                      ->where('id_coach', $cID)->pluck('date');
      if (count($oDates) > 0) {
        foreach ($oDates as $dateTime) {
          $aux_date = substr($dateTime, 0, 10);
          $aux_time = intval(substr($dateTime, 11, 2));
          if (isset($aux[$aux_date])) {
            if (isset($aux[$aux_date][$aux_time])) {
              $aux[$aux_date][$aux_time] = 0;
            }
          }
        }
      }
      /* -------------------------------------------------------- */
      /* -------------------------------------------------------- */
      foreach ($aux as $d => $hs) {
        foreach ($hs as $h => $enable) {
          if ($enable) {
            $lstDays[$d][$h] = 1;
          }
        }
      }
      /* -------------------------------------------------------- */
      /* ---- REMOVE APPOINTMENTS WITH ECOGRAFIA      ----------- */
      if ($ecograf){
            $oDates = Dates::where('date_type', $type)
                    ->join('appointment_meta', function($join)
                {
                  $join->on('appointment_meta.appoin_id', '=', 'appointment.id');
                  $join->where('appointment_meta.meta_key', "ecogr");
                })->where('date', '>=', $start)
                ->where('date', '<=', $end)->get();
          if ($oDates){
            foreach ($oDates as $d){
              if ($d->meta_value == 1){
                $aux_date = substr($d->date, 0, 10);
                $aux_time = intval(substr($d->date, 11, 2));
                if (isset($lstDays[$aux_date][$aux_time]))
                  $lstDays[$aux_date][$aux_time] = 0;
              }
            }
          }     
               
      }
      
      
    }
    return $lstDays;
  }

}
