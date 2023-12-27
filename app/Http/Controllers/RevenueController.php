<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use DB;
use App\Models\Charges;
use App\Models\UserRates;
use App\Models\Incomes;
use App\Models\Expenses;
use App\Services\IncomesService;

class RevenueController extends Controller {
    public function index() {


        $year = getYearActive();
        $monts = lstMonthsSpanish();
        unset($monts[0]);
        $mm = [];
        foreach ($monts as $k=>$v) $mm[$k] = 0;


        $payMethod = payMethod();
        
        // $lst = UserRates::where('rate_year',$year)->with('charges')->get();


        return view('revenue.index',[
            'year'=>$year,
            'monts'=>$monts,
            'payMethod'=>$payMethod,
            'byTemp'=>$this->byTemp($year),
            'ratesCharges'=>$this->ratesCharges($year,$payMethod),
        ]);
    }

    function byTemp($year){
        $respo = [];
        $year-=2;
        $respo[$year] = [
            'ing' => UserRates::where('rate_year',$year)->sum('charged'),
            'exp' => Expenses::whereYEar('date',$year)->where('type','!=','distribucion')->sum('import'),
        ]; 
        $year++;
        $respo[$year] = [
            'ing' => UserRates::where('rate_year',$year)->sum('charged'),
            'exp' => Expenses::whereYEar('date',$year)->where('type','!=','distribucion')->sum('import'),
        ]; 
        $year++;
        $respo[$year] = [
            'ing' => UserRates::where('rate_year',$year)->sum('charged'),
            'exp' => Expenses::whereYEar('date',$year)->where('type','!=','distribucion')->sum('import'),
        ]; 

        foreach($respo as $y=>$v)
            $respo[$y]['result'] = $v['ing'] - $v['exp'];
        
        return $respo;
    }

    function ratesCharges($year,$payMethod){
        $pay = $noPay = 0;

        foreach($payMethod as $k=>$v) $payMethod[$k] = 0;
        $lst = UserRates::where('rate_year',$year)->get();
        foreach($lst as $v){
            if ($v->charged) $pay += $v->charged;
            else $noPay += $v->price;
        }
        return ['pay'=>$pay,'no_pay'=>$noPay,'method'=>$payMethod];
    }
}

