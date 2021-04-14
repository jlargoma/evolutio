<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use App\Models\User;
use App\Models\Dates;
use App\Models\Rates;
use App\Models\UserRates;
use App\Models\CoachTimes;
use Illuminate\Support\Facades\Mail;

class DatesController extends Controller {

    public function index($month = "") {

        if ($month == "") {
            $month = Carbon::now()->startOfMonth();
            $startWeek = Carbon::now()->startOfWeek();
            $endWeek = Carbon::now()->endOfWeek();
        } else {
            $startWeek = Carbon::createFromFormat('Y-m-d', $month)->startOfWeek();
            $endWeek = Carbon::createFromFormat('Y-m-d', $month)->endOfWeek();

            $month = Carbon::createFromFormat('Y-m-d', $month)->startOfMonth();
        }

        return view('admin/dates/index', [
            'month' => $month,
            'week' => $startWeek,
            'selectedWeek' => $startWeek->format("W")
        ]);
    }

    public function delete($id) {
        $date = Dates::find($id);
        if ($date->delete()) {
            return redirect()->back();
        }
    }

    public function create(Request $request) {

        $validated = $this->validate($request, [
            'date' => 'required',
            'id_rate' => 'required',
            'id_user' => 'required',
            'id_coach' => 'required',
                ], [
            'date.required' => 'Fecha requerida',
            'id_rate.required' => 'Tarifa requerida',
            'id_user.required' => 'Usuario requerido',
            'id_coach.required' => 'Coach requerido',
        ]);
        $ID = $request->input('idDate', null);
        $id_user = $request->input('id_user');
        $id_coach = $request->input('id_coach');
        $oCarbon = Carbon::createFromFormat('d-m-Y H:00:00', $request->input('date') . " " . $request->input('hour') . ":00:00");
        $date = $oCarbon->format('Y-m-d H:i:00');

        /*         * *********************************************************** */
        $alreadyExit = Dates::where('date', $date)
                        ->where('id', '!=', $ID)
                        ->where(function ($query) use ($id_user, $id_coach) {
                            $query->where('id_user', $id_user)
                            ->orWhere('id_coach', $id_coach);
                        })->first();
        if ($alreadyExit) {
            if ($alreadyExit->id_user == $id_user)
                $msg = 'Usuario ocupado';
            if ($alreadyExit->id_coach == $id_coach)
                $msg = 'Personal ocupado';
            return redirect()->back()->withErrors([$msg]);
        }
        /*         * *********************************************************** */
        $coachTimes = CoachTimes::where('id_coach', $id_coach)->first();
        if ($coachTimes) {
            $t_control = json_decode($coachTimes->times, true);
            $aux_d = $oCarbon->format('w');
            $aux_h = $oCarbon->format('H');
            if (isset($t_control[$aux_d])) {
                if (isset($t_control[$aux_d][$aux_h]) && $t_control[$aux_d][$aux_h] == 0)
                    return redirect()->back()->withErrors(['Horario no disponible']);
            }
        }
        /*         * *********************************************************** */




        if ($ID) {
            $oObj = Dates::find($ID);
        } else {
            $oObj = new Dates();
        }
        $oObj->id_rate = $request->input('id_rate');
        $oObj->id_user = $id_user;
        $oObj->id_coach = $id_coach;
        $oObj->date_type = $request->input('date_type');
        $oObj->date = $date;
        $oObj->created_at = $date;
        if (!$ID) {
            $oObj->charged = 0;
            $oObj->status = 0;
            $oObj->updated_at = $date;
        }
        if ($oObj->save()) {
            $oUser = User::find($oObj->id_user);
            $uEmail = $request->input('email');
            if ($oUser && $oUser->email != $uEmail) {
                $oUser->email = $uEmail;
                $oUser->save();
            }

            /*             * ************************************************************** */
            $timeCita = strtotime($oObj->date);
            $service = Rates::find($oObj->id_rate);
            $coach = User::find($oObj->id_coach);

            $mailData = [
                'dayCita' => getNameDay(date('w', $timeCita), false) . ' ' . date('d', $timeCita),
                'monthCita' => getMonthSpanish(date('n', $timeCita), false),
                'hourCita' => date('g A', $timeCita),
                'service' => $service->name,
                'coach' => $coach->name,
                'user' => $oUser
            ];
//            return view('emails._create_cita', $mailData);

            $sended = Mail::send('emails._create_cita', $mailData, function ($message) use ($uEmail) {
                        $message->subject('Cita en Evolutio');
                        $message->from('info@evolutio.fit', 'Evolutio');
                        $message->to($uEmail);
                    });

            /*             * ************************************************************** */
            return redirect()->back();
        }
    }

    public function chargeaaaaaaaaaaaas(Request $request) {
        switch ($request->input('type')) {
            case 1: //si el cobro es mediante bono
                $date = Dates::find($request->input('idDate'));
                $dayDate = Carbon::createFromFormat('Y-m-d H:i:s', $date->date);
                $user = $date->user;
                $service = $date->service;

                $classe = Classes::where('type_rate', $service->id)->get();
                $scheduleSelected = Schedules::where('type', $service->id)->where('hour', $dayDate->copy()->format('H'))->get();
                /* Apuntamos la clase en la base de asistencias */
                $newAssitance = new Assistance();
                $newAssitance->id_user = $user->id;
                $newAssitance->id_schedule = $scheduleSelected[0]->id;
                $newAssitance->date_assistance = $dayDate->copy()->format('Y-m-d H:i:s');
                // $newAssitance->save();

                /* Apuntamos la clase al coach */
                $newCoachClasses = new CoachClasses();
                $newCoachClasses->id_class = $classe[0]->id;
                $newCoachClasses->id_user = $date->id_coach;
                $newCoachClasses->date = $dayDate->copy()->format('Y-m-d H:i:s');
                // $newCoachClasses->save();
                // Actualizamos la cita
                $date->status = 1;
                $date->charged = 1;

                if ($newAssitance->save() && $newCoachClasses->save() && $date->save()) {
                    return redirect()->back();
                }
                break;

            case 2:

                $date = Dates::find($request->input('idDate'));
                $dayDate = Carbon::createFromFormat('Y-m-d H:i:s', $date->date);
                $user = $date->user;
                $service = $date->service;
                $rate = Rates::find($request->input('id_rate'));

                $classe = Classes::where('type_rate', $service->id)->get();
                $scheduleSelected = Schedules::where('type', $service->id)
                        ->where('hour', $dayDate->copy()->format('G'))
                        ->where('day', $dayDate->copy()->format('N'))
                        ->get();

                /* Apuntamos la clase en la base de asistencias */
                $newAssitance = new Assistance();
                $newAssitance->id_user = $user->id;
                foreach ($scheduleSelected as $key => $value) {
                    $newAssitance->id_schedule = $value->id;
                }
                $newAssitance->date_assistance = $dayDate->copy()->format('Y-m-d H:i:s');

                /* Apuntamos la clase al coach */
                $newCoachClasses = new CoachClasses();
                $newCoachClasses->id_class = $classe[0]->id;
                $newCoachClasses->id_user = $date->id_coach;
                $newCoachClasses->date = $dayDate->copy()->format('Y-m-d H:i:s');

                //Asignamos y cobramos la tarifa que nos envia.


                $cobro = new \App\Models\Charges();
                $cobro->id_user = $user->id;
                $cobro->date_payment = $dayDate->copy()->format('Y-m-d');
                $cobro->id_rate = $rate->id;
                $cobro->type_payment = $request->input('type_pay');
                $cobro->type = 1;
                $cobro->import = $rate->price;
                $cobro->discount = 0;
                $cobro->type_rate = $rate->type;

                $userRate = new UserRates();
                $userRate->id_user = $user->id;
                $userRate->id_rate = $rate->id;
                $userRate->created_at = $dayDate->copy()->format('Y-m-d');
                $userRate->updated_at = $dayDate->copy()->format('Y-m-d');

                if ($request->input('type_pay') == "cash") {

                    $cashBox = new \App\Models\CashBox();
                    $cashBox->concept = 'Cobro de cita';
                    $cashBox->import = (float) $cobro->import;
                    $cashBox->date = $dayDate->copy('Y-m-d');
                    $cashBox->comment = 'Cobro ' . $rate->name . ' :' . $user->name;

                    $cashBox->typePayment = "INGRESO";
                    $cashBox->type = "INGRESO";
                    $oldBalance = \App\Models\CashBox::orderBy('id', 'desc')->get();

                    $cashBox->balance = (float) $oldBalance[0]->balance + (float) $cobro->import;
                }
                // Actualizamos la cita
                $date->status = 1;
                $date->charged = 1;

                if ($newAssitance->save() && $newCoachClasses->save() && $date->save() && $cobro->save() && $userRate->save() && $cashBox->save()) {
                    return redirect()->back();
                    // echo "METALICO";
                    // die();
                }

                break;

            case 3:

                $date = Dates::find($request->input('idDate'));

                $dayDate = Carbon::createFromFormat('Y-m-d H:i:s', $date->date);
                $user = $date->user;
                $service = $date->service;
                $rate = Rates::find($request->input('id_rate'));

                $classe = Classes::where('type_rate', $service->id)->get();
                $scheduleSelected = Schedules::where('type', $service->id)
                        ->where('hour', $dayDate->copy()->format('G'))
                        ->where('day', $dayDate->copy()->format('N'))
                        ->get();
                /* Apuntamos la clase en la base de asistencias */
                $newAssitance = new Assistance();
                $newAssitance->id_user = $user->id;
                foreach ($scheduleSelected as $key => $value) {
                    $newAssitance->id_schedule = $value->id;
                }
                $newAssitance->date_assistance = $dayDate->copy()->format('Y-m-d H:i:s');

                /* Apuntamos la clase al coach */
                $newCoachClasses = new CoachClasses();
                $newCoachClasses->id_class = $classe[0]->id;
                $newCoachClasses->id_user = $date->id_coach;
                $newCoachClasses->date = $dayDate->copy()->format('Y-m-d H:i:s');
                $date->status = 1;
                $date->charged = 1;

                if ($newCoachClasses->save() && $date->save() && $newAssitance->save()) {
                    return redirect()->back();
                }
                break;
        }
    }

    public function chargeAdvanced(Request $request) {

        $ajax = $request->ajax();
        $oDates = Dates::find($request->idDate);
        if (!$oDates) {
            if ($ajax)
                return "Cita no encontada";
            else
                return redirect()->back()->with(['error' => 'Cita no encontada']);
        }

        $oUser = $oDates->user;
        $service = $oDates->service;
        $oRate = Rates::find($request->input('id_rate'));
        $payType = $request->input('type');

        if (!$oRate) {
            if ($ajax)
                return "Tarifa no encontada";
            else
                return redirect()->back()->with(['error' => 'Tarifa no encontada']);
        }

        if ($request->input('type') == 5) { //invitado
            $oDates->status = 1;
            $oDates->charged = 2;
        } else {

            //Asignamos y cobramos la tarifa que nos envia.
            $oCobro = new \App\Models\Charges();
            $oCobro->id_user = $oUser->id;
            $oCobro->date_payment = date('Y-m-d');
            $oCobro->id_rate = $oRate->id;
            $oCobro->type_payment = ($payType == 4) ? 'banco' : 'cash';
            $oCobro->type = 1;
            $oCobro->import = $request->input('importe');
            $oCobro->discount = 0;
            $oCobro->type_rate = $oRate->type;
            $oCobro->save();

            $userRate = new UserRates();
            $userRate->id_user = $oUser->id;
            $userRate->id_rate = $oRate->id;
            $userRate->rate_year = date('Y', strtotime($oDates->date));
            $userRate->rate_month = date('m', strtotime($oDates->date));
            $userRate->id_charges = $oCobro->id;
            $userRate->active = 0;
            $userRate->save();

            if ($payType == 3) {

                $cashBox = new \App\Models\CashBox();
                $cashBox->concept = 'Cobro ' . $oRate->name . ' :' . $oUser->name;
                $cashBox->import = (float) $oCobro->import;
                $cashBox->date = date('Y-m-d');
                $cashBox->comment = "";
                $cashBox->PayFor = "";
                $cashBox->typePayment = "INGRESO";
                $cashBox->type = "INGRESO";
                $oldBalance = \App\Models\CashBox::orderBy('id', 'desc')->first();
                if ($oldBalance)
                    $cashBox->balance = (float) $oldBalance->balance + (float) $oCobro->import;
                else
                    $cashBox->balance = (float) $oCobro->import;
                $cashBox->save();
            }

            // Actualizamos la cita
            $oDates->status = 1;
            $oDates->charged = 1;
        }

        if ($oDates->save()) {
            if ($ajax)
                return "OK";
            else
                return redirect()->back()->with(['success' => 'Cobro guadado']);
        } else {
            if ($ajax)
                return "No se pudo guardar el cobro";
            else
                return redirect()->back()->with(['error' => 'No se pudo guardar el cobro']);
        }
    }

}
