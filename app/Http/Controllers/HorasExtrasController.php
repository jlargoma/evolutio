<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests;
use App\Models\CoachLiquidation;
use App\Models\ExtraHoursRequestItems;
use App\Models\ExtraHoursRequests;
use App\Models\CoachRates;
use App\Models\ExtraHoursRequestReviews;
use \Carbon\Carbon;
use DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HorasExtrasController extends Controller {


  public function getLink($id) {

    try {
      
      if (!$id) {
        throw new \Exception('El id es requerido', 400);
      }

      $user = User::find($id);
      
      if($user) {

        $horasExtrasLink = ExtraHoursRequests::where('user_id', $id)->first();
        
        if($horasExtrasLink){

          return response()->json(['status' => 'OK', 'details' => [
            'id'    => $horasExtrasLink->id,
            'token' => $horasExtrasLink->token,
            'user'  => [
              "name"  => $user->name
            ]
          ]], 200);

        }else{
          
          $newObj = new ExtraHoursRequests();
          $newObj->token = Str::uuid()->toString();
          $newObj->user_id = $id;
          $newObj->creator_id = Auth::user()->id;
          
          if($newObj->save()){
            
            return response()->json(['status' => 'OK', 'details' => [
              'id'    => $newObj->id,
              'token' => $newObj->token,
              'user'  => [
                "name"  => $user->name
              ]
            ]], 200);

          } else {

            throw new \Exception('No se pudo guardar el registro', 500);

          }
        }

      } else {
        throw new \Exception('Usuario inexistente', 400);
      }
      
    } catch (\Exception $e) {

      return response()->json(['status' => 'error', 'details' => $e->getMessage()], $e->getCode());

    }
  }


  public function showLink($id, $token, $period = null) {

    try {
      if (!$id) {
        throw new \Exception('El id es requerido', 400);
      }

      if (!$token) {
        throw new \Exception('El token es requerido', 400);
      }

      if(!is_null($period) && !strtotime($period)) {
        throw new \Exception('Período incorrecto', 400);
      }

      $month = date('m');
      $year  = date('Y');
      $lstMonths = lstMonthsSpanish();
      unset($lstMonths[0]);

      if(!is_null($period)){
        $periodParts = explode('-', $period);

        if(
          count($periodParts) == 2 && 
          isset($lstMonths[$periodParts[1]]) && 
          strtotime($periodParts[0]) !== false
        ){
          $month = $periodParts[1];
          $year  = $periodParts[0];
        }
      }
      

      $extraHoursRequest = ExtraHoursRequests::find($id);

      if($extraHoursRequest) {

        if ($extraHoursRequest->token == $token) {

          $user = User::find($extraHoursRequest->user_id);

          if($user) {

            $rates = CoachRates::where('id_user', $user->id)->first();

            if($rates) {

              $user->rates = $rates;

            } else {

              throw new \Exception('No hay precios asociados', 404);

            }
            
            switch($user->role) {
              case 'admin':
                $user->rol = 'Administrador';
                break;
              case 'teach':
                $user->rol = 'Entrenador';
                break;
              case 'fisio':
                $user->rol = 'Fisioterapia';
                break;
              case 'fisioG':
                $user->rol = 'Fisioterapia Getafe';
                break;
              case 'nutri':
                $user->rol = 'Nutricionista';
                break;
              case 'nutriG':
                $user->rol = 'Nutricionista Getafe';
                break;
              case 'teach_nutri':
                $user->rol = 'Entrenador / Nutricionista';
                break;
              case 'teach_fisio':
                $user->rol = 'Entrenador / Fisioterapia';
                break;
              case 'empl':
                $user->rol = 'Empleado';
                break;
              case 'esthetic':
                $user->rol = 'Estética';
                break;
              case 'estheticG':
                $user->rol = 'Estética Getafe';
                break;
            }

            $requestItems = ExtraHoursRequestItems::where('request_id', $extraHoursRequest->id)
                                                  ->where('year', $year)->where('month', $month)->get();

            $total = $rates->salary;

            foreach( $requestItems as $item ){
              $total += $item->amount / 100;
            }

            return view('horasExtras.form', [
              'items'     => $requestItems,
              'user'      => $user,
              'lstMonths' => $lstMonths,
              'month'     => $month,
              'year'      => $year,
              'total'     => $total,
              'requestId' => $id,
              'token'    => $token
            ]); 

          } else {

            throw new \Exception('Link incorrecto', 400);

          }

        } else {

          throw new \Exception('Token incorrecto', 400);

        }

      } else {
        throw new \Exception('Link incorrecto', 400);
      }
      
    } catch (\Exception $e) {

      return view('errors.error', [
        'code'    => $e->getCode(),
        'message' => $e->getMessage()
      ]); 

    }
  }

  public function carga(Request $request) {

    try {

      if(
        !$request->month || 
        !$request->year || 
        strtotime($request->year . '-' . $request->month) == false ||
        strtotime($request->year . '-' . $request->month) > strtotime(date('Y-m')) //no deberian poder cargar horas extras a futuro
      ) {
        throw new \Exception('Período incorrecto');
      }

      $messages = [
        'requestId'   => 'El id es requerido.',
        'token'       => 'El token es requerido.',
        'amount'      => 'Debe ingresar montos válidos.',
        'description' => 'Debe ingresar descripciones válidas.',
        'amount.*.required'      => 'Debe ingresar montos válidos.',
        'description.*.required' => 'Debe ingresar descripciones válidas.',
        'amount.*.numeric'      => 'Debe ingresar montos válidos.',
        'description.*.string' => 'Debe ingresar descripciones válidas.',
        'amount.*.between'      => 'Debe ingresar montos válidos.'
      ];

      $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
        'token'          => 'required',
        'requestId'      => 'required',
        'amount'         => 'required|array|min:1',
        'amount.*'       => 'required|numeric|between:0,3000',
        'description'    => 'required|array|min:1',
        'description.*'  => 'required|string',
      ], $messages);

      if ($validator->fails()) {
        return redirect()->back()->withErrors($validator);
      }

      if(count($request->amount) != count($request->description)){
        throw new \Exception('Datos incorrectos.');
      }

      $requestItemsCount = ExtraHoursRequestItems::where('request_id', $request->requestId)
                      ->where('year', $request->year)->where('month', $request->month)->count();

      if($requestItemsCount > 0){
        throw new \Exception('Ya se cargaron los datos para el período.');
      }

      $extraHoursRequest = ExtraHoursRequests::find($request->requestId);

      if($extraHoursRequest) {

        if ($extraHoursRequest->token == $request->token) {

          $user = User::find($extraHoursRequest->user_id);

          if($user) {

            DB::beginTransaction();

            try {

              foreach($request->amount as $i => $v){
                $requestItem              = new ExtraHoursRequestItems();
                $requestItem->request_id  = $request->requestId;
                $requestItem->description = $request->description[$i];
                $requestItem->amount      = $v * 100;
                $requestItem->month       = $request->month;
                $requestItem->year        = $request->year;
                $requestItem->save();
              }

              DB::commit();

              return redirect()->back();

            } catch (\Exception $e) {

              DB::rollback();
              throw $e;

            }
            

          } else {
            throw new \Exception('Link incorrecto');
          }

        } else {
          throw new \Exception('Token incorrecto');
        }

      } else {
        throw new \Exception('Link incorrecto');
      }
      
    } catch (\Exception $e) {
      return redirect()->back()->withErrors([
        $e->getMessage()
      ]);
    }
  }

  public function list(Request $request) {

    try {

      $year = getYearActive();
      $month = date('m');

      if($request->month){
        $month = $request->month;
      }

      $qryBuilder = ExtraHoursRequestItems::leftjoin('extra_hours_requests','extra_hours_requests.id','extra_hours_request_items.request_id')
                    ->leftjoin('users', 'users.id', 'extra_hours_requests.user_id')
                    ->select('extra_hours_request_items.*', 'users.id as user_id', 'users.name', 'users.role')
                    ->where('extra_hours_request_items.year', $year)
                    ->where('extra_hours_request_items.month', $month);

      $items = $qryBuilder->get();

      $coachs = User::whereCoachs()->leftjoin('coach_rates', 'users.id', 'coach_rates.id_user')
      ->select('users.*', 'coach_rates.salary')
      ->where('status', 1)->get();
      
      $roles = getRoles();
      
      foreach($roles as $i => $val){
        $roles[$i] = [
          'name'  => $val
        ];
      }

      foreach($items as $k => $data){

        if(
          isset($roles[$data->role]) &&
          isset($roles[$data->role]['users']) &&
          isset($roles[$data->role]['users'][$data->user_id]) 
        ){

          $roles[$data->role]['users'][$data->user_id]['total'] += $data->amount;
          $roles[$data->role]['users'][$data->user_id]['request_items'][] = [
              'id'          => $data->id,
              'request_id'  => $data->request_id,
              'description' => $data->description,
              'amount'      => $data->amount
          ];

        }else{

          $roles[$data->role]['users'][$data->user_id] = [
            'id'          => $data->user_id,
            'name'        => $data->name,
            'role'        => $data->role,
            'request_id'  => $data->request_id,
            'total'       => $data->amount,
            'request_items' => [[
              'id'          => $data->id,
              'request_id'  => $data->request_id,
              'description' => $data->description,
              'amount'      => $data->amount
            ]]
          ];
          
        }
        
        if(isset($roles[$data->role]['total'])){

          $roles[$data->role]['total'] += $data->amount;

        }else{

          $roles[$data->role]['total'] = $data->amount;

        }
      }
      
      foreach($coachs as $coach) {

        if(!isset($roles[$coach->role]['users'])){
          $roles[$coach->role]['users'] = [];
        }

        if(!isset($roles[$coach->role]['users'][$coach->id])){
          $roles[$coach->role]['users'][$coach->id] = [
              'id'          => $coach->id,
              'name'        => $coach->name,
              'role'        => $coach->role,
              'salary'      => $coach->salary * 100,
              'total'       => $coach->salary * 100,
              'request_items' => []
          ];
        } else {
          $roles[$coach->role]['users'][$coach->id]['salary'] = $coach->salary * 100;
          $roles[$coach->role]['users'][$coach->id]['total'] += $coach->salary * 100;
        }

        if(!isset($roles[$coach->role]['total'])){
          $roles[$coach->role]['total'] = $coach->salary * 100;
        } else {
          $roles[$coach->role]['total'] += $coach->salary * 100;
        }
       
      }

      $months = lstMonths();
      unset($months[0]);

      foreach($roles as $index => $rol){
        if(!isset($rol['users'])){
          unset($roles[$index]);
        }
      }

      $review = ExtraHoursRequestReviews::where('month', $month)->where('year', $year)->first();

      $allowCRUD = true;
      if($review){
        $allowCRUD = false;
      }
      
      return view('horasExtras.index',[
        'roles'       => $roles,
        'year'        => $year,
        'lstMonths'   => $months,
        'month'       => $month,
        'allowCRUD'   => $allowCRUD
      ]);

    } catch (\Exception $e) {

      return view('errors.error', [
        'code'    => $e->getCode(),
        'message' => $e->getMessage()
      ]); 

    }
  }

  public function deleteItem(Request $request) {

    try {
      
      if (!$request->id) {
        throw new \Exception('El id es requerido', 400);
      }

      $item = ExtraHoursRequestItems::find($request->id);
      
      if($item) {

        $review = ExtraHoursRequestReviews::where('month', $item->month)->where('year', $item->year)->first();

        if($review){
          throw new \Exception('No se puede eliminar el registro, ya se ha enviado a salarios.', 400);
        }

        /**
         * TODO: CHECK STATUS
         */
        $item->delete();

        return response()->json(['status' => 'OK', 'details' => [
          'message'    => "El registro ha sido eliminado correctamente."
        ]], 200);

      } else {
        throw new \Exception('Registro inexistente', 404);
      }
      
    } catch (\Exception $e) {

      return response()->json(['status' => 'error', 'details' => $e->getMessage()], $e->getCode());

    }
  }

  public function editItem(Request $request) {

    try {
      
      $messages = [
        'id.required'         => 'El id es requerido.',
        'description.string'  => 'Debe ingresar una descripcion válida.',
        'amount.numeric'      => 'Debe ingresar un monto válido.',
        'amount.between'      => 'Debe ingresar un monto válido.'
      ];

      $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
        'id'             => 'required',
        'amount'         => 'numeric|between:0,3000',
        'description'    => 'string'
      ], $messages);

      if ($validator->fails()) {
        throw new \Exception('Datos inválidos.', 400);
      }

      $item = ExtraHoursRequestItems::find($request->id);
      
      if($item) {

        $review = ExtraHoursRequestReviews::where('month', $item->month)->where('year', $item->year)->first();

        if($review){
          throw new \Exception('No se puede eliminar el registro, ya se ha enviado a salarios.', 400);
        }
        
        /**
         * TODO: CHECK STATUS
         */
        if($request->description){
          $item->description = $request->description;
        }

        if($request->amount){
          $item->amount = $request->amount * 100;
        }

        $item->save();

        return response()->json(['status' => 'OK', 'details' => [
          'message'    => "El registro ha sido actualizado correctamente."
        ]], 200);

      } else {
        throw new \Exception('Registro inexistente', 404);
      }
      
    } catch (\Exception $e) {

      return response()->json(['status' => 'error', 'details' => $e->getMessage()], $e->getCode());

    }
  }

  public function addItem(Request $request) {

    try {
      
      $messages = [
        'month.required'      => 'El mes es requerido.',
        'user.required'       => 'El usuario es requerido.',
        'description.string'  => 'Debe ingresar una descripción válida.',
        'amount.numeric'      => 'Debe ingresar un monto válido.',
        'amount.between'      => 'Debe ingresar un monto válido.'
      ];

      $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
        'month'          => 'required',
        'user'           => 'required',
        'amount'         => 'numeric|between:0,3000',
        'description'    => 'string'
      ], $messages);

      if ($validator->fails()) {
        throw new \Exception('Datos inválidos.', 400);
      }

      $year = getYearActive();

      $review = ExtraHoursRequestReviews::where('month', $request->month)->where('year', $year)->first();

      if($review){
        throw new \Exception('No se puede agregar el registro, ya se ha enviado a salarios.', 400);
      }


      $user = User::find($request->user);
      
      if($user) {

        $horasExtrasLink = ExtraHoursRequests::where('user_id', $request->user)->first();
        
        if(!$horasExtrasLink){
          
          $horasExtrasLink = new ExtraHoursRequests();
          $horasExtrasLink->token = Str::uuid()->toString();
          $horasExtrasLink->user_id = $request->user;
          $horasExtrasLink->creator_id = Auth::user()->id;
          
          if(!$horasExtrasLink->save()){

            throw new \Exception('No se pudo guardar el registro', 500);

          }
        }

      } else {
        throw new \Exception('Usuario inexistente', 400);
      }
      
      $item = new ExtraHoursRequestItems();
      $item->request_id   = $horasExtrasLink->id;
      $item->description  = $request->description;
      $item->amount       = $request->amount * 100;
      $item->month        = $request->month;
      $item->year         = $year;

      if(!$item->save()){

        throw new \Exception('No se pudo guardar el registro', 500);

      }

      return response()->json(['status' => 'OK', 'details' => [
        'id'    => $item->id
      ]], 200);

      
    } catch (\Exception $e) {

      return response()->json(['status' => 'error', 'details' => $e->getMessage()], $e->getCode());

    }
  }

  public function review(Request $request) {

    try {

      $year = getYearActive();

      $messages = [
        'month.required'  => 'El mes es requerido.',
        'month.numeric'   => 'El mes es inválido.',
        'month.between'   => 'El mes es inválido.',
      ];

      $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
        'month'          => 'required|numeric|between:1,12',
      ], $messages);

      if ($validator->fails()) {
        return redirect()->back()->withErrors($validator);
      }

      $review = ExtraHoursRequestReviews::where('month', $request->month)->where('year', $year)->first();

      if($review){
        throw new \Exception('No se puede volver a enviar, ya se ha enviado a sueldos y salarios.', 400);
      }

      try {

        $qryBuilder = ExtraHoursRequestItems::leftjoin('extra_hours_requests','extra_hours_requests.id','extra_hours_request_items.request_id')
        ->leftjoin('users', 'users.id', 'extra_hours_requests.user_id')
        ->leftjoin('coach_rates', 'users.id', 'coach_rates.id_user')
        ->select('extra_hours_request_items.*', 'users.id as user_id', 'coach_rates.salary')
        ->where('extra_hours_request_items.year', $year)
        ->where('extra_hours_request_items.month', $request->month);

        $items = $qryBuilder->get();

        $users = [];
        foreach($items as $item){
          if(isset($users[$item->user_id])){
            $users[$item->user_id]['amount'] += $item->amount / 100;
          } else {
            $users[$item->user_id] = [
              'salary' => $item->salary,
              'amount'  => $item->amount / 100
            ];
          }
        }

        $coachs = User::whereCoachs()->leftjoin('coach_rates', 'users.id', 'coach_rates.id_user')
        ->select('users.*', 'coach_rates.salary')
        ->where('status', 1)->get();

        foreach($coachs as $coach) {
          if(!isset($users[$coach->id])) {
            $users[$coach->id] = [
              'salary' => $coach->salary,
              'amount'  => null
            ];
          }
        }

        DB::beginTransaction();
        $date = date('Y-m-d 00:00:00', strtotime($year.'-'.$request->month));
        
        foreach($users as $id => $user) {
          $oLiq = CoachLiquidation::where('id_coach',$id)
          ->where('date_liquidation',$date)->first();

          if (!$oLiq){
            $oLiq = new CoachLiquidation();
            $oLiq->id_coach = $id;
            $oLiq->date_liquidation = $date;
          }

          if($user['salary']){
            $oLiq->salary = intval($user['salary']);
          }

          if($user['amount']){
            $oLiq->commision = intval($user['amount']);
          }
          
          if (!$oLiq->save()){
            throw new \Exception('Hubo un error al guardar los datos.');
          }
        }

        $review = new ExtraHoursRequestReviews();
        $review->month      = $request->month;
        $review->year       = $year;
        $review->creator_id = Auth::user()->id;

        if (!$review->save()){
          throw new \Exception('Hubo un error al guardar los datos.');
        }

        DB::commit();

      } catch (\Exception $e) {

        DB::rollback();

        throw new \Exception('No se pudo procesar la solicitud.');
      }
      
      return redirect()->back()->with('message', 'La información ha sido procesada exitosamente.');
      
    } catch (\Exception $e) {
      return redirect()->back()->withErrors([
        $e->getMessage()
      ]);
    }
  }
}
