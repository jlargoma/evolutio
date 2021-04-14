<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Carbon\Carbon;
use DB;
use \App\Models\User;

class InformesController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
    }

    public function informeForma() {
        $plan = array();
die('no armado');
        $usuarios = \App\Models\Models\UserRates::whereIn('id_rate', [
                    63,
                    69
                ])->groupBy('id_user')->get();

        foreach ($usuarios as $usuario) {
            $resumenes = \App\Models\PlanFit::where('id_user', $usuario->user->id)->orderBy('week', 'ASC')->get();
            if (count($resumenes) > 0) {
                foreach ($resumenes as $resumen) {
                    $plan[$usuario->user->id][$resumen->week] = $resumen;
                }
            } else {
                
            }
        }

        return view('.admin.informes.informe_nutri', [
            'usuarios' => $usuarios,
            'plan' => $plan,
            'actualWeek' => Carbon::now()->format('W'),
        ]);
    }

    public function saveForma(Request $request) {
        $forma = \App\Models\PlanFit::find($request->id);
        if (count($forma) > 0) {
            $forma->weight = $request->weight;
            $forma->save();
        } else {
            $forma = new \App\Models\Planfit();

            $forma->id_user = $request->user;
            $forma->week = $request->week;
            $forma->weight = $request->weight;
            $forma->save();
        }
    }

    public function newForma(Request $request) {
        $forma = new \App\Models\Planfit();

        $forma->id_user = $request->user;
        $forma->week = $request->week;
        $forma->weight = $request->weight;
        $forma->date = Carbon::now();
        $forma->save();
    }

    private function getCharges($year,$month,$day,$search=null) {
        $sql_charges = \App\Models\Charges::where('import', '!=', 0);
        $sql_CashBox = \App\Models\CashBox::where('import', '!=', 0)
                ->where('type', 'INGRESO');
        
        if ($search){
            $search = trim($search);
            $cliIDs = User::where('name', 'LIKE', "%" . $search . "%")->pluck('id');
            $sql_charges->whereIn('id_user',$cliIDs);
            $sql_CashBox->where('concept', 'LIKE', "%" . $search . "%");
        }
        
        if ($day == "all") {
            $starDate = "$year-$month-01";
            $endDate = date("Y-m-t", strtotime($starDate));
            $sql_charges->where('date_payment', '>=', $starDate)->where('date_payment', '<=', $endDate);
            $sql_CashBox->where('date', '>=', $starDate)->where('date', '<=', $endDate);
        } else {
            $starDate = "$year-$month-$day";
            $sql_charges->where('date_payment', '=', $starDate);
            $sql_CashBox->where('date', '=', $starDate);
        }

        $charges = $sql_charges->orderBy('date_payment')->get();
        $extrasCharges = $sql_CashBox->orderBy('date')->get();

        $bank = 0;
        $cash = 0;
        $clients = [];
        $rates = [];
        foreach ($charges as $charge) {
            $clients[] = $charge->id_user;
            $rates[] = $charge->id_rate;
            if ($charge->type_payment == "banco") {
                $bank += $charge->import;
            } elseif ($charge->type_payment == "cash") {
                $cash += $charge->import;
            }
        }

        /* EXTRAS OF CASH (VENDING... CURSOS... ) */

        if ($extrasCharges) {
            foreach ($extrasCharges as $index => $charge) {
                $cash += $charge->import;
            }
        }
        $endDay =  date("t", strtotime($starDate));
        $aUsers =  User::whereIn('id', $clients)->get()
                        ->pluck('name', 'id')->toArray();
        $aRates =  \App\Models\Rates::whereIn('id', $rates)->get()
                        ->pluck('name', 'id')->toArray();
        
        return [
            'charges' => $charges,
            'extrasCharges' => $extrasCharges,
            'cash' => $cash,
            'bank' => $bank,
            'clients' => $clients,
            'rates' => $rates,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'endDay' => $endDay,
            'aUsers' => $aUsers,
            'aRates' => $aRates,
                ];
                
                
    }
    
    
    public function informeClienteMes(Request $request, $month = null, $day = null) {

        $year = getYearActive();
        if (!$month)
            $month = date('m');
        if (!$day)
            $day = 'all';

        $data = $this->getCharges($year,$month,$day);
        $lstMonthsSpanish = lstMonthsSpanish();
        unset($lstMonthsSpanish[0]);
        $data['months'] =  $lstMonthsSpanish;
        return view('admin.informes.informeClientesMes',$data);
    }
    public function informeCuotaMes(Request $request, $month = null, $day = null) {

        $year = getYearActive();
        if (!$month)
            $month = date('m');
        if (!$day)
            $day = 'all';

        $data = $this->getCharges($year,$month,$day);
        $lstMonthsSpanish = lstMonthsSpanish();
        unset($lstMonthsSpanish[0]);
        $data['months'] =  $lstMonthsSpanish;
        
        $byRate = [];
        foreach ($data['charges'] as $charges){
            if (!isset($byRate[$charges->id_rate]))
                $byRate[$charges->id_rate] = 0;
            
            $byRate[$charges->id_rate] += $charges->import;
        }
        $data['byRate'] =  $byRate;
        return view('admin.informes.informeCuotaMes',$data);
    }
    
    public function searchClientInform(Request $request,  $month = null) {
        $year = getYearActive();
        if (!$month)  $month = date('m');
        
        $search = trim($request->input('search',''));
        $data = $this->getCharges($year,$month,'all',$search);
        return view('admin/informes/_table_informes', $data);
    }

    public function informeCierreDiario(Request $request,$month = null, $day = null) {
        
        
        $year = getYearActive();
        if (!$month)
            $month = date('m');
        if (!$day)
            $day = 'all';

        $data = $this->getCharges($year,$month,$day);
        $lstMonthsSpanish = lstMonthsSpanish();
        unset($lstMonthsSpanish[0]);
        $months =  $lstMonthsSpanish;
        
        $totalBank = $totalCash = 0;
        $arrayDays=[];
        if ($day == 'all'){
            for($i=1;$i<=$data['endDay'];$i++)
                $arrayDays[$i]=['cash'=>0,'bank'=>0];
        } else $arrayDays[$day]=['cash'=>0,'bank'=>0];
        
        foreach ($data['charges'] as $i){
            $j = date('j', strtotime($i->date_payment));
            if ($i->type_payment == 'cash'){
                $arrayDays[$j]['cash'] += $i->import;
                $totalCash += $i->import;
            } else {
                $arrayDays[$j]['bank'] += $i->import;
                $totalBank += $i->import;
            }
        }
        foreach ($data['extrasCharges'] as $i){
            $j = date('j', strtotime($i->date));
            $arrayDays[$j]['cash'] += $i->import;
            $totalCash += $i->import;
        }
        
        $auxTime = $year.'-'.str_pad($month, 2, "0", STR_PAD_LEFT).'-';
        if ($day == 'all'){
            $auxTime .= '01';
            $yesterday = date('n/j',strtotime($auxTime .' -1 days'));
            $tomorrow = date('n/j',strtotime($auxTime));
        } else {
            $auxTime .= str_pad($day, 2, "0", STR_PAD_LEFT);
            $yesterday = date('n/j',strtotime($auxTime .' -1 days'));
            $tomorrow = date('n/j',strtotime($auxTime .' +1 days'));
        }
            
        return view('.admin.informes.cierresDiarios', [
            'arrayDays' => $arrayDays,
            'totalBank' => $totalBank,
            'totalCash' => $totalCash,
            'month' => $month,
            'months' => $months,
            'year' => $year,
            'day' => $day,
            'clients' => $data['clients'],
            'endDay' => $data['endDay'],
            'total' => $totalBank+$totalCash,
            'yesterday'=>$yesterday,
            'tomorrow'=>$tomorrow,
        ]);
    }

    public function informeCaja(Request $request) {
        
        $date = date('Y-m-d');
        $charges = \App\Models\Charges::where('import', '!=', 0)
                ->where('date_payment', '=', $date)
                ->where('type_payment', 'cash')
                ->sum('import');
        $oCashBox = \App\Models\CashBox::where('import', '!=', 0)
                ->where('date', '=', $date)->get();
        $total = $charges;
        foreach ($oCashBox as $item) {
            if ($item->typePayment == "INGRESO") {
                $total += $item->import;
            } else{
                $total -= $item->import;
            }
        }

      
        return view('.admin.informes.caja', [
            'totalCash' => $total,
            'date' => $date
        ]);
    }

    

}
