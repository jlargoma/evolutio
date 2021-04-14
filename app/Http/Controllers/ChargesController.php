<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use App\Models\Rates;
use App\Models\UserRates;
use App\Models\Charges;

class ChargesController extends Controller {

    public function updateCobro(Request $request, $id) {
        $charge = Charges::find($id);

        if (!$charge)
            return view('admin.popup_msg');
        $uRate = UserRates::where('id_charges', $charge->id)->first();

        if ($uRate) {
            $date = getMonthSpanish($uRate->rate_month, false) . ' ' . $uRate->rate_year;
        } else {
            $time = strtotime($charge->date_payment);
            $date = getMonthSpanish(date('n', $time), false) . ' ' . date('Y', $time);
        }

        return view('admin.charges.cobro_update', [
            'taxes' => Rates::all(),
            'rate' => Rates::find($charge->id_rate),
            'date' => $date,
            'user' => $charge->user,
            'importe' => $charge->import,
            'charge' => $charge
        ]);
    }

    public function updateCharge(Request $request, $id) {
        $charge = Charges::find($id);
        if (!$charge) {
            return redirect()->back()->withErrors(['cobro no encontrado']);
        }
        if ($request->input('deleted')) {
            $charge->delete();
            return redirect()->back()->with('success', 'cobro Eliminado');
        } else {
            $charge->type_payment = $request->input('type_payment');
            $charge->import = $request->input('importe');
            $charge->discount = $request->input('discount');
            $charge->save();

            return redirect()->back()->with('success', 'cobro actualizado');
        }
    }

    public function cobrar(Request $request) {

        $month = $request->input('fecha_pago', null);
        if ($month)
            $time = strtotime($month);
        else
            $time = time();

        $uID = $request->input('id_user', null);
        $rID = $request->input('id_rate', null);
        $tpay = ($request->input('type_payment') == "card") ? "banco" : $request->input('type_payment', 'cash');
        $value = $request->input('importe', 0);
        $disc = $request->input('discount', '0');
        

        $resp = $this->generateePayment($time, $uID, $rID, $tpay, $value, $disc);

        if ($resp[0] == 'error') {
            return redirect()->back()->withErrors([$resp[1]]);
        }
        return redirect()->back()->with('success', $resp[1]);
    }

    public function chargeUser(Request $request) {
        $month = $request->input('date_payment', null);
        if ($month)
            $time = strtotime($month);
        else
            $time = time();
        $uID = $request->input('id_user', null);
        $rID = $request->input('id_rate', null);
        $tpay = ($request->input('type_payment') == "card") ? "banco" : $request->input('type_payment', 'cash');
        $value = $request->input('importe', 0);
        $disc = $request->input('discount', 0);

        $resp = $this->generateePayment($time, $uID, $rID, $tpay, $value, $disc);

        if ($resp[0] == 'error') {
            return redirect()->back()->withErrors([$resp[1]]);
        }
        return redirect()->back()->with('success', $resp[1]);
    }

    private function generateePayment($time, $uID, $rID, $tpay, $value, $disc=0) {

        $month = date('Y-m-d', $time);
        $oUser = \App\Models\User::find($uID);
        if (!$oUser)
            return ['error', 'Usuario no encontrado'];


        $oRate = Rates::find($rID);
        if (!$oRate)
            return ['error', 'Tarifa no encontrada'];
        $dataMail = [
            'fecha_pago' => $month,
            'type_payment' => $tpay,
            'importe' => $value,
        ];
        if(!$disc) $disc = 0;
        //BEGIN PAYMENTS MONTH
        for ($i = 0; $i < $oRate->mode; $i++) {

            $oCobro = new Charges();
            $oCobro->id_user = $oUser->id;
            $oCobro->date_payment = date('Y-m-d');
            $oCobro->id_rate = $oRate->id;
            $oCobro->type_payment = $tpay;
            $oCobro->type = 1;
            $oCobro->import = $value;
            $oCobro->discount = $disc;
            $oCobro->type_rate = $oRate->type;
            $oCobro->save();

            /*             * ************************************************** */

            $oUserRate = UserRates::where('id_user', $oUser->id)
                    ->where('id_rate', $oRate->id)
                    ->where('rate_month', date('n', $time))
                    ->where('rate_year', date('Y', $time))
                    ->first();
            if ($oUserRate) {
                $oUserRate->id_charges = $oCobro->id;
                $oUserRate->save();
            } else { //si no tenia asignada la tarifa del mes
                $newRate = new UserRates();
                $newRate->id_user = $oUser->id;
                $newRate->id_rate = $oRate->id;
                $newRate->rate_year = date('Y', $time);
                $newRate->rate_month = date('n', $time);
                $newRate->id_charges = $oCobro->id;
                $newRate->save();
            }
            /*             * ************************************************ */
            if ($tpay == "cash") {

//                    $cashBox              = new \App\CashBox();
//                    $cashBox->concept     = "COBRO DE TARIFA";
//                    $cashBox->import      = (float) $oCobro->import;
//                    $cashBox->date        = $month;
//                    $cashBox->comment     = 'Cobro ' . $oRate->name . ' :' . $oUser->name;
//                    $cashBox->type        = "INGRESO";
//                    $cashBox->typePayment = "INGRESO CLIENTES";
//
//                    $oldBalance = \App\CashBox::orderBy('id', 'desc')->get();
//                    $cashBox->balance = (float) $oldBalance[0]->balance + (float) $oCobro->import;
//                    $cashBox->save();
                $statusPayment = "Pago realizado correctamente, por caja";
            } else {
                $statusPayment = "Pago realizado correctamente, por stripe/Banco";
            }
            //Next month
            $time = strtotime($month . ' +1 month');
            $month = date('Y-m-d', $time);
            $value = 0; //solo se factura el primer mes
            $disc = 0; //solo se factura el primer mes
        }
        //END PAYMENTS MONTH



        /*         * ************************************************************ */
        MailController::sendEmailPayRate($dataMail, $oUser, $oRate);
        return ['OK', $statusPayment];
    }

    public function getPriceTax(Request $request) {
        $tax = Rates::find($request->idTax);
        return $tax->price;
    }

}
