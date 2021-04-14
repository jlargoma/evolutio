<?php

//die('asdf');
function formatToImport($data) {
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

function lstMonthsSpanish($min = true) {
    if ($min) {
        return['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sept', 'Oct', 'Nov', 'Dic'];
    } else {
        return ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    }
}

function getMonthSpanish($m, $min = true) {
    $arrayMonth = lstMonthsSpanish($min);
    $m = intval($m);
    return isset($arrayMonth[$m]) ? $arrayMonth[$m] : '';
}

function convertSpanishDate($date) {
    $aux = explode(',', $date);
    if (isset($aux[1])) {
        $date = explode(' de ', $aux[1]);
        if (count($date) == 3) {
            $day = intval($date[0]);
            if ($day < 10)
                $day = '0' . $day;
            $months = lstMonthsSpanish(FALSE);
            $month = array_search(trim($date[1]), $months);
            if ($month < 10)
                $month = '0' . $month;

            return intval($date[2]) . '-' . $month . '-' . $day;
        }
    }
    return '';
}

function convertDateToShow($date, $yearsComplete = false) {
    $date = trim($date);
    if ($date) {

        $aux = explode('-', $date);
        if (is_array($aux) && count($aux) == 3) {
            if ($yearsComplete)
                return $aux[2] . '/' . $aux[1] . '/' . $aux[0];
            return $aux[2] . '/' . $aux[1] . '/' . ($aux[0] - 2000);
        }
    }
    return null;
}

function convertDateToShow_text($date, $year = false) {
    $date = trim($date);
    if ($date) {

        $aux = explode('-', $date);
        if (is_array($aux) && count($aux) == 3) {
            if ($year)
                return $aux[2] . ' ' . getMonthSpanish($aux[1]) . ', ' . ($aux[0] - 2000);
            return $aux[2] . ' ' . getMonthSpanish($aux[1]);
        }
    }
    return null;
}

function dateMin($date) {
    $date = trim($date);
    if ($date) {

        $aux = explode('-', $date);
        if (is_array($aux) && count($aux) == 3) {
            return $aux[2] . ' ' . getMonthSpanish(intval($aux[1]));
        }
    }
    return null;
}

function rates_codes() {
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

function assetV($uri) {
    $uri_asset = asset($uri);
//  $v = 'v1.0.1';
    $v = env('VERSION', 'v1.0.1');
    return $uri_asset . '?' . $v;
}

function getYearActive() {

    if (isset($_COOKIE['ActiveYear'])) {
        return $_COOKIE['ActiveYear'];
    }
    setcookie('ActiveYear', date('Y'), time() + (86400 * 30), "/"); // 86400 = 1 day
    return date('Y');
}

function colors() {
    return ['#9b59ff', '#295d9b', '#10cfbd', 'red', '#871282', '#066572', '#a7dae7', '#1fa7c0', '#b2d33d', '#3aaa49'];
}

function show_isset($index, $array) {
    if (isset($array[$index])) {
        echo $array[$index];
    }
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

function getNameDay($dw, $min = false) {
    $wds = listDaysSpanish($min);
    return isset($wds[$dw]) ? $wds[$dw] : '';
}

function moneda($mount,$cero=true,$decimals=0){
  if ($cero)  return number_format($mount, $decimals, ',', '.' ).' €';
  
  if ($mount != 0) return number_format($mount, $decimals, ',', '.' ).' €';
  return '--';
  
}

function str_limit($txt,$limit,$end='...'){
return \Illuminate\Support\Str::limit($txt,$limit,$end);
}
function str_random($limit=40){
    return Str::random($limit);
}