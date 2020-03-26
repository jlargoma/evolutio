<?php

function formatToImport($data) {
  return $data;
  return utf8_decode($data);
}

  function lstMonths($startYear, $endYear, $format = 'ym') {
    $diff = $startYear->diffInMonths($endYear) + 1;
    $lstMonths = [];
    if (is_numeric($diff) && $diff > 0) {
      $aux = strtotime($startYear);
      while ($diff > 0) {
        $lstMonths[date($format, $aux)] = ['m' => date('n', $aux), 'y' => date('y', $aux)];
        $aux = strtotime("+1 month", $aux);
        $diff--;
      }
    }

    return $lstMonths;
  }

  function lstMonthsSpanish($min=true){
    if ($min){
      return['','Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sept', 'Oct', 'Nov', 'Dic'];
    } else {
      return ['','Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    }
  }

  function getMonthsSpanish($m, $min = true) {
    if ($min) {
      $arrayMonth = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sept', 'Oct', 'Nov', 'Dic'];
    } else {
      $arrayMonth = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    }
    return isset($arrayMonth[$m]) ? $arrayMonth[$m] : '';
  }
  
  function getDaysSpanish($m=false, $min = true) {

    $array = listDaysSpanish($min);

    if ($m){
      return isset($array[$m]) ? $array[$m] : '';
    }
    
    return $array;
  }
  
  function listDaysSpanish($min = false) {
    if ($min) {
      $array = [
          1 => 'Lun', 
          2 => 'Mar', 
          3 => 'Mié', 
          4 => 'Jue', 
          5 => 'Vie',
          6 => 'Sáb',
          0 => 'Dom', 
          ];
    } else {
      $array = [
          1 => 'Lunes', 
          2 => 'Martes', 
          3 => 'Miércoles', 
          4 => 'Jueves', 
          5 => 'Viernes',
          6 => 'Sábado',
          0 => 'Domingo', 
          ];
    }
    return $array;
  }

  function getUserIpAddr() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      //ip from share internet
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      //ip pass from proxy
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }

  function convertDateHTML5($dateHTML5){
    $dateHTML5= trim($dateHTML5);
    if ($dateHTML5){
      
      $aux = explode('/',$dateHTML5);
      if (is_array($aux) && count($aux)==3){
        return $aux[2].'-'.$aux[1].'-'.$aux[0];
      }
    }
    return null;
  }
  function convertDateToShow($date,$datatime=false){
    $date= trim($date);
    if ($date){
      
      if (strpos($date,' ') !== FALSE){
        $aux = explode(' ', $date);
        $date = $aux[0];
      }
      
      $aux = explode('-',$date);
      if (is_array($aux) && count($aux)==3){
        if ($datatime){
          return $aux[2].'/'.$aux[1].'/'.$aux[0];
        }
        $year = $aux[0]-2000;
        if ($aux[0]<2000) $year = $aux[0];
        return $aux[2].'/'.$aux[1].'/'.$year;
      }
    }
    return null;
  }
  
  function lstGralStatus(){
    $statusLst = array(
         1=>'publicado',
         2=>'borrador'
      );
    return $statusLst;
  }
  
  function formatTime($time,$array=false) {
      $time = intval($time);
      if ($time<1){
        return '00:00';
      }
      $hours = floor($time / 60);
      $minutes = $time - ($hours * 60);

      if ($array) return [$hours,$minutes];
      
      if ($hours< 10) $hours = '0'.$hours;
      if ($minutes< 10) $minutes = '0'.$minutes;
      return "$hours:$minutes";
  }
  
  function calcTime($start,$end) {
    
    $time_start = $time_end = 0;
    $aux1 = explode(':', $start);
    if (is_array($aux1) && count($aux1)>1){
      $time_start = $aux1[0] * 60 + $aux1[1];
    }
    $aux1 = explode(':', $end);
    if (is_array($aux1) && count($aux1)>1){
      $time_end = $aux1[0] * 60 + $aux1[1];
    }
    
    
    if ($time_start > 0 && $time_end > 0){
      return $time_end-$time_start;
    }
    
    return 0;
  }
  
   function getUnixTime($time) {
    
    $aux1 = explode(':', $time);
    if (is_array($aux1) && count($aux1)>1){
      return  $aux1[0] * 60 + $aux1[1];
    }
    return 0;
  }
  
  function validation_msg(){
    return [
        'required' => 'El campo :attribute es requerido.',
        'email'    => 'El :attribute debe ser un email válido.',
        'unique'  => 'Ya existe otro registro con el valor del campo :attribute.',
        'confirmed'  => 'El campo :attribute y su confirmación no coinciden.',
    ];
  }
  function start_end_week($fecha){

      $start="Monday";
      $end="Sunday";

      $strDate = strtotime($fecha);

      $startDate = date('Y-m-d',strtotime('last '.$start,$strDate));
      $endDate = date('Y-m-d',strtotime('next '.$end,$strDate));

      if(date("l",$strDate)==$start){
          $startDate= date("Y-m-d",$strDate);
      }
      if(date("l",$strDate)==$end){
          $endDate= date("Y-m-d",$strDate);
      }
      return Array("start"=>$startDate,"end"=>$endDate);
  }
  function renderTime($hours){
    return substr($hours, 0, 5);
  }
  
  function renderListAstext($lst){
    $aux_text = '';
    if(count($lst)>0){
      $aux_last = array_pop($lst);
      if(count($lst)>0)
        $aux_text = implode(', ', $lst).' y ';

      $aux_text .= $aux_last;
      $aux_text = ucfirst(strtolower($aux_text));
     
    }
      
    return $aux_text.'<br/>';
  }
  function renderListAsList($lst){
    $aux_text = '';
    if(count($lst)>0){
      $aux_text = implode('<br/>', $lst);
    }
      
    return $aux_text;
  }
  
function assetV($uri){
  $uri_asset = asset($uri);
  $v = env('APP_VERSION','1.1');
  return $uri_asset.'?'.$v;
}


function convertSpanishDate($date) {
  $aux = explode(',',$date);
  if (isset($aux[1])){
    $date = explode(' de ', $aux[1]);
    if (count($date) == 3){
      $day = intval($date[0]);
      if ($day<10) $day = '0'.$day;
      $months = lstMonthsSpanish(FALSE);
      $month = array_search(trim($date[1]),$months);
      if ($month<10) $month = '0'.$month;
      
      return intval($date[2]).'-'.$month.'-'.$day;
    }
  }
  return '';
}

function rates_codes(){
  return [
    'TARIFA_CL1',  
    'TARIFA_CL2',  
    'TARIFA_PT1',  
    'TARIFA_PT2',  
    'TARIFA_FI',  
    'FARIFA_NU',  
    'TARIFA_SALGE',  
    'TARIFA_SALIM'
  ];
}
function getUsrRole(){
  global $uRole;
  
  if (isset($uRole) && !empty($uRole)) {
    return $uRole;
  }
  
  $uRole = Auth::user()->role;
  return $uRole;
}

function moneda($mount,$cero=true){
  if ($cero)  return number_format($mount, 0, ',', '.' ).' €';
  
  if ($mount>0) return number_format($mount, 0, ',', '.' ).' €';
  return '--';
  
}