<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Charges;
use App\Models\Expenses;
use App\Models\Rates;
use App\Models\User;
use App\Models\CashBoxs;
use App\Services\MailsService;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class CashBoxsController extends Controller {

    function getToUser($month = null, $day = null){
        $year = getYearActive();
        if (!$month)
          $month = date('m');
        if (!$day) $day = date('d');

        
        $closedBy = null;
        $dateQry = $year.'-'.$month.'-'.$day;
        $tCashCharges = $tCashBox = 0;
        $lstItems = [];
        $userIDs = $rateIDs = [];
        $lstUsr = $lstRates = [];
        
        $aCoachs = User::getCoachs()->pluck('name', 'id');
        $oCoachs = User::getCoachs();
        
        /** Saldos */
        $cashbox = CashBoxs::where('date','<', $dateQry)->orderBy('date','DESC')->first();
        if ($cashbox){
            $lstItems[] = [
                'id' => $cashbox->id,
                'import' => $cashbox->saldo,
                'concept' => 'Saldo caja anterior - ' . ($cashbox->concept),
                'type' => 'Saldo',
                'css' => 'grey',
                'user' => isset($aCoachs[$cashbox->user_id]) ? $aCoachs[$cashbox->user_id] : ''
            ];
            $tCashBox += $cashbox->saldo;
        }
        $cashbox = CashBoxs::where('date','=', $dateQry)->first();
        if ($cashbox){
            $closedBy = isset($aCoachs[$cashbox->user_id]) ? $aCoachs[$cashbox->user_id] : ' - ';
        }
        

        /** Charges */
        $lstCharges = Charges::join('users_rates','users_rates.id_charges','charges.id')
                ->where('import', '!=', 0)
                ->where('date_payment', '=', $dateQry)
                ->where('type_payment', 'cash')->get();
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

        /** Expenses */
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

        /** Dates */
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

        /** view */
        return view('admin.contabilidad.cashbox.cierres-diarios', [
                    'is_admin' => (Auth::user()->role == "admin"),
                    'closedBy' => $closedBy,
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
                    'dateQry' => $dateQry,
                    'typePayment' => [2=>'CASH']
                ]);
    }



    function close(Request $req){

        
        $date = $req->input('date');
        $cashbox = CashBoxs::where('date', $date)->get();
        if (count($cashbox) > 0){
            return back()->withErrors('La caja ya está cerrada');
        }

        $cashbox = new CashBoxs();
        $cashbox->date = $date;
        $cashbox->saldo = $req->input('tCashBox')+$req->input('import');
        $cashbox->ajuste = $req->input('import');
        $cashbox->concept = $req->input('concept');
        $cashbox->comment = $req->input('comment');
        $cashbox->user_id = $req->input('to_user');
        $cashbox->save();

        /** Send Mail */
        $year = date('Y', strtotime($date));
        $month = date('m', strtotime($date));
        $day = date('d', strtotime($date));
        $aCoachs = User::getCoachs()->pluck('name', 'id');
        $lstCashbox = CashBoxs::whereYear('date', $year)
        ->whereMonth('date', $month)->orderBy('date')->get();
        $tableMail = '';
        if ($lstCashbox){
            $tableMail = '<table class="table"><tr><th>Día</th><th>Saldo</th><th>Ajuste</th><th>Concepto</th><th>Observ</th><th>Cierre por</th></tr>';
            
            foreach($lstCashbox as $c){
                $tableMail .= '<tr>';
                $tableMail .= '<td class="nowrap">'.$c->date.'</td>';
                $tableMail .= '<td class="nowrap">'.moneda($c->saldo).'</td>';
                $tableMail .= '<td class="nowrap">'.moneda($c->ajuste).'</td>';
                $tableMail .= '<td>'.$c->concept.'</td>';
                $tableMail .= '<td>'.$c->comment.'</td>';
                $tableMail .= '<td>'. ( isset($aCoachs[$c->user_id]) ? $aCoachs[$c->user_id] : ' - ' ).'</td>';
                $tableMail .= '</tr>';
            }
            $tableMail .= '</table>';
        }
        $lstMonthsSpanish = lstMonthsSpanish();
        $MailsService = new MailsService();
        $month = intval($month);
        $MailsService->sendEmail_CashBoxs($day,$lstMonthsSpanish[$month].' '.$year,$tableMail);
        /** Send Mail */
        
        
        return back()->with([ 'success' => 'La caja ya está cerrada']);
    }

}
