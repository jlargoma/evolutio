<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; //línea necesaria

class UserRates extends Model {

  protected $table = 'users_rates';

  use SoftDeletes; //Implementamos 

  public function user() {
    return $this->hasOne('\App\Models\User', 'id', 'id_user');
  }

  public function rate() {
    return $this->hasOne('\App\Models\Rates', 'id', 'id_rate');
  }

  public function charges() {
    return $this->hasOne('\App\Models\Charges', 'id', 'id_charges');
  }

  static function getSumYear($year) {
    $uRates = UserRates::where('rate_year', $year)->get();
    $total = 0;
    foreach ($uRates as $item) {
      $c = $item->charges;
      if ($c) {
        $total += $c->import;
      } else {
        $total += $item->price;
      }
    }

    $oBonos = Charges::whereYear('date_payment', '=', $year)->where('bono_id', '>', 0)->sum('import');
    if ($oBonos) {
      $total += $oBonos;
    }
    return $total;
  }

  static function getSumYear_months($year) {
    $byMonths = [];
    for ($i = 1; $i < 13; $i++)
      $byMonths[$i] = 0;

    $uRates = UserRates::where('rate_year', $year)->get();
    $total = 0;
    foreach ($uRates as $item) {
      $c = $item->charges;
      if ($c) {
        $byMonths[$item->rate_month] += $c->import;
      } else {
        $byMonths[$item->rate_month] += $item->price;
      }
    }

    $oBonos = Charges::whereYear('date_payment', '=', $year)->where('bono_id', '>', 0)->get();
    if ($oBonos) {
      foreach ($oBonos as $c) {
        $m = intval(substr($c->date_payment, 5, 2));
        $byMonths[$m] += $c->import;
      }
    }

    return $byMonths;
  }

}
