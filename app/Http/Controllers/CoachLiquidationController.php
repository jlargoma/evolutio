<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use \Carbon\Carbon;
use App\Models\CoachLiquidation;
use App\Models\User;
use App\Models\CoachRates;
use App\Models\Dates;
use Barryvdh\DomPDF\Facade as PDF;

class CoachLiquidationController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }
    
    public function coachLiquidation(Request $request)
    {
      $liquidation = null;
      if ( !empty($request->id_liquidation) ) {
        $liquidation = CoachLiquidation::find($request->id_liquidation);
      }
      if (!$liquidation){
        $liquidation = new CoachLiquidation();
        $liquidation->id_coach = $request->id_coach;
        $liquidation->total = $request->importe;
        $liquidation->date_liquidation = Carbon::createFromFormat('Y-m-d', $request->date_liquidation)->copy()->format('Y-m-d');
      }
      
      if ($request->importe == "") {
        $liquidation->total = 0;
      } else {
        $liquidation->total = $request->importe;
      }

      if ($liquidation->save())	return 'OK';
    	else return "Error al guardar, intentelo de nuevo más tarde!";
    }
    
    
    public function store(Request $request)
    {
        $id_coach = $request->id_coach;
        $importe  = $request->importe;
        $date     = $request->date.'-01';
        
        $oLiq = CoachLiquidation::where('id_coach',$id_coach)
                ->where('date_liquidation',$date)->first();
        if (!$oLiq){
            $oLiq = new CoachLiquidation();
            $oLiq->id_coach = $id_coach;
            $oLiq->date_liquidation = $date;
        }
        $oLiq->total = intval($importe);
        if ($oLiq->save()) return 'OK';
        
        return "Error al guardar, intentelo de nuevo más tarde!";
    }
    
      
    private function liquMensual($id,$year,$month) {
        $lstMonts = lstMonthsSpanish();
        $typePT = 2;
        
        $taxCoach = CoachRates::where('id_user', $id)->first();
        
        $ppc = $salary = 0;
        if ($taxCoach){
            $ppc = $taxCoach->ppc;
            $salary = $taxCoach->salary;
        }
        /**********************************************************/
        $oTurnos = Dates::where('id_coach',$id)
                ->whereMonth('date','=',$month)
                ->whereYear('date','=', $year)
                ->join('users_rates','users_rates.id','=','id_user_rates')
                ->whereNotNull('users_rates.id_charges')
                ->with('user')->with('service')
                ->orderBy('date')
                ->get();

        $totalClase = array();
        $pagosClase = array();
        $classLst = [];
        
        if ($oTurnos){
            foreach ($oTurnos as $item) {
                $key= $item->service->id;
                if (!isset($classLst[$key])){
                 $classLst[$key] = $item->service->name;
                 $pagosClase[$key] = [];
                 $totalClase[$key] = 0;
                }
               
                if ($item->service->type == $typePT) {
                    /* 50€ precio de entrenamiento personal */
                    $totalClase[$key] += 50;
                }else{
                    $totalClase[$key] += $ppc;
                }
               
                $time = strtotime($item->date);
                $className  = date('d',$time).' de '.$lstMonts[date('n',$time)];
                $className .= ' a las '.date('h a',$time);
                $className .= ' (cliente : '.$item->user->name.')';
                $pagosClase[$key][] = $className;
            }
        }
        
        //-----------------------------------------------------------//
        $oExpenses = \App\Models\Expenses::where('to_user',$id)
                ->whereMonth('date','=',$month)
                ->whereYear('date','=', $year)
                ->orderBy('date')
                ->get();
        $lstExpType = \App\Models\Expenses::getTypes();
        if ($oExpenses){
            foreach ($oExpenses as $item) {
              $key= $item->type;
              if (!isset($classLst[$key])){
               $classLst[$key] = $lstExpType[$key];
               $pagosClase[$key] = [];
               $totalClase[$key] = 0;
              }
              $totalClase[$key] += $item->import;
              $time = strtotime($item->date);
              $className  = date('d',$time).' de '.$lstMonts[date('n',$time)];
              $pagosClase[$key][] = $className;
            }
        }
            
        return compact('pagosClase','totalClase','classLst','ppc','salary');
        
    }
    public function liquidEntrenador($id, $date = null)
    {
        
        if (!$date) $date = date('Y-m');
        $aux = explode('-', $date);
        if (count($aux) == 2){
            $year  = $aux[0];
            $month = $aux[1];
        } else {
            $year = getYearActive();
            $month = date('m');
        }
        /**********************************************************/
        $lstMonts = lstMonthsSpanish();
        $aMonths  = [];
        foreach ($lstMonts as $k=>$v){
            if ($k>0)    $aMonths[$year.'-'.str_pad($k, 2, "0", STR_PAD_LEFT)] = $v;
        }
        
        /**********************************************************/
        
        $liqLst = [];
        $oLiq = CoachLiquidation::where('id_coach',$id)
                    ->whereYear('date_liquidation' ,'=', $year)
                    ->get();
        $anual = 0;
        if ($oLiq){
            foreach ($oLiq as $item){
              $aux = substr($item->date_liquidation,0,7);
              $liqLst[$aux] =$item->total;
              $anual+=$item->total;
            }
        }
        /**********************************************************/
        $payMonth = [];
        $oExpenses = \App\Models\Expenses::where('to_user',$id)
                ->whereYear('date','=', $year)
                ->orderBy('date')
                ->get();
        $lstExpType = \App\Models\Expenses::getTypes();
        if ($oExpenses){
            foreach ($oExpenses as $item) {
              $aux = substr($item->date,0,7);
              if (!isset($payMonth[$aux])) $payMonth[$aux] = 0;
              $payMonth[$aux]+= $item->import;
            }
        }
        //-----------------------------------------------------//
        //---- BEGIN liquidación mensual    -------------------//
        $aLiq = null;
        $liqByM = [];
        $now = date('m');
        foreach ($aMonths as $k=>$v){
          $am = substr($k, 5,2);
          if ($am>$now){
            $liqByM[$k] = 0;
          } else {
            $aux = $this->liquMensual($id,$year,$am);
            if ($am==$month) $aLiq =$aux;
            $liqByM[$k] = $aux['salary']+ array_sum($aux['totalClase']);
          }
        }
        //---- END liquidación mensual    ---------------------//
        //-----------------------------------------------------//
        

        return view('/admin/usuarios/entrenadores/liquidacion',[ 
                                                'user' => User::find($id),
                                                'pagosClase' => $aLiq['pagosClase'],
                                                'totalClase' => $aLiq['totalClase'],
                                                'salary' => $aLiq['salary'],
                                                'classLst' => $aLiq['classLst'],
                                                'payMonth' => $payMonth,
                                                'liqByM' => $liqByM,
                                                'date' => $date,
                                                'aMonths'=>$aMonths,
                                                'year' => $year,
                                                'liqLst' => $liqLst,
                                                'anual' => $anual,
                                                ]);


    }
    
     public function enviarEmailLiquidacion($id, $date = null){
        
                 
        if (!$date) $date = date('Y-m');
        $aux = explode('-', $date);
        if (count($aux) == 2){
            $year  = $aux[0];
            $month = $aux[1];
        } else {
            $year = getYearActive();
            $month = date('m');
        }
        
        $aData = $this->liquMensual($id,$year,$month);
        $user = User::find($id);
        $aData['user'] = $user;
        $aData['mes'] = getMonthSpanish($month,false).' '.$year;
//        $view =  \View::make('pdfs.liquidacion', $aData)->render();
//        echo $view;die;

        $fileName = str_replace(' ','-','liquidacion '.$aData['mes'].' '. strtoupper($user->name));
        $routePdf = storage_path('/app/liquidaciones/'. urlencode($fileName).'.pdf');
        $pdf = PDF::loadView('pdfs.liquidacion', $aData);
        $pdf->save($routePdf);
        
//        return $pdf->download('invoice.pdf');

//
//        return view('emails._liquidacion_coach',['user' => $user,'mes'=>$aData['mes']]);
        $emailing = $user->email;
        try{
        \Mail::send(['html' => 'emails._liquidacion_coach'],['user' => $user,'mes'=>$aData['mes']], function ($message) use ($emailing, $fileName,$routePdf)  {
                setlocale(LC_TIME, "ES");
                setlocale(LC_TIME, "es_ES");
                $message->subject($fileName);
                $message->from(config('mail.from.address'), config('mail.from.name'));
                $message->to($emailing);
                $message->attach($routePdf);
            });
        } catch (\Exception $ex) {
          dd($ex);
        }
            
        return 'OK';

            
    }
}
