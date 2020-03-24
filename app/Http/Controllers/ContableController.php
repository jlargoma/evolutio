<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContableController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('backend.contabilidad.index');
    }
    
    
        /**
     */
    public function salarioMes($year = '')
    {
      $year = 2019;
      if ($year == "") {
            $date = Carbon::now();
            $year = $date->format('Y');
        }
      
        
         
      $rates = \App\Rates::all();
      $rateLst = [];
      $rateTipes = [];
      foreach ($rates as $i){
        $rateLst[$i->id] = $i->typeRate->id;
        $rateTipes[$i->typeRate->id] = $i->typeRate->name;
      }
      
//      dd($rateTipes);
        
      $lstMonths = lstMonths(Carbon::parse('first day of January '.$year),$year.'-12-31','Y-m');
      $sessionsByYear = \App\CoachSessions::whereYear('date','=',$year)->get();
      $byUsers = [];
      if ($sessionsByYear){
        foreach ($sessionsByYear as $item){
          
          $uID = $item['id_user'];
          $id_rate = $item['id_rate'];
          if (!isset($byUsers[$uID])){
            $byUsers[$uID] = [
                'total_sal' => 0,
                'total_session' => 0,
                'name'=>$item['name'],
            ];
            //by  type of rates
            for($i=1;$i<4;$i++){
              $byUsers[$uID]['tr_'.$i] = false;
              $byUsers[$uID]['total_sal_'.$i] = 0;
              $byUsers[$uID]['total_session_'.$i] = 0;
            }
          }
          
          $month = date('Y-m', strtotime($item['date']));
          if (!isset($byUsers[$uID][$month])){
            $byUsers[$uID][$month] = ['sal'=>0,'ses'=>0];
          }
          
          if (!isset($rateLst[$id_rate])) continue;
          
          $tRate = $rateLst[$id_rate]; 
          
          if (!isset($byUsers[$uID][$month][$tRate])){
             $byUsers[$uID][$month][$tRate] = ['sal'=>0,'ses'=>0];
          }
          $byUsers[$uID]['tr_'.$tRate] = true;
          
          $salary = round($item['numb']*$item['cost']);
        
          
          $byUsers[$uID][$month]['ses'] += $item['numb'];
          $byUsers[$uID][$month]['sal'] += $salary;
          $byUsers[$uID][$month][$tRate]['ses'] += $item['numb'];
          $byUsers[$uID][$month][$tRate]['sal'] += $salary;
          $byUsers[$uID]['total_sal'] += $salary;
          $byUsers[$uID]['total_session'] += $item['numb'];
          $byUsers[$uID]['total_sal_'.$tRate] += $salary;
          $byUsers[$uID]['total_session_'.$tRate] += $item['numb'];
          
     
        }
      }
      
      
      /**
       * Data to resume and grafics
       */
      
      $resumeTable = DB::select(     
              'SELECT id_rate,date,SUM(total) as total_cost '
              . 'FROM `coach_sessions` '
              . 'WHERE YEAR(date)=:year '
              . 'GROUP BY id_rate,date'
              , ['year' => $year]);
      $resume = ['t_1'=>0,'t_2'=>0,'t_3'=>0];
      if ($resumeTable){
        foreach ($resumeTable as $item){
          $dateK = date('Y-m', strtotime($item->date));
          if (!isset($resume[$dateK])) 
            $resume[$dateK] = ['total'=>0,'t_1'=>0,'t_2'=>0,'t_3'=>0];
          
          
          $resume[$dateK]['total'] += $item->total_cost;
          $id_rate = $item->id_rate;
          if (!isset($rateLst[$id_rate])) continue;
          
          $tRate = $rateLst[$id_rate]; 
          $resume[$dateK]['t_'.$tRate] += $item->total_cost;
          $resume['t_'.$tRate] += $item->total_cost;
        }
      }
      
      //other years
      $resumeTable = DB::select(     
              'SELECT date,SUM(total) as total_cost '
              . 'FROM `coach_sessions` '
              . 'WHERE YEAR(date)=:year '
              . 'GROUP BY id_rate,date'
              , ['year' => $year-1]);
      $resume_2 = [];
      if ($resumeTable){
        foreach ($resumeTable as $item){
          $dateK = date('Y-m', strtotime($item->date));
          if (!isset($resume_2[$dateK])) $resume_2[$dateK] = 0;
          $resume_2[$dateK] += $item->total_cost;
        }
      }
      //render the info to the grafic
      $aux = '';
      foreach ($lstMonths as $k=>$v){
        if(isset($resume_2[$k]))
          $aux .= "'" . round($resume_2[$k]) . "',";
        else
          $aux .="'0',";
      }
      $resume_2 = $aux;
      
      $resumeTable = DB::select(     
              'SELECT date,SUM(total) as total_cost '
              . 'FROM `coach_sessions` '
              . 'WHERE YEAR(date)=:year '
              . 'GROUP BY id_rate,date'
              , ['year' => $year-2]);
      $resume_3 = [];
      if ($resumeTable){
        foreach ($resumeTable as $item){
          $dateK = date('Y-m', strtotime($item->date));
          if (!isset($resume_3[$dateK])) $resume_3[$dateK] = 0;
          
          $resume_3[$dateK] += $item->total_cost;
        }
      }
      //render the info to the grafic
      $aux = '';
      foreach ($lstMonths as $k=>$v){
        if(isset($resume_3[$k]))
          $aux .= "'" . round($resume_3[$k]) . "',";
        else
          $aux .="'0',";
      }
      $resume_3 = $aux;
      

       return view('admin.contabilidad.salarios',[
           'year' => $year,
           'byUsers' => $byUsers,
           'rateTipes' => $rateTipes,
           'year' => $year,
           'lstMonths' => $lstMonths,
           'resume' => $resume,
           'resume_2' => $resume_2,
           'resume_3' => $resume_3,
           ]);
    }
    
    public function ventasMes($year = '')
    {
//      $year = 2019;
//      if ($year == "") {
//            $year = $date->format('Y');
//        }
      
      $date = Carbon::now();
      $year = $date->format('Y');
      $start = $date->copy()->subMonth(5);
        
         
      $rates = \App\Rates::all();
      $rateLst = [];
      $rateTipes = [];
      foreach ($rates as $i){
        $rateLst[$i->id] = $i->typeRate->id;
        $rateTipes[$i->typeRate->id] = $i->typeRate->name;
      }
      $lstMonths = lstMonths($start,$date,'Y-m');
      
      $salesTotal = 0;
      $oSalesBy_type = Sales::select('type','date_emmit',DB::raw('SUM(total) AS sum_total'))
              ->groupBy('type')->groupBy('date_emmit')->get();
      $salesBy_type = [
          'Tarifas'=>[
              'percent'=>0,
              'total'=>0,
          ],
          'Otros'=>[
              'percent'=>0,
              'total'=>0,
          ],
      ];
      
      if ($oSalesBy_type){
        foreach ($oSalesBy_type as $s){
          $type = $s->type;
          $month = date('Y-m', strtotime($s->date_emmit));
          if (!isset($salesBy_type[$type]['total'])){
            $salesBy_type[$type]['total'] = 0;
          }
          if (!isset($salesBy_type[$type][$month])){
            $salesBy_type[$type][$month] = 0;
          }
          
          $salesBy_type[$type][$month] += $s->sum_total;
          $salesBy_type[$type]['total'] += $s->sum_total;
          $salesTotal += $s->sum_total;
        }
        
        if ($salesTotal>0){
          foreach ($salesBy_type as $k=>$s){
            if (isset($salesBy_type[$k]['total']))
              $salesBy_type[$k]['percent'] = ceil(($salesBy_type[$k]['total']/$salesTotal)*100);
          }
        }
        
      }
      
      /**************************************************/
      
      $oSalesBy_tGroup = Sales::select('tarifa_g','date_emmit',DB::raw('SUM(total) AS sum_total'))
              ->groupBy('tarifa_g')->groupBy('date_emmit')->get();
      $salesBy_tGroup = [  ];
            
      if ($oSalesBy_tGroup){
        foreach ($oSalesBy_tGroup as $s){
          $type = $s->tarifa_g;
          if (trim($type) == '') $type = '-';
          
          $month = date('Y-m', strtotime($s->date_emmit));
          if (!isset($salesBy_tGroup[$type]['total'])){
            $salesBy_tGroup[$type]['total'] = 0;
          }
          if (!isset($salesBy_tGroup[$type][$month])){
            $salesBy_tGroup[$type][$month] = 0;
          }
          
          $salesBy_tGroup[$type][$month] += $s->sum_total;
          $salesBy_tGroup[$type]['total'] += $s->sum_total;
          
        }

        foreach ($salesBy_tGroup as $k=>$s){
          $salesBy_tGroup[$k]['percent'] = ceil(($salesBy_tGroup[$k]['total']/$salesTotal)*100);
        }
        
      }
      
      /**************************************************/
      
      $oSalesBy_TypePay = Sales::select('pay_from','date_emmit',DB::raw('SUM(total) AS sum_total'))
              ->groupBy('pay_from')->groupBy('date_emmit')->get();
      $salesBy_TypePay = [  ];
      
      if ($oSalesBy_TypePay){
        foreach ($oSalesBy_TypePay as $s){
          $type = $s->pay_from;
          if (trim($type) == '') $type = '-';
          
          $month = date('Y-m', strtotime($s->date_emmit));
          if (!isset($salesBy_TypePay[$type]['total'])){
            $salesBy_TypePay[$type]['total'] = 0;
          }
          if (!isset($salesBy_TypePay[$type][$month])){
            $salesBy_TypePay[$type][$month] = 0;
          }
          
          $salesBy_TypePay[$type][$month] += $s->sum_total;
          $salesBy_TypePay[$type]['total'] += $s->sum_total;
          
        }

        foreach ($salesBy_TypePay as $k=>$s){
          $salesBy_TypePay[$k]['percent'] = ceil(($salesBy_TypePay[$k]['total']/$salesTotal)*100);
        }
        
      }
      
      /**************************************************/
      /**************************************************/
      
      $oSalesBy_FISIO = Sales::select('pay_from','date_emmit',DB::raw('SUM(total) AS sum_total'))
              ->where('tarifa_g','FISIOTERAPIA')->groupBy('pay_from')->groupBy('date_emmit')->get();
      $salesBy_FISIO = [  ];
      $salesBy_FISIOTotal = 0;
      
      if ($oSalesBy_FISIO){
        foreach ($oSalesBy_FISIO as $s){
          $type = $s->pay_from;
          if (trim($type) == '') $type = '-';
          
          $month = date('Y-m', strtotime($s->date_emmit));
          if (!isset($salesBy_FISIO[$type]['total'])){
            $salesBy_FISIO[$type]['total'] = 0;
          }
          if (!isset($salesBy_FISIO[$type][$month])){
            $salesBy_FISIO[$type][$month] = 0;
          }
          
          $salesBy_FISIO[$type][$month] += $s->sum_total;
          $salesBy_FISIO[$type]['total'] += $s->sum_total;
          $salesBy_FISIOTotal += $s->sum_total;
          
        }

        foreach ($salesBy_FISIO as $k=>$s){
          $salesBy_FISIO[$k]['percent'] = round(($salesBy_FISIO[$k]['total']/$salesBy_FISIOTotal)*100,2);
        }
        
      }
      
      
      /**
       * Grafics
       */
      $lstMonthsYear = lstMonths(Carbon::parse('first day of January '.$year),$year.'-12-31','m');
      $resume = [];
      for($i=0;$i<3;$i++){
        $resumeTable = DB::select(     
                'SELECT date_emmit,SUM(total) as total_cost '
                . 'FROM `sales` '
                . 'WHERE YEAR(date_emmit)=:year '
                . 'GROUP BY date_emmit'
                , ['year' => ($year-$i)]);
        $resume_aux = [];
        if ($resumeTable){
          foreach ($resumeTable as $item){
            $dateK = date('m', strtotime($item->date_emmit));
            if (!isset($resume_aux[$dateK])) $resume_aux[$dateK] = 0;

            $resume_aux[$dateK] += $item->total_cost;
          }
        }
        //render the info to the grafic
        $aux = '';
        foreach ($lstMonthsYear as $k=>$v){
          if(isset($resume_aux[$k]))
            $aux .= "'" . round($resume_aux[$k]) . "',";
          else
            $aux .="'0',";
        }
        $resume[] = $aux;
        
//        dd($resume_aux,$lstMonthsYear);
      }
      
     
      
//      dd($resume);
      
      return view('admin.contabilidad.ventas.index',[
           'year' => $year,
           'rateTipes' => $rateTipes,
           'year' => $year,
           'lstMonths' => $lstMonths,
           'lstMonthsYear' => $lstMonthsYear,
           'resume' => $resume,
           'salesTotal' => $salesTotal,
           'salesBy_type' => $salesBy_type,
           'salesBy_tGroup' => $salesBy_tGroup,
           'salesBy_TypePay' => $salesBy_TypePay,
           'salesBy_FISIO' => $salesBy_FISIO,
      
           ]);
    }
    
}
