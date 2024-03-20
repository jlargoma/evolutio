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
use App\Services\CitasService;
use App\Models\Convenios;

class EstheticGController extends Controller {
    //estheticG
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($month = null, $coach = 0, $serv = 0) {
        if (!$month){
          $yearActive = getYearActive();
          $month = $yearActive.date('-m');
        }
        $date = $month . '-01';

        $oCalendar = new \App\Services\CalendarService($date);
        $calendar = $oCalendar->getCalendarWeeks();
        
        $start = $calendar['firstDay'];
        $finish = $calendar['lastDay'];
        $rslt = CitasService::get_calendars($start,$finish,$serv,$coach,'estheticG',$calendar['days']);
        $rslt['calendar'] = $calendar['days'];
        $rslt['month'] = $month;
        $rslt['tColors'] = $this->changeColors($rslt['tColors']);
        /*******************************************/
        return view('estheticGetafe.index', $rslt);
    }
    
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexWeek($week = null, $coach = 0, $serv = 0) {
          
        $yearActive = getYearActive();
        if (!$week) $week = date('W');
        if ($week<10) $week = '0'.intVal($week);

        $time = strtotime($yearActive.'W'.$week);
        $date = date('Y-m-d',$time);

        $oCalendar = new \App\Services\CalendarService($date);
        $calendar = $oCalendar->getCalendarOneWeek();
        
        $start = $calendar['firstDay'];
        $finish = $calendar['lastDay'];
        $rslt = CitasService::get_calendars($start,$finish,$serv,$coach,'estheticG',$calendar['days']);
        $rslt['calendar'] = $calendar['days'];
        $rslt['week'] = $week;
        $rslt['time'] = $time;
        $rslt['tColors'] = $this->changeColors($rslt['tColors']);
        /*******************************************/
        return view('estheticGetafe.indexWeek', $rslt);
    }

    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($date = null, $time = null) {
      $data = CitasService::get_create($date,$time,'estheticG');
      $data['tColors'] = $this->changeColors($data['tColors']);
      $convenios = Convenios::all();
      $data['convenios'] = $convenios;
      return view('estheticGetafe.form', $data);
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
      $data = CitasService::get_edit($id);
      if ($data){
        $data['tColors'] = $this->changeColors($data['tColors']);
        $convenios = Convenios::all();
        $data['convenios'] = $convenios;
        return view('estheticGetafe.form',$data);
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
        $month = null;
        /**************************************************** */
        $servic = Rates::getByTypeRate('estheticG')->pluck('name', 'id');
        /**************************************************** */
        $aLst = [];
        $aUser = $uIDs = [];
        $sql = Dates::where('date_type', 'estheticG')
                ->whereYear('date', '=', $year);
        if ($type && $type != 0)
            $sql->where('id_rate', $type);
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
        $coachs = User::whereCoachs('estheticG')->where('status', 1)->get();
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
        $tColors = $this->changeColors($tColors);

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

        return view('estheticGetafe.listado', $rslt);
    }

    public function informe($uID) {
        $year = getYearActive();
        $user = User::find($uID);
        $servic = TypesRate::where('type', 'estheticG')->pluck('name', 'id');
        $coachs = User::whereCoachs('estheticG')->pluck('name', 'id');
        $lstMonts = lstMonthsSpanish();
        /**************************************************** */
        $aLst = [];
        $oLst = Dates::where('date_type', 'estheticG')
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
        return view('estheticGetafe.informe', [
            'user' => $user,
            'aLst' => $aLst,
            'lstRates' => $lstRates,
            'id' => $uID,
            'year' => $year,
        ]);
    }

    public function uploadFile(Request $request) {

        $directory = storage_path() . "/EsteticaGetafe/" . $request->nombre;

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $directory = storage_path() . "/EsteticaGetafe/" . $request->nombre . "/";
        // echo $storage_path . basename( $_FILES['uploadedfile']['name']);
        $directory = $directory . basename($_FILES['uploadedfile']['name']);
        if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $directory)) {
            return redirect()->action('EstheticGController@index');
        } else {
            return redirect()->action('EstheticGController@index');
        }
    }
    
    public function toggleEquipment(Request $request) {

      $id =  $request->input('id');
      $equipment =  $request->input('equipment');

      $oDate = Dates::find($id);
      if (!$oDate) return 'Cita no encontrada';
      
      $obj = $oDate->getMetaContent($equipment);
      if ($obj && $obj == 1) $obj = 0;
      else $obj = 1;
      
      if($obj == 1){
        $inUse = Dates::Join('appointment_meta', function ($join) {
            $join->on('appointment.id', '=', 'appointment_meta.appoin_id');
            }) ->where('date', $oDate->date)
            ->where('appointment.id', '!=', $oDate->id)
            ->where('meta_value', 1)->where('meta_key', $equipment)->count();
        if($inUse>0){
            return 'Equipamiento en uso';
        }

      }
      $oDate->setMetaContent($equipment,$obj);
      
      return 'OK';
      
    }
   
    
    private function changeColors($tColors){
      /*$tColors[1716] = '#9b59ff';
      $tColors[1971] = '#295d9b';
      $tColors[2347] = '#ffa116';
      $tColors[2504] = '#10cfbd';*/
      
      return User::changeColors($tColors);
    }

}
