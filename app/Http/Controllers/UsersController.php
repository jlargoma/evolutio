<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Response;
use App\Http\Requests;
use \Carbon\Carbon;
use Mail;
use URL;
use Cache;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use App\Models\UserRates;
use \App\Traits\EntrenadoresTraits;
use \App\Traits\ClientesTraits;
use \App\Traits\ValoracionTraits;
use \App\Traits\ClientesFilesTraits;

class UsersController extends Controller {

  use EntrenadoresTraits,ValoracionTraits,
      ClientesTraits,ClientesFilesTraits;

  public function index() {

    return view('/admin/usuarios/index', [
        'users' => User::whereIn('role', [
            'admin',
            'administrativo'
        ])->get(),
        'date' => Carbon::now(),
    ]);
  }

  public function newCustomer(Request $request) {
    return view('admin.usuarios.clientes.forms.new');
  }

  public function saveCustomer(Request $request) {
    $issetUser = User::where('email', $request->input('email'))->get();
    if (count($issetUser) > 0) {
      return redirect('/admin/usuarios/nuevo')->withErrors(["email duplicado"])->withInput();
    } else {
      $newUser = new User();
      $newUser->name = $request->input('name');
      $newUser->email = $request->input('email');
      $newUser->password = str_random(60); //bcrypt($request->input('password'));
      $newUser->remember_token = str_random(60);
      $newUser->role = 'user';
      $newUser->telefono = $request->input('telefono');
      $newUser->dni = $request->input('dni');
      $newUser->address = $request->input('address');
      if ($newUser->save()) {
        $email = $newUser->email;
        $sended = Mail::send('emails._create_user_email', ['user' => $newUser], function ($message) use ($email) {
                  $message->subject('Inscripción en Evolutio');
                  $message->from('info@evolutio.fit', 'Inscripción Evolutio');
                  $message->to($email);
                });
        return redirect('/admin/usuarios/informe/' . $newUser->id);
      }
    }
    return redirect('/admin/usuarios/nuevo')->withErrors(["Error al crear el usuario"])->withInput();
  }

  public function nueva($role = null) {
    $url = URL::previous();
    if (!$role) {
      if (preg_match('/usuarios/i', $url)) {
        $role = 'admin';
      } elseif (preg_match('/clientes/i', $url)) {
        $role = 'user';
      } elseif (preg_match('/entrenadores/i', $url)) {
        $role = 'teach';
      } elseif (preg_match('/nutricion/i', $url)) {
        $role = 'nutri';
      }
    }

    $aCoachs = [];
    $uCoach = null;
    if ($role == 'user') {
      $aCoachs = User::whereCoachs('teach')->orderBy('name')->pluck('name', 'id')->toArray();
    }
    return view('/admin/usuarios/nueva', [
        'rates' => \App\Models\Rates::orderBy('status', 'desc')->orderBy('name', 'asc')->get(),
        'role' => $role,
        'aCoachs' => $aCoachs,
        'uCoach' => $uCoach,
    ]);
  }

  public function create(Request $request) {
    $issetUser = User::where('email', $request->input('email'))->get();
    if (count($issetUser) > 0) {
      return "email duplicado";
    } else {
      $newUser = new User();
      $newUser->name = $request->input('name');
      $newUser->email = $request->input('email');
      $newUser->password = bcrypt($request->input('password'));
      $newUser->remember_token = str_random(60);
      $newUser->role = $request->input('role', 'user');
      $newUser->telefono = $request->input('telefono');
      $newUser->password = bcrypt($request->input('password'));

      if ($newUser->save()) {
        /*         * ************************************ */
        $rateID = $request->input('id_rate');
        if ($rateID > 0) {
          $oRate = \App\Models\Rates::find($request->input('id_rate'));
          if ($oRate) {
            $rateUser = new UserRates();
            $rateUser->id_user = $newUser->id;
            $rateUser->id_rate = $oRate->id;
            $rateUser->rate_year = date('Y');
            $rateUser->rate_month = date('m');
            $rateUser->save();
          }
        }
        /*         * ************************************ */
        if ($newUser->role == 'user') {
          $uCoach = new \App\Models\CoachUsers();
          $uCoach->id_user = $newUser->id;
          $uCoach->id_coach = $request->input('u_coach', 0);
          $uCoach->save();
        }
        /*         * ************************************ */

        $email = $newUser->email;
        $role = $newUser->role;
        switch ($role) {
          case 'admin';
          case 'administrativo';
            return redirect('/admin/usuarios');
            break;
          case 'teach';
          case 'nutri';
          case 'fisio';
          case 'teach_nutri';
          case 'teach_fisio';
            return redirect('/admin/entrenadores');
            break;
          default :
            $sended = Mail::send('emails._create_user_email', ['user' => $newUser], function ($message) use ($email) {
                      $message->subject('Registro de Usuario');
                      $message->subject('Inscripción en Evolutio');
                      $message->from('info@evolutio.fit', 'Inscripción Evolutio');
                      $message->to($email);
                    });
            if ($request->ajax())
              return 'OK';
            return redirect('/admin/clientes');
            break;
        }
      }
    }
    if ($request->ajax())
      return 'error';
  }

  public function actualizar($id) {
    return view('/admin/usuarios/update', [
        'rates' => \App\Models\Rates::all(),
        'user' => User::find($id)
    ]);
  }

  public function getMail($id) {
    $oUser = User::find($id);
    if ($oUser) {
      return [$oUser->email, $oUser->telefono];
    }
    return ['', ''];
//        return ($oUser) ? $oUser->email : '';
  }
  public function getRates($uID) {
    $aLst = UserRates::where('id_user',$uID)
            ->where('active',1)->pluck('id_rate')->toArray();
    \App\Services\ValoracionService::RateLstID($uID,$aLst);
    return response()->json(array_unique($aLst));
  }

  public function getList() {
    return \App\User::where('role', 'user')
                    ->where('status', 1)
                    ->orderBy('name', 'ASC')->pluck('name', 'id')->toArray();
  }

  public function delete($id) {
    User::find($id)->delete();
    return redirect('/admin/usuarios');
  }
/**
 * /clientes/update
 * @param Request $request
 * @return type
 */
  public function updateCli(Request $request) {
    $rates = $request->input('id_rates');

    $id = $request->input('id');
    $userToUpdate = User::find($id);
    if (!$userToUpdate) return 'Usuario no encontrado';

    $userToUpdate->name = $request->input('name');
    $userToUpdate->email = $request->input('email');
    $userToUpdate->role = $request->input('role', 'user');
    $userToUpdate->dni = $request->input('dni');
    $userToUpdate->address = $request->input('address');
    $userToUpdate->status = $request->input('status');
    $userToUpdate->visa = $request->input('visa');
      
    if ($request->input('password'))
      $userToUpdate->password = bcrypt($request->input('password'));

    $userToUpdate->telefono = $request->input('telefono');
    $userToUpdate->save();
    if ($request->has('fidelity')){
      $oldFidelity = $userToUpdate->getMetaContent('has_fidelity');
      $userToUpdate->setMetaContent('plan',$request->input('fidelity'));
      // agrega un bono de Fisio y otro de Nutry
      if (!$oldFidelity && $request->input('fidelity') == 'fidelity'){
        $userToUpdate->setMetaContent('has_fidelity',1);
        $sBono = new \App\Services\BonoService();
        $rateBonos = \App\Models\TypesRate::whereIn('type',['fisio','nutri'])->pluck('id');
        foreach ($rateBonos as $rTypeID){
          $sBono->fidelityADD($userToUpdate->id, $rTypeID);
        }
      }
    }

    /* ------------------------- */
    $file = null;
    if ($request->hasfile('f_photo')) {
      $validated = $request->validate(
              ['f_photo' => 'required|file|mimes:jpeg,webp,png,jpg|max:204800'],
              ['f_photo.mimes' => 'La foto debe ser png o jpg',
                  'f_photo.max' => 'La foto debe ser menor de 200 Mb',
                
      ]);

      $file = $request->file('f_photo');
    }
    if ($request->hasfile('f_photoMobil')) {
      $validated = $request->validate(
              ['f_photoMobil' => 'required|file|mimes:jpeg,webp,png,jpg|max:204800'],
              ['f_photoMobil.mimes' => 'La foto debe ser png o jpg',
                  'f_photoMobil.max' => 'La foto debe ser menor de 200 Mb',
                
      ]);

      $file = $request->file('f_photoMobil');
    }
    if ($file){
      $filename = 'photo_' . $userToUpdate->id . '.' . $file->extension();
      \Storage::disk('local')->put('photos/' . $filename, \File::get($file));
      $path = storage_path('app/photos/' . $filename);
      $userToUpdate->setMetaContent('photo',$filename);
    }
    /* ------------------------- */
    
    return redirect()->back()->with('success', 'Cliente actualizado');
  }
  public function update(Request $request) {
    $rates = $request->input('id_rates');

    $id = $request->input('id');
    $userToUpdate = User::find($id);
    $userToUpdate->name = $request->input('name');
    $userToUpdate->email = $request->input('email');
    $userToUpdate->role = $request->input('role', 'user');
    $userToUpdate->dni = $request->input('dni');
    $userToUpdate->address = $request->input('address');
      
    if ($request->input('password'))
      $userToUpdate->password = bcrypt($request->input('password'));

    $userToUpdate->telefono = $request->input('telefono');

    if ($userToUpdate->role == 'user') {
      if (!empty($rates)) {
        $oldRates = UserRates::where('id_user', $userToUpdate->id)
                ->whereMonth('created_at', '=', date('m'))
                ->whereYear('created_at', '=', date('Y'))
                ->get();

        if (count($oldRates) > 0) {
          foreach ($oldRates as $key => $oldRate) {
            $oldRate->delete();
          }
        }

        $rateUser = new UserRates();
        $rateUser->id_user = $userToUpdate->id;
        $rateUser->id_rate = $rates;
        $rateUser->save();
      }
      /*       * ************************************ */
      $uCoach = \App\Models\CoachUsers::where('id_user', $id)->first();
      if (!$uCoach) {
        $uCoach = new \App\Models\CoachUsers();
        $uCoach->id_user = $id;
      }
      $uCoach->id_coach = intval($request->input('u_coach', 0));
      $uCoach->save();
      /*       * ************************************ */
    }

    if (in_array($userToUpdate->role,['teach','fisio','nutri','empl','teach_nutri','teach_fisio'])) {
      $userToUpdate->iban = $request->input('iban');
      $userToUpdate->ss = $request->input('ss');
      $userToUpdate->priority = $request->input('priority');
      $CoachRates = \App\Models\CoachRates::where('id_user', $userToUpdate->id)->first();

      if (!$CoachRates) {
        $CoachRates = new \App\Models\CoachRates();
        $CoachRates->id_user = $userToUpdate->id;
      }
      $CoachRates->salary = intval($request->input('salario_base'));
      $CoachRates->ppc = $request->input('ppc');
      $CoachRates->pppt = $request->input('pppt');
      $CoachRates->ppcg = $request->input('ppcg');
      $CoachRates->comm = $request->input('comm');
      $CoachRates->save();
    }

    if ($userToUpdate->save()) {
      if ($userToUpdate->role == 'admin') {

        return redirect('/admin/usuarios');
      } 

      return redirect()->back()->with('success', 'Registro actualizado');
      
    }
  }

  public function disable($id) {
    $usuario = User::find($id);
    $usuario->status = 0;
    if ($usuario->save()){
      $usuario->newMetaContent('disable',Auth::user()->id);
      die('OK');
    } 
    else die('ERROR');
    if ($usuario->save()) {
      $usuario->newMetaContent('disable',Auth::user()->id);
      if ($usuario->role == 'admin') {
        return redirect('/admin/usuarios');
      } elseif ($usuario->role == 'teach' || $usuario->role == 'teacher') {
        return redirect('/admin/entrenadores');
      } else {
        return redirect('/admin/clientes');
      }
    }
  }

  public function activate($id) {
    $usuario = User::find($id);
    $usuario->status = 1;
    if ($usuario->save()){
      $usuario->newMetaContent('activate',Auth::user()->id);
       die('OK');
    }
    else die('ERROR');
  }

  public function actualizarUsuario($id) {

    return view('/admin/usuarios/_form', [
        'rates' => \App\Models\Rates::all(),
        'user' => User::find($id)
    ]);
  }

  public function sendEmailEntrenadores($id) {

    # _info_trainers_email
    $trainer = User::find($id);
    $emailTrainer = $trainer->email;
    $sended = Mail::send('emails._info_trainers_email', ['user' => $trainer], function ($message) use ($emailTrainer) {
              $message->subject('Registro de Usuario');
              $message->from(config('mail.from.address'), config('mail.from.name'));
              $message->to($emailTrainer);
            });

    return "Correo enviado a " . $emailTrainer;
//         return "No se pudo enviar el correo a ".$emailTrainer;
  }

  public function duplicateRatesUser($date = "") {
    $date = Carbon::now();
    $users = User::where('role', 'user')->get();

    foreach ($users as $user) {
      $oldRatesUser = UserRates::where('id_user', $user->id)
              ->whereMonth('created_at', '=', $date->copy()->format('m'))
              ->whereYear('created_at', '=', $date->copy()->format('Y'))
              ->get();
      echo "Cliente: " . $user->name . "<br>";
      $total_tarifas_cliente = count($oldRatesUser);
      echo "Tarifas encontradas para este cliente (" . $total_tarifas_cliente . ") en el mes de " . $date->copy()
              ->format('m') . " : <br>";
      if ($total_tarifas_cliente > 0) {
        foreach ($oldRatesUser as $oldRateUser) {
          echo $oldRateUser->rate->name . " ";
          if ($oldRateUser->rate->type != 4 || !preg_match('/BONO/i', $oldRateUser->rate->name)) {
            if ($oldRateUser->rate->mode <= 1) {
              $actualDate = $date->copy()->addMonth();
              $isRateExistNow = UserRates::where('id_user', $user->id)
                      ->where('id_rate', $oldRateUser->id_rate)
                      ->whereMonth('created_at', '=', $actualDate->copy()
                              ->format('m'))
                      ->whereYear('created_at', '=', $actualDate->copy()
                              ->format('Y'))
                      ->get();

              if (count($isRateExistNow) == 0) {
                $newRateUser = new UserRates();
                $newRateUser->id_user = $oldRateUser->id_user;
                $newRateUser->id_rate = $oldRateUser->id_rate;
                $newRateUser->created_at = $actualDate->copy()->startOfMonth();
                $newRateUser->updated_at = $actualDate->copy()->startOfMonth();
                $newRateUser->save(['timestamps' => false]);

                echo "Duplicada para el mes de " . $actualDate->copy()->format('m') . "!!<br>";
              } else {
                echo "Ya existe la tarifa<br>";
              }
            } else {
              echo "ya pago, la tarifa No es mensual";
            }
          } else {
            echo "La tarifa no se cobra de forma mensual";
          }

          echo "<br>";
        }
      }

      echo "<br><br><br>";
    }
  }

  public function informRate(Request $request) {
    //_informRateUser
    $rate = \App\Models\Rates::find($request->idRate);
    $userRates = UserRates::where('id_user', $request->idUser)->where('id_rate', $request->idRate)
                    ->orderBy('created_at', 'desc')->get();
    if ($rate->type == 4) {

      if (count($userRates) > 0) {
        $userRateCreated = $userRates[0]->created_at;
      }

      $dateCreatedUserRate = Carbon::createFromFormat('Y-m-d H:i:s', $userRateCreated);
      $classes = \App\Assistance::where('id_user', $request->idUser)
              ->where('date_assistance', ">", $dateCreatedUserRate->copy()
                      ->format('Y-m-d H:i:s'))
              ->get();
    } else {
      $classes = \App\Assistance::where('id_user', $request->idUser)
              ->whereYear('date_assistance', "=", date('Y'))
              ->whereMonth('date_assistance', "=", date('m'))
              ->get();
    }

    return view('/admin/usuarios/_informRateUser', [
        'classes' => $classes,
        'rate' => $rate,
        'userRates' => $userRates,
    ]);
  }

  public static function getPendingPaymentByMonth($date) {
    $pendiente = 0;
    $users = User::where('role', 'user')->where('status', 1)->get();
    $month = date('m', strtotime($date));
    $year = date('Y', strtotime($date));

    $ratesLst = \App\Models\Rates::all()->pluck('price', 'id')->toArray();
    $users = User::where('role', 'user')->where('status', 1)
            ->join('users_rates', 'users.id', '=', 'users_rates.id_user')
            ->whereYear('users_rates.created_at', '=', $year)
            ->whereMonth('users_rates.created_at', '=', $month)
            ->get();

    foreach ($users as $user) {
      if (isset($ratesLst[$user->id_rate])) {
        $cobro = \App\Charges::where('id_user', $user->id)
                ->where('id_rate', $user->id_rate)
                ->whereYear('date_payment', '=', $year)
                ->whereMonth('date_payment', '=', $month)
                ->count();

        if ($cobro == 0) {
          $pendiente += $ratesLst[$user->id_rate];
        }
      }
    }

    return $pendiente . "€";
  }

  function sendConsent(Request $request){
    $uID = $request->input('id_user',null);
    $type = $request->input('type',null);
    if (!$uID){
      return response()->json(['error','usuario no encontrado']);
    }
    $oUser = User::find($uID);
    if (!$oUser){
      return response()->json(['error','usuario no encontrado']);
    }
    
    $email = $oUser->email;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
      return response()->json(['error',$email.' no es un mail válido']);
    }
    
    $doc = $tit = $subject = $link = '' ;
    switch ($type){
      case 'fisioIndiba':
        $tit = 'CONSENTIMIENTO FISIOTERAPIA CON INDIBA';
        $doc = 'CONSENTIMIENTO-FISIOTERAPIA-CON-INDIBA.pdf';
        $subject = 'Firma de consentimiento';
        break;
      case 'sueloPelvico':
        $tit = 'CONSENTIMIENTO SUELO PELVICO';
        $doc = 'CONSENTIMIENTO-SUELO-PELVICO.pdf';
        $subject = 'Firma de consentimiento';
        break;
      case 'autorizacion':
        $tit = 'Autorización Infantil';
        $subject = 'Firma de Autorización Infantil';
        break;
      case 'contrato':
        
        $uPlan = $oUser->getPlan();
        if ($uPlan){
          if ($uPlan == 'fidelity'){
              $tit = 'CONTRATO PLAN FIDELITY';
              $subject = 'Firma de contrato: PLAN FIDELITY';
          }
          if ($uPlan == 'basic'){
              $tit = 'CONTRATO PLAN BÁSICO';
              $subject = 'Firma de contrato: PLAN NORMAL';
          }
        }
        break;
      case 'leform':
          $tit = 'CUESTIONARIO CONSENTIMIENTO INFORMADO';
          $subject = 'Firma de CUESTIONARIO CONSENTIMIENTO INFORMADO';
          break;
      case 'esthetic':
          $tit = 'CONSENTIMIENTO INFORMADO';
          $subject = 'Firma de CONSENTIMIENTO INFORMADO';
          break;
      case 'peeling':
            $tit = 'CONSENTIMIENTO PEELING QUÍMICO';
            $subject = 'Firma de CONSENTIMIENTO PEELING QUÍMICO';
          break;
      case 'presoterapia':
            $tit = 'CONSENTIMIENTO PRESOTERAPIA';
            $subject = 'Firma de CONSENTIMIENTO PRESOTERAPIA';
          break;
      default:
        $tit = '';
        $doc = '';
        break;
    }
    
   
    $link = \App\Services\LinksService::getLinkContracts($uID,$type);
    
    
    $sended = Mail::send('emails._sign-consent', ['user' => $oUser,'tit'=>$tit,'link'=>$link], function ($message) use ($subject,$email) {
          $message->subject($subject);
          $message->from(config('mail.from.address'), config('mail.from.name'));
          $message->to($email);
        });
    return response()->json(['OK','Email enviado']);
  }
  
  function getLinkContracts(Request $request){
    $uID = $request->input('id_user',null);
    $type = $request->input('type',null);
    if (!$uID){
      return response()->json(['error','usuario no encontrado']);
    }
    $oUser = User::find($uID);
    if (!$oUser){
      return response()->json(['error','usuario no encontrado']);
    }
    
    $email = $oUser->email;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
      return response()->json(['error',$email.' no es un mail válido']);
    }
   
    $link = \App\Services\LinksService::getLinkContracts($uID,$type);
    if ($link){
      return response()->json(['OK',$link]);
    } else {
      return response()->json(['error','link no válido']);
    }
  }
}
