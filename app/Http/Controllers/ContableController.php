<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\Rates;
use App\Models\CoachSessions;
use Illuminate\Support\Facades\DB;


class ContableController extends AppController
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
      $data = $this->prepareTables();
      
      return view('backend.contabilidad.index',$data);
    }
    
    
    public function sales()
    {
      $data = $this->prepareTables();
      $year  = $data['year'];
      $months_empty = $data['months_empty'];
      unset($months_empty[0]);
      $temporadas_month = [
        ($year->year-2) => $data['months_empty'],
        ($year->year-1) =>  $data['months_empty'],
        ];
      
      foreach ($temporadas_month as $k=>$v){
        $oSales =  Sales::whereYear('date_emmit', '=', $k)->get();
        if ($oSales){
          foreach ($oSales as $s){
          $m = date('n', strtotime($s->date_emmit));
          $temporadas_month[$k][$m] += $s->total;
          }
        }
       
      }
      
      $temporadas_month[$year->year] = [];
      foreach ($data['total_byMonth'] as $k=>$v){
        if ($k != 0)   $temporadas_month[$year->year][$k] = $v;
      }
      
      $data['temporadas_month'] = $temporadas_month;
      $data['totalClientes'] = array_sum($data['clients_byMonth']);
      
      
      
      
      $oSalesBy_TypePay = Sales::select('pay_from','date_emmit',DB::raw('SUM(total) AS sum_total'))
              ->whereYear('date_emmit', '=', $year->year)
              ->groupBy('pay_from')->groupBy('date_emmit')->get();
      $salesBy_TypePay = [  ];
      $salesTotal = 0;
      if ($oSalesBy_TypePay){
        foreach ($oSalesBy_TypePay as $s){
          $type = $s->pay_from;
          if (trim($type) == '') $type = '-';
          
          $month = date('n', strtotime($s->date_emmit));
          if (!isset($salesBy_TypePay[$type]['total'])){
            $salesBy_TypePay[$type]['total'] = 0;
          }
          if (!isset($salesBy_TypePay[$type][$month])){
            $salesBy_TypePay[$type][$month] = 0;
          }
          
          $salesBy_TypePay[$type][$month] += $s->sum_total;
          $salesBy_TypePay[$type]['total'] += $s->sum_total;
          $salesTotal += $s->sum_total;
        }

        foreach ($salesBy_TypePay as $k=>$s){
          $salesBy_TypePay[$k]['percent'] = ceil(($salesBy_TypePay[$k]['total']/$salesTotal)*100);
        }
        
      }
      $data['salesBy_TypePay'] = $salesBy_TypePay;
      
      return view('backend.contabilidad.sales',$data);
    }
    
    
    function prepareTables(){
      $year = $this->getActiveYear();
      $lstMonths = lstMonthsSpanish();

      $oSales = Sales::whereYear('date_emmit', '=', $year->year)->get();
    
      $aRates = Rates::getRatesNameBy_id();
      $aRatesGroup = Rates::getRatesNameBy_type();
      $aRateType = \App\Models\RateTypes::getNameBy_id();
    
      $months_empty = array();
      for($i=0;$i<13;$i++) $months_empty[$i] = 0;
      
      $list_items = [];
      $lstS_byMonth = [];
      $total_byMonth = $months_empty;
      $clients_byMonth = $months_empty;
      $byPayType = [];
      foreach ($aRates as $r=>$v){
          $lstS[$r] = $months_empty;
      }
     
      if ($oSales){
        foreach ($oSales as $s){
          if (!isset($lstS[$s->id_rate])) $lstS[$s->id_rate] = [];
          
          $m = date('n', strtotime($s->date_emmit));
          
          if (!isset($lstS[$s->id_rate][$m])){
            $lstS[$s->id_rate][$m] = 0;
            $lstS[$s->id_rate][0] = 0;
          }
          
          $lstS[$s->id_rate][$m] += $s->total;
          $lstS[$s->id_rate][0]  += $s->total;
          $total_byMonth[$m] += $s->total;
          
          $clients_byMonth[$m]++;
          
          if (!isset($byPayType[$s->pay_from])) $byPayType[$s->pay_from] = 0;
          
          $byPayType[$s->pay_from] += $s->total;
        }
      }
      
      
      $total_byMonth[0] = array_sum($total_byMonth);
      if ($total_byMonth[0]<1)  $total_byMonth[0] = 1;
      
      $clients_byMonth[0] = array_sum($clients_byMonth);
      if ($clients_byMonth[0]<1)  $clients_byMonth[0] = 1;
      
      
      foreach ($aRatesGroup as $r=>$v){
        $months_aux = $months_empty;
        foreach ($v as $r2=>$v2){
          if (isset($lstS[$r2])){
            
            foreach ($lstS[$r2] as $m=>$v3)   $months_aux[$m] += $v3;
            
            $list_items[$r][$r2] = $lstS[$r2];
          }
        }
        
        $list_items[$r]['totals'] = $months_aux;
      }
//      dd($list_items);
      
      /***********************/
      /***  TEMPORADAS     ***/
      $temporadas = [
          ($year->year-2) => ['cuota'=>0,'otro'=>0],
          ($year->year-1) => ['cuota'=>0,'otro'=>0],
          ($year->year) => ['cuota'=>0,'otro'=>0],
          ];
      
      foreach ($temporadas as $k=>$v){
        $temporadas[$k]['cuota'] = Sales::whereYear('date_emmit', '=', $k)->where('type','Tarifas')->sum('total');
        $temporadas[$k]['otro'] = Sales::whereYear('date_emmit', '=', $k)->where('type','Otros')->sum('total');
      }
      /***  TEMPORADAS     ***/
      /***********************/
      unset($lstMonths[0]);
      
      
      $aColors = [];
      $aColors[1] = '151, 187, 205';
      $aColors[2] = '151, 187, 005';
      $aColors[3] = '151, 087, 205';
      $aColors[4] = '251, 187, 205';
      return [
        'temporadas' => $temporadas,
        'lstMonths' => $lstMonths,
        'months_empty' => $months_empty,
        'year' => $year,
        'aRates' => $aRates,
        'aRateType' => $aRateType,
        'aColors' => $aColors,
        'lstSales' => $list_items,
        'total_byMonth' => $total_byMonth,
        'byPayType' => $byPayType,
        'clients_byMonth' => $clients_byMonth
        ];
    
    }
    
     
    
    public function salarios(){
  
      $year = $this->getActiveYear();
      $lstMonths = lstMonthsSpanish();

      $oObj = CoachSessions::whereYear('date', '=', $year->year)->where('cancels',0)->get();
    
      $aEntrenadores = \App\Models\Entrenadores::getRatesNameBy_id();
      $aRatesTypes = \App\Models\RateTypes::getNameBy_id();
      $aRatesGroup = Rates::getRatestypeBy_id();
      $months_empty = array();
      for($i=0;$i<13;$i++){
        $months_empty[$i]  = 0;
        $months_empty2[$i] = ['t'=>0,'c'=>0];
      }
     
      
      $lst_items = [];
      $lst_byMonth = [];
      $lst_byRate = [];
      $byPayType = [];
      $lst_byMonth = $months_empty;
      foreach ($aEntrenadores as $k=>$v){
        foreach ($aRatesTypes as $k2=>$v2){
          $lst_items[$k][$k2] = $months_empty2;
        }
        
      }
      
      foreach ($aRatesTypes as $k2=>$v2){
          $lst_byRate[$k2] = $months_empty;
        }
        
      $total = 0;
      if ($oObj){
        foreach ($oObj as $obj){
          
          if (!isset($lst_items[$obj->id_user])){
            continue;
          }
          $rateType = isset($aRatesGroup[$obj->id_rate]) ? $aRatesGroup[$obj->id_rate] : 4;
          if (!isset($lst_items[$obj->id_user][$rateType])){
            continue;
          }
          $m = date('n', strtotime($obj->date));
//          if (!isset($lst_items[$obj->id_user][$obj->id_rate][$m])){
//            $lst_items[$obj->id_user][$obj->id_rate][$m] = ['t'=>0,'c'=>0];
//          }
          $lst_items[$obj->id_user][$rateType][$m]['t'] += $obj->total;
          $lst_items[$obj->id_user][$rateType][$m]['c'] += $obj->numb;
          $lst_byRate[$rateType][$m] += $obj->total;
          $lst_byMonth[$m] += $obj->total;
          $total += $obj->total;
        }
      }
      
     

      $totals = [];
      foreach ($lst_items as $user=>$rates){
        
        $totals[$user] = $months_empty2;
        $aux_c = $aux_t = 0;
        foreach ($rates as $rate=>$months){
          $aux_r_c = $aux_r_t = 0;
          foreach ($months as $k=>$v){
            $totals[$user][$k]['t'] += $v['t'];
            $totals[$user][$k]['c'] += $v['c'];
            $aux_c += $v['c'];
            $aux_t += $v['t'];
            $aux_r_c += $v['c'];
            $aux_r_t += $v['t'];
          }
          
          $lst_items[$user][$rate][0]['c'] = $aux_r_c;
          $lst_items[$user][$rate][0]['t'] = $aux_r_t;
        
        }
        $totals[$user][0]['c'] = $aux_c;
        $totals[$user][0]['t'] = $aux_t;
        
      }

      $lst_byMonth[0] = array_sum($lst_byMonth);
      if ($lst_byMonth[0]<1)  $lst_byMonth[0] = 1;
      
      foreach ($lst_byRate as $r=>$v){
        $lst_byRate[$r][0] = array_sum($lst_byRate[$r]);
        if ($lst_byRate[$r][0]<1)  $lst_byRate[$r][0] = 1;
      }
      
      if ($total<1)  $total = 1;
//      dd($lst_byMonth);
//      dd($aEntrenadores,$totals);
      
      $lst_byRate[0] = array_sum($lst_byRate);
      if ($lst_byRate[0]<1)  $lst_byRate[0] = 1;
      
      
      /***********************/
      /***  TEMPORADAS     ***/
      $temporadas = [
          ($year->year-2) => $months_empty,
          ($year->year-1) => $months_empty,
          ];
      
      foreach ($temporadas as $k=>$v){
        $oObj = CoachSessions::whereYear('date', '=', $k)->where('cancels',0)->get();
        if ($oObj){
          foreach ($oObj as $obj){
            $m = date('n', strtotime($obj->date));
            $temporadas[$k][$m] += $obj->total;
          }
        }
      }
      
      $temporadas[$year->year] = $lst_byMonth;
      foreach ($temporadas as $k=>$v){
        unset($temporadas[$k][0]);
      }
      
      /***  TEMPORADAS     ***/
      /***********************/
      unset($lstMonths[0]);
     
      
      $aColors = [];
      $aColors[1] = '151, 187, 205';
      $aColors[2] = '151, 187, 005';
      $aColors[3] = '151, 087, 205';
      $aColors[4] = '251, 187, 205';
      $data = [
        'temporadas' => $temporadas,
        'lstMonths' => $lstMonths,
        'months_empty' => $months_empty,
        'year' => $year,
        'aRateType' => $aRatesTypes,
        'aEntrenadores' => $aEntrenadores,
        'aColors' => $aColors,
        'lst_items' => $lst_items,
        'totals' => $totals,
        'total' => $total,
        'lst_byRate' => $lst_byRate,
        ];
      return view('backend.contabilidad.salarios',$data);
    }
    
}
