<?php

namespace App\Http\Controllers;

use App\Models\Bonos;
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
        $tSaldo = $tIngr = $tExpen = $tCashBox = 0;
        $lstItems = [];
        $userIDs = $rateIDs = $bonoIDs =[];
        $lstUsr = $lstRates = [];
        $is_admin = (Auth::user()->role == "admin");

        if(!$is_admin){
            $day = date('d');
            $month = date('m');
        }

        
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
            $tSaldo += $cashbox->saldo;
        }
        $oCashbox = CashBoxs::where('date','=', $dateQry)->first();
        if ($oCashbox){
            $closedBy = isset($aCoachs[$oCashbox->user_id]) ? $aCoachs[$oCashbox->user_id] : ' - ';
        }
        

        /** Charges */
        $lstCharges = Charges::select('charges.*','users_rates.id_rate','users_rates.rate_month','users_rates.rate_year')->leftjoin('users_rates','users_rates.id_charges','charges.id')
                ->where('import', '!=', 0)
                ->where('date_payment', '=', $dateQry)
                ->where('type_payment', 'cash')->get();
        if($lstCharges){
            foreach($lstCharges as $ch){
                $userIDs[] = $ch->id_user;
                $rateIDs[] = $ch->id_rate;
                $bonoIDs[] = $ch->bono_id;
            }
            $lstUsr = User::whereIn('id',array_unique($userIDs))->pluck('name','id')->toArray();
            $lstRates = Rates::whereIn('id',array_unique($rateIDs))->pluck('name','id')->toArray();
            $lstBonos = Bonos::whereIn('id',array_unique($bonoIDs))->pluck('name','id')->toArray();

            foreach($lstCharges as $ch){
                $concept = isset($lstUsr[$ch->id_user]) ? $lstUsr[$ch->id_user] : '';
                if ($ch->id_rate){
                    $concept .= ' - '.(isset($lstRates[$ch->id_rate]) ? $lstRates[$ch->id_rate] : 'Pago ');
                    $concept .= ' ('.$ch->rate_month.'/'.$ch->rate_year.')';
                }
                if ($ch->bono_id)   $concept .= ' - Bono '.(isset($lstBonos[$ch->bono_id]) ? $lstBonos[$ch->bono_id] : 'Bonos ');
                
                $lstItems[] = [
                    'id' => $ch->id,
                    'import' => $ch->import,
                    'concept' => $concept,
                    'type' => 'Cobro',
                    'css' => 'green',
                    'user' => isset($aCoachs[$ch->coach_id]) ? $aCoachs[$ch->coach_id] : ''
                ];
                
                $tCashBox += $ch->import;
                $tIngr += $ch->import;
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
                $tExpen += $item->import;
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


        
        /** Monthly */
        $tableMonthly = '';
        if ($is_admin){
            $lstCashboxMonth = CashBoxs::whereYear('date', $year)->whereMonth('date', $month)->orderBy('date')->get();
            if ($lstCashboxMonth){
                $tableMonthly = '<table class="table"><tr><th>Día</th><th>Saldo</th><th>Ajuste</th><th>Concepto</th><th>Cierre por</th></tr>';
                $totalArqueo = 0;
                foreach($lstCashboxMonth as $c){
                    $tableMonthly .= '<tr>';
                    $tableMonthly .= '<td class="nowrap">'.$c->date.'</td>';
                    $tableMonthly .= '<td class="nowrap">'.moneda($c->saldo).'</td>';
                    $tableMonthly .= '<td class="nowrap">'.moneda($c->ajuste).'</td>';
                    $tableMonthly .= '<td>'.$c->concept.'</td>';
                    $tableMonthly .= '<td>'. ( isset($aCoachs[$c->user_id]) ? $aCoachs[$c->user_id] : ' - ' ).'</td>';
                    $tableMonthly .= '</tr>';
                    $totalArqueo += $c->ajuste;
                }
                $tableMonthly .= '</table>';
                $tableMonthly .= '<p  style="text-align: center;background-color: #e9e9e9;padding: 7px;"><b>Total Arqueos:</b> '.moneda($totalArqueo).'</p>';
            }
        }



        /** view */
        return view('admin.contabilidad.cashbox.cierres-diarios', [
                    'is_admin' => $is_admin,
                    'closedBy' => $closedBy,
                    'tSaldo' => $tSaldo,
                    'tIngr' => $tIngr,
                    'tExpen' => $tExpen,
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
                    'oCashbox' => $oCashbox,
                    'gType' => $gType,
                    'datePayment' =>  $day .'-'.$month.'-'.$year,
                    'dateQry' => $dateQry,
                    'tableMonthly' => $tableMonthly,
                    'typePayment' => [2=>'CASH']
                ]);
    }



    function close(Request $req){

        
        $date = $req->input('date');
        $cashbox = CashBoxs::where('date', $date)->get();
        if (count($cashbox) > 0){
            return back()->withErrors('La caja ya está cerrada');
        }

        $arqueo = $req->input('import');
        $cashbox = new CashBoxs();
        $cashbox->date = $date;
        $cashbox->saldo = $req->input('tCashBox')+$req->input('import');
        $cashbox->ajuste = $arqueo;
        $cashbox->concept = $req->input('concept');
        $cashbox->comment = $req->input('comment');
        $cashbox->user_id = $req->input('to_user');
        $cashbox->save();

        /** Send Mail */
        $year = date('Y', strtotime($date));
        $month = date('m', strtotime($date));
        $day = date('d', strtotime($date));
        $aCoachs = User::getCoachs()->pluck('name', 'id');
        $lstCashbox = CashBoxs::whereYear('date', $year)->whereMonth('date', $month)->orderBy('date')->get();
        $tableMail = '';
        if ($lstCashbox){
            $tableMail = '<table class="table">';
            $tableMail .= '<tr><td>Saldo Anterior</td><td class="tVal">'.moneda($req->input('tSaldo')).'</td></tr>';
            $tableMail .= '<tr><td>Ingresos del día</td><td class="tVal">'.moneda($req->input('tIngr')).'</td></tr>';
            $tableMail .= '<tr><td>Salidas del día</td><td class="tVal">-'.moneda($req->input('tExpen')).'</td></tr>';

            if ($arqueo || $arqueo == 0)
            $tableMail .= '<tr><td>Arqueo: <small>'.$req->input('concept').'</small></td><td class="tVal">'.moneda($arqueo).'</td></tr>';


            $tableMail .= '<tr><td>Resultado</td><td class="tVal"><b>'.moneda($req->input('tSaldo')+$req->input('tIngr')-$req->input('tExpen')+$arqueo).'</b></td></tr>';
            $tableMail .= '<tr><td colspan="2" class="tCenter">Cerrada por: <b>'.( isset($aCoachs[$cashbox->user_id]) ? $aCoachs[$cashbox->user_id] : ' - ' ).'</b></td></tr>';
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