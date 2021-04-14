<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use DB;

class IncomesController extends Controller {

  public function index($year = "") {
    return view('admin.contabilidad.incomes.index');
  }

  private function getIncomeByType($year) {
    $resume = [];
    $charges = DB::select('SELECT MONTH(date_payment) as month, type_payment, SUM(import) AS import FROM charges WHERE YEAR(date_payment)=:year GROUP BY MONTH(date_payment), type_payment ORDER BY MONTh(date_payment)', ['year' => $year]);
    $inconmes = DB::select('SELECT MONTH(date) as month, typePayment as type_payment, SUM(import) AS import FROM incomes WHERE YEAR(date)=:year GROUP BY MONTH(date), typePayment ORDER BY MONTh(date)', ['year' => $year]);

    foreach ($charges as $value) {
      @$resume[$value->month][$value->type_payment] += $value->import;
    }

    foreach ($inconmes as $value) {
      @$resume[$value->month][$value->type_payment] += $value->import;
    }

    return $resume;
  }

  public function nuevo() {
    $stripe = StripeController::$stripe;
    return view('/admin/incomes/_form', ['stripe' => $stripe]);
  }

  public function create(Request $request) {

    $date = Carbon::createFromFormat('d-m-Y', $request->input('date'))->format('Y-m-d');
    $ingreso = new \App\Incomes();
    $ingreso->concept = $request->input('concept');
    $ingreso->type = $request->input('type');
    $ingreso->typePayment = $request->input('type_payment');
    $ingreso->import = (float) $request->input('import');
    $ingreso->date = $date;


    if ($request->input('type_payment') == "cash") {

      $cashBox = new \App\CashBox();
      $cashBox->concept = "cobro " . $request->input('type');
      $cashBox->import = (float) (float) $request->input('import');
      ;
      $cashBox->date = $date;
      $cashBox->comment = $request->input('concept');
      $cashBox->typePayment = "INGRESO CLIENTE";
      $cashBox->type = "INGRESO";
      $oldBalance = \App\CashBox::orderBy('id', 'desc')->get();

      $cashBox->balance = (float) $oldBalance[0]->balance + (float) (float) $request->input('import');

      $cashBox->save();
    } else {
      $dataToCharge = $request->input();

      $dataToCharge['import'] = $request->input('import');
      $dataToCharge['email'] = $request->input('email');

      if (isset($dataToCharge['stripeToken']))
        StripeController::makeCharge($dataToCharge);
    }

    if ($ingreso->save()) {
      return redirect()->back();
    }
  }

  /*

   */

  static function getSummaryYear($year, $full = 0) {

    $cobros = \App\Charges::whereYear('date_payment', '=', $year)->get();
    $types = \App\TypesRate::all();

    foreach ($types as $key => $type) {
      $total[$year]['t_'.$type->id] = [
          0,
          0,
          0,
          0,
          0,
          0,
          0,
          0,
          0,
          0,
          0,
          0,
          0
      ];
    }

    foreach ($cobros as $key => $cobro) {
      if ($cobro->rate)
      $total[$year]["clients"]['c_'.$cobro->rate->typeRate->name] = [
          0,
          0,
          0,
          0,
          0,
          0,
          0,
          0,
          0,
          0,
          0,
          0,
          0
      ];
    }

    foreach ($cobros as $key => $cobro) {
      if (!$cobro->rate) continue;
      $date = Carbon::createFromFormat('Y-m-d', $cobro->date_payment);
      if ( isset($total[$year][$cobro->rate->typeRate->name]))
      $total[$year][$cobro->rate->typeRate->name][$date->format('n')] += $cobro->import;

      if (isset($total[$year]["clients"][$cobro->rate->typeRate->name]))
      $total[$year]["clients"][$cobro->rate->typeRate->name][$date->format('n')] += 1;
    }

    return $total;
  }

  public static function getArrayClientMonth($value = '') {
    $array = array();
    $date = Carbon::now();
    for ($year = 2016; $year <= $date->copy()->format('Y'); $year++) {
      for ($month = 1; $month <= 12; $month++) {
        $array[$year][$month] = 0;
      }
    }

    return $array;
  }

  public static function getTypeIncomes($year) {
    $array[$year] = [
        'Cursos de Formación' => [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
            8 => 0,
            9 => 0,
            10 => 0,
            11 => 0,
            12 => 0
        ],
        'Eventos especiales Empresas' => [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
            8 => 0,
            9 => 0,
            10 => 0,
            11 => 0,
            12 => 0
        ],
        'Venta Material Deportivo' => [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
            8 => 0,
            9 => 0,
            10 => 0,
            11 => 0,
            12 => 0
        ],
        'Vending' => [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
            8 => 0,
            9 => 0,
            10 => 0,
            11 => 0,
            12 => 0
        ]
    ];
    return $array;
  }

}
