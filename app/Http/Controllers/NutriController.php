<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use Carbon\Carbon;
use App\Models\Dates;
use App\Models\User;
use App\Models\Rates;
use App\Models\CoachTimes;
use App\Models\TypesRate;

class NutriController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($month = null, $coach = 0, $type = 0) {
        if (!$month)
            $month = date('Y-m');
        $date = $month . '-01';
        $calendar = \App\Services\CalendarService::getCalendarWeeks($date);
        $start = $calendar['firstDay'];
        $finish = $calendar['lastDay'];
        $times = [];

        /**************************************************** */
        $servic = Rates::getByTypeRate('nutri')->pluck('name', 'id');
        /**************************************************** */
        $aLst = [];
        $sql = Dates::where('date_type', 'nutri')
                ->where('date', '>=', date('Y-m-d', $start))
                ->where('date', '<=', date('Y-m-d', $finish));
        if ($type && $type != 0)
            $sql->where('id_rate', $type);
        if ($coach && $coach > 0){
            $sql->where('id_coach', $coach);
            
            /*******************************************/
            /***    BEGIN HORARIOS                   ***/
            
            $coachTimes = CoachTimes::where('id_coach',$coach)->first(); 
            if ($coachTimes){
                $times = json_decode($coachTimes->times,true);
                if (!is_array($times)) $times = [];
            }
            /***    END: HORARIOS                    ***/
            /*******************************************/
        }

        $oLst = $sql->with('user')->get();

        if ($oLst) {
            foreach ($oLst as $item) {
                $time = strtotime($item->date);
                $hour = date('G', $time);
                $date = date('Y-m-d', $time);
                $time = strtotime($date);

                if (!isset($aLst[$time]))
                    $aLst[$time] = [];
                if (!isset($aLst[$time][$hour]))
                    $aLst[$time][$hour] = [];

                $aLst[$time][$hour][] = [
                    'id' => $item->id,
                    'charged' => $item->charged,
                    'type' => $item->id_rate,
                    'coach' => $item->id_coach,
                    'name' => ($item->user) ? $item->user->name : ' -- ',
                ];
            }
        }
        /*         * *************************************************** */
        $lstMonts = lstMonthsSpanish();
        $aMonths = [];
        $year = getYearActive();
        foreach ($lstMonts as $k => $v) {
            if ($k > 0)
                $aMonths[$year . '-' . str_pad($k, 2, "0", STR_PAD_LEFT)] = $v;
        }
        /*         * *************************************************** */
        $coachs = User::where('role', 'nutri')->where('status', 1)->get();
        $tColors = [];
        if ($coachs) {
            $auxColors = colors();
            $i = 0;
            foreach ($coachs as $item) {
                if (!isset($auxColors[$i]))
                    $i = 0;
                $tColors[$item->id] = $auxColors[$i];
                $i++;
            }
        }



        $rslt = [
            'type' => $type,
            'calendar' => $calendar['days'],
            'aLst' => $aLst,
            'aMonths' => $aMonths,
            'year' => $year,
            'month' => $month,
            'type' => $type,
            'types' => $servic,
            'tColors' => $tColors,
            'coachs' => $coachs,
            'coach' => $coach,
            'times' => $times,
        ];

        return view('nutricion.index', $rslt);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($date = null, $time = null) {

        if (!$date)
            $date = time();

        return view('nutricion.form', [
            'date' => date('d-m-Y', $date),
            'time' => $time,
            'id_serv' => -1,
            'id_user' => -1,
            'id_coach' => -1,
            'email' => '',
            'id' => -1,
            'charged' => 0,
            'price' => 0,
            'services' => Rates::getByTypeRate('nutri'),
            'users' => User::where('role', 'user')->where('status', 1)->orderBy('name', 'ASC')->get(),
            'coachs' => User::where('role', 'nutri')->where('status', 1)->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $oDate = Dates::find($id);
        if ($oDate) {
            $date = explode(' ', $oDate->date);
            $user = $oDate->user()->first();
            $oServicios = Rates::getByTypeRate('nutri');
            $price = 0;
            if ($oServicios){
                foreach ($oServicios as $s)
                    if($s->id == $oDate->id_rate)
                        $price = $s->price;
            }
            return view('nutricion.form', [
                'date' => date('d-m-Y', strtotime($date[0])),
                'time' => intval($date[1]),
                'id_serv' => $oDate->id_rate,
                'id_user' => $oDate->id_user,
                'id_coach' => $oDate->id_coach,
                'email' => $user->email,
                'price' => $price,
                'id' => $oDate->id,
                'charged' => $oDate->charged,
                'services' => $oServicios,
                'users' => User::where('role', 'user')->where('status', 1)->orderBy('name', 'ASC')->get(),
                'coachs' => User::where('role', 'nutri')->where('status', 1)->get()
            ]);
        } else {
            return $this->create();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

    public function listado($coach = 0, $type = 0) {

        $year = getYearActive();
        /**************************************************** */
        $servic = TypesRate::where('type', 'nutri')->pluck('name', 'id');
        /**************************************************** */
        $aLst = [];
        $aUser = $uIDs = [];
        $sql = Dates::where('date_type', 'nutri')
                ->whereYear('date', '=', $year);
        if ($type && $type != 0)
            $sql->where('id_type_rate', $type);
        if ($coach && $coach > 0)
            $sql->where('id_coach', $coach);

        $oLst = $sql->orderBy('date')->get();

        if ($oLst) {
            foreach ($oLst as $item) {
                $time = strtotime($item->date);
                $month = date('n', $time);
                $date = date('d / H', $time);
                $uID = $item->id_user;
                $uIDs[] = $uID;
                if (!isset($aLst[$uID]))
                    $aLst[$uID] = [];
                if (!isset($aLst[$uID][$month]))
                    $aLst[$uID][$month] = [];

                $aLst[$uID][$month][] = $date . ':00';
            }
        }
        /**************************************************** */
        $oUsers = User::whereIn('id', $uIDs)->get();
        /**************************************************** */
        $lstMonts = lstMonthsSpanish();

        /**************************************************** */
        $coachs = User::where('role', 'nutri')->where('status', 1)->get();
        $tColors = [];
        if ($coachs) {
            $auxColors = colors();
            $i = 0;
            foreach ($coachs as $item) {
                if (!isset($auxColors[$i]))
                    $i = 0;
                $tColors[$item->id] = $auxColors[$i];
                $i++;
            }
        }



        $rslt = [
            'type' => $type,
            'aLst' => $aLst,
            'aMonths' => $lstMonts,
            'year' => $year,
            'month' => $month,
            'type' => $type,
            'types' => $servic,
            'tColors' => $tColors,
            'coachs' => $coachs,
            'coach' => $coach,
            'oUsers' => $oUsers,
        ];

        return view('nutricion.listado', $rslt);
    }

    public function informeNutricion($uID) {
        $year = getYearActive();
        $user = User::find($uID);
        $servic = TypesRate::where('type', 'nutri')->pluck('name', 'id');
        $coachs = User::where('role', 'nutri')->pluck('name', 'id');
        $lstMonts = lstMonthsSpanish();
        /**************************************************** */
        $aLst = [];
        $oLst = Dates::where('date_type', 'nutri')
                ->where('id_user', $uID)
                ->whereYear('date', '=', $year)
                ->orderBy('date')->get();

        if ($oLst) {
            foreach ($oLst as $i) {
                $time = strtotime($i->date);
                $month = date('n', $time);
                $date = date('d', $time);
                $hour = date('H', $time);
                $uID = $i->id_user;
                $tm  = isset($lstMonts[$month]) ? $lstMonts[$month] : '';
                $aLst[] = [
                    'id'   => $i->id,
                    'hour' => $hour . ':00',
                    'date' => $date .' '.$tm,
                    'rate' => isset($servic[$i->id_type_rate]) ? $servic[$i->id_type_rate] : '',
                    'coach'=> isset($coachs[$i->id_coach]) ? $coachs[$i->id_coach] : '',
                    'charged' => $i->charged,
                ];
                
            }
        }
        
        $lstRates = [];
        $userRates = UserRates::where('id_user', $user->id)
                        ->whereYear('created_at', "=", $year)->get();
        if($userRates){
            foreach ($userRates as $i){
                $lstRates[] = $i->rate->name;
            }
            $lstRates = array_unique($lstRates);
            sort($lstRates);
        }
        /**************************************************** */
        return view('nutricion.informe', [
            'user' => $user,
            'aLst' => $aLst,
            'lstRates' => $lstRates,
            'id' => $uID,
            'year' => $year,
        ]);
    }

    public function uploadFile(Request $request) {

        $directory = storage_path() . "/Nutricion/" . $request->nombre;

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $directory = storage_path() . "/Nutricion/" . $request->nombre . "/";
        // echo $storage_path . basename( $_FILES['uploadedfile']['name']);
        $directory = $directory . basename($_FILES['uploadedfile']['name']);
        if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $directory)) {
            return redirect()->action('NutriController@index');
        } else {
            return redirect()->action('NutriController@index');
        }
    }

}
