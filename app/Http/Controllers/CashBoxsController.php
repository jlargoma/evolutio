<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Charges;
use App\Models\Expenses;
use App\Models\Rates;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CashBoxsController extends Controller {

    function getToUser($month = null, $day = null){
        $year = getYearActive();
        if (!$month)
          $month = date('m');
        if (!$day) $day = date('d');

        

        $dateQry = $year.'-'.$month.'-'.$day;
        $tCashCharges = $tCashBox = 0;
        $lstCharges = Charges::join('users_rates','users_rates.id_charges','charges.id')
                ->where('import', '!=', 0)
                ->where('date_payment', '=', $dateQry)
                ->where('type_payment', 'cash')->get();

          
        $aCoachs = User::getCoachs()->pluck('name', 'id');
        $oCoachs = User::getCoachs();
        $lstItems = [];
        $userIDs = $rateIDs = [];
        $lstUsr = $lstRates = [];
        if($lstCharges){
            foreach($lstCharges as $ch){
                $userIDs[] = $ch->id_user;
                $rateIDs[] = $ch->id_rate;
            }

            $lstUsr = User::whereIn('id',array_unique($userIDs))->pluck('name','id')->toArray();
            $lstRates = Rates::whereIn('id',array_unique($rateIDs))->pluck('name','id')->toArray();

            foreach($lstCharges as $ch){

                $concept = isset($lstUsr[$ch->id_user]) ? $lstUsr[$ch->id_user] : '';
                $concept .= ' - '.(isset($lstRates[$ch->id_rate]) ? $lstRates[$ch->id_rate] : 'Pago ');
                $concept .= ' ('.$ch->rate_month.'/'.$ch->rate_year.')';
                $lstItems[] = [
                    'id' => $ch->id_charges,
                    'import' => $ch->import,
                    'concept' => $concept,
                    'type' => 'Cobro',
                    'css' => 'green',
                    'user' => isset($aCoachs[$ch->coach_id]) ? $aCoachs[$ch->coach_id] : ''
                ];
                $tCashBox += $ch->import;
            }
        }

        $Expenses = Expenses::where('date',$dateQry)->where('typePayment',2)->get();
        $gType = Expenses::getTypes();
        if($Expenses){
            foreach($Expenses as $item){

                $concept = $item->concept;
                $concept .= ' - '.(isset($gType[$item->type]) ? $gType[$item->type] : ' ');
                $lstItems[] = [
                    'id' => $item->id,
                    'import' => $item->import,
                    'concept' => $concept,
                    'type' => 'Pago',
                    'css' => 'red',
                    'user' => isset($aCoachs[$item->to_user]) ? $aCoachs[$item->to_user] : ''
                ];
                $tCashBox -= $item->import;
            }
        }


        $auxTime = $year . '-' . str_pad($month, 2, "0", STR_PAD_LEFT) . '-';
        if ($day == 'all') {
            $auxTime .= '01';
            $yesterday = date('n/j', strtotime($auxTime . ' -1 days'));
            $tomorrow = date('n/j', strtotime($auxTime));
        } else {
            $auxTime .= str_pad($day, 2, "0", STR_PAD_LEFT);
            $yesterday = date('n/j', strtotime($auxTime . ' -1 days'));
            $tomorrow = date('n/j', strtotime($auxTime . ' +1 days'));
        }
        $lastDay = date("t", strtotime($auxTime));

        $lstMonthsSpanish = lstMonthsSpanish();
        unset($lstMonthsSpanish[0]);
        $months = $lstMonthsSpanish;

        return view('admin.contabilidad.cashbox.cierres-diarios', [
                    'is_admin' => (Auth::user()->role == "admin"),
                    'totalCash' => $tCashCharges,
                    'tCashBox' => $tCashBox,
                    'lstItems' => $lstItems,
                    'lstUsr' => $lstUsr,
                    'lstRates' => $lstRates,
                    'yesterday' => $yesterday,
                    'tomorrow' => $tomorrow,
                    'lastDay' => $lastDay,
                    'months' => $months,
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'oCoachs' => $oCoachs,
                    'gType' => $gType,
                    'datePayment' =>  $day .'-'.$month.'-'.$year,
                    'typePayment' => [2=>'CASH']
                ]);
    }
    function setToUser(Request $req){
        $date = date('Y-m-d');
        $charges = Charges::where('import', '!=', 0)
                ->where('date_payment', '=', $date)
                ->where('type_payment', 'cash')
                ->sum('import');

        
        return view('admin.informes.caja.user', [
                    'totalCash' => $charges,
                    'date' => $date
                ]);
    }

}
