<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

trait EncuestaNutriTraits {

  public function get_encNutriFields() {

    $f = [];
    for ($i = 1; $i < 23; $i++)
      $f[] = 'nutri_q' . $i;
    for ($i = 1; $i < 5; $i++)
      $f[] = 'nutri_q22_1_' . $i;
    for ($i = 1; $i < 5; $i++)
      $f[] = 'nutri_q22_2_' . $i;

    return $f;
  }

  public function get_nutriQuestions() {

    $qstion1 = [
        'nutri_q1' => 'Nombre completo',
        'nutri_q2' => 'Fecha de nacimiento',
        'nutri_q3' => 'Sexo',
        'nutri_q4' => 'Intervenciones quirúrgicas',
        'nutri_q5' => 'Patologías? (Hipertensión, diabetes, colesterol alto, tiroides…)',
        'nutri_q6' => 'Toma alguna medicación?',
        'nutri_q7' => 'Alergias o intolerancias alimentarias?',
        'nutri_q8' => 'Alimentos que rechaza',
        'nutri_q9' => 'Agua / infusiones?',
        'nutri_q10' => 'Digestiones',
        'nutri_q11' => 'Deposiciones',
        'nutri_q12' => 'Cuantas veces orina al día?',
        'nutri_q13' => 'Ha seguido alguna dieta anteriormente?',
        'nutri_q14' => 'Suele comer fuera de casa? (catering de la empresa, menú de bar, etc)',
        'nutri_q15' => 'Con cuantas personas convive?',
        'nutri_q16' => 'Quien cocina habitualmente?',
        'nutri_q17' => 'Tipo de alimentación?',
        'nutri_q18' => 'Cuantas comidas hace al día?',
        'nutri_q19' => 'Hace algún tipo de deporte o acude al gym? (mínimo 1 hora)',
        'nutri_q20' => 'En su actividad laboral, pasa gran parte de la jornada sentado?',
        'nutri_q21' => 'Cómo acude a su puesto de trabajo?',
        'nutri_q22' => 'Diario de alimentación',
    ];
    //Entres semana / Fines de semana

    $qstion2 = [
        'nutri_q22_1_1' => 'Desayuno',
        'nutri_q22_1_2' => 'Comida',
        'nutri_q22_1_3' => 'Cena',
        'nutri_q22_1_4' => 'Snacks / Entrehoras',
        'nutri_q22_2_1' => 'Desayuno',
        'nutri_q22_2_2' => 'Comida',
        'nutri_q22_2_3' => 'Cena',
        'nutri_q22_2_4' => 'Snacks / Entrehoras',
    ];
    $options = [
        'nutri_q3' => ['Masculino', 'Femenino'],
        'nutri_q9' => ['Menos de 500 ml', 'Entre 500 ml y 1,5 l', 'Entre 1,5 l y 2,5 l', 'Más de 2,5 l'],
        'nutri_q10' => ['Normales', 'Pesadas y lentas'],
        'nutri_q11' => ['Varias veces al día ', 'Una vez al día', 'Cada 2-3 días', 'Estreñimiento crónico'],
        'nutri_q12' => ['Menos de 4 veces', 'Entre 4 y 10 veces', 'Más de 10 veces'],
        'nutri_q13' => ['Si', 'No'],
        'nutri_q14' => ['Si', 'No', 'Algún día'],
        'nutri_q17' => ['Onmívora', 'Vegana', 'Vegetariana', 'Ovolacteovegetariana', 'Otra'],
        'nutri_q18' => ['2', '3', '5'],
        'nutri_q19' => ['Menos de 3 veces a la semana', 'Entre 3 y 5 días a la semana', 'No hago ningún tipo de deporte'],
        'nutri_q20' => ['Si', 'No'],
        'nutri_q21' => ['Coche / moto', 'Transporte público', 'Bicicleta / patinete', 'Caminando'],
    ];

    return [
        'qstion1' => $qstion1,
        'qstion2' => $qstion2,
        'options' => $options,
    ];
  }

  public function get_encNutri($user) {

    $fields = $this->get_encNutriFields();
    $data = $user->getMetaContentGroups($fields);
    foreach ($fields as $f)
      if (!isset($data[$f]))
        $data[$f] = null;


    $code = encriptID($user->id) . '-' . encriptID(time() * rand());
    $keys = $code . '/' . getKeyControl($code);
    $data['url'] = \App\Services\LinksService::getLinkEncuesta($user->id);;
    $data['url_dwnl'] = '/admin/ver-encuesta/' . $keys;
    $data['url_get'] = '/admin/ver-encuesta/' . $keys;

    
    
    $lstFiles = [];
    $oFiles = \App\Models\UsersFiles::where('id_user',$user->id)
            ->where('type','nutri')->get();
    if ($oFiles){
      foreach ($oFiles as $item){
        $lstFiles[$item->id] = [
            'name'=>$item->file_name,
            'url'=>\App\Services\LinksService::getLinkNutriFile($item->id)
        ];
      }
    }
    $data['lstFiles'] = $lstFiles;
    
    return array_merge($data, $this->get_nutriQuestions());
  }

  public function show_encNutri($user) {

    $fields = $this->get_encNutriFields();
    $data = $user->getMetaContentGroups($fields);
    foreach ($fields as $f)
      if (!isset($data[$f]))
        $data[$f] = null;

    return array_merge($data, $this->get_nutriQuestions());
  }

  public function setEncNutri(Request $request) {

    $fields = $this->get_encNutriFields();
    $code = $request->input('_code', '');
    $aCode = explode('-', $code);
    if (count($aCode) != 2)
      return 'error1';
    if ($request->input('_control') != getKeyControl($code))
      return 'error2';

    $uid = desencriptID($aCode[0]);
    $oUser = User::find($uid);
    if (!$oUser) {
      abort(404);
      exit();
    }
    $this->updEncNutri($request, $oUser);
    return redirect()->back()->with('success', 'Encuesta enviada.');
  }

  public function setEncNutri_Admin(Request $request) {

    $uid = $request->input('uid', '');
    $oUser = User::find($uid);
    if (!$oUser) {
      abort(404);
      exit();
    }
    $this->updEncNutri($request, $oUser);
    return redirect()->back()->with('success', 'Encuesta guardada.');
  }

  public function updEncNutri(Request $request, $oUser) {
    $fields = $this->get_encNutriFields();
    $data = $oUser->getMetaContentGroups($fields);
    $req = $request->all();
    $metaDataADD = $metaDataUPD = [];
//    dd($fields);
    foreach ($fields as $f) {
      if (isset($req[$f])) {
        if (isset($data[$f]))
          $metaDataUPD[$f] = $req[$f];
        else
          $metaDataADD[$f] = $req[$f];
      }
    }

    $oUser->setMetaContentGroups($metaDataUPD, $metaDataADD);
  }

  function seeEncuestaNutri($code, $control) {
    $aCode = explode('-', $code);
    if (count($aCode) != 2)
      return 'error1';
    if ($control != getKeyControl($code))
      return 'error2';

    $uid = desencriptID($aCode[0]);
    $oUser = User::find($uid);
    if (!$oUser) {
      abort(404);
      exit();
    }

    $encNutri = $this->get_encNutri($oUser);
    //dd($encNutri);
    return view('customers.printEncuestaNutri', [
        'data' => $encNutri
    ]);
  }

  function formEncuestaNutri($code, $control) {


    $aCode = explode('-', $code);
    if (count($aCode) != 2)
      return 'error1';
    if ($control != getKeyControl($code))
      return 'error2';

    $uid = desencriptID($aCode[0]);
    $oUser = User::find($uid);
    if (!$oUser) {
      abort(404);
      exit();
    }
    $u_name = $oUser->name;

    $nutri_q1 = $oUser->getMetaContent('nutri_q1');
    if ($nutri_q1) {
      return view('customers.encuestaNutri', ['already' => true]);
    }

    $encNutri = $this->get_encNutri($oUser);
    return view('customers.encuestaNutri', [
        'data' => $encNutri,
        'user' => $oUser,
        'code' => $code,
        'control' => $control,
        'url_dwnl' => '/descargar-encNutri/' . $code . '/' . $control,
    ]);
  }

  public function clearEncuestaNutri(Request $request) {
    $uID = $request->input('uID', null);

    $lstKeys = $this->get_nutriQuestions();
    $lstKeys = array_merge($lstKeys['qstion1'], $lstKeys['qstion2']);

    DB::table('user_meta')
            ->where('user_id', $uID)->whereIn('meta_key', array_keys($lstKeys))
            ->delete();

    return 'OK';
  }

  public function sendEncuestaNutri(Request $request) {
    $uID = $request->input('uID', null);
    $oUser = User::find($uID);
    if (!$oUser)
      return 'Usuario no encontrado';
    $already = $oUser->getMetaContent('nutri_q1');
    if ($already)
      return 'La encuesta ya se encuentra completada';

    $urlEncuesta = \App\Services\LinksService::getLinkEncuesta($oUser->id);

    $email = $oUser->email;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
      return $email . ' no es un mail válido';
    try {
      $subj = 'Encuesta de Nutrición - evolutio';
      $sended = \Illuminate\Support\Facades\Mail::send('emails._encuestaNutri', [
                  'user' => $oUser,
                  'urlEntr' => $urlEncuesta,
                      ], function ($message) use ($email, $subj) {
                        $message->subject($subj);
                        $message->from(config('mail.from.address'), config('mail.from.name'));
                        $message->to($email);
                        $message->attach(public_path('/img/protocolo.jpeg'), array(
                            'as' => 'Protocolo Covid',
                            'mime' => 'image/jpeg'));
                      });
    } catch (\Exception $ex) {
      return ($ex->getMessage());
    }
    return 'OK';
  }

  public function autosaveNutri(Request $req) {
    $uID = $req->input('id');
    $field = $req->input('field');
    $val = $req->input('val');
    $oUser = User::find($uID);
    if ($oUser) {
      $oUser->setMetaContent($field, $val);
      die('OK');
    }
    die('error');
  }

  public function saveFilesNutri(Request $req) {
    $uID = $req->input('uid');
    $oUser = User::find($uID);
    if ($oUser) {

      if ($req->hasfile('file')) {
        $validated = $req->validate(
                ['fileName' => 'required'],
                ['file' => 'required|file|mimes:doc,docx,pdf,png,jpg|max:204800'],
                ['file.mimes' => 'El archivo debe ser doc,docx,pdf,png o jpg',
                    'file.required' => 'El archivo debe ser doc,docx,pdf,png o jpg',
                    'fileName.required' => 'El nombre del archivo es requerido',
        ]);

        $file = $req->file('file');
        $fName = $req->input('fileName');

        $filename = urlencode($fName) . '_' . $uID . '.' . $file->extension();
        $oUser->setMetaContent('nutri_file', $filename);
        \Storage::disk('local')->put('nutri/' . $filename, \File::get($file));
        $path = storage_path('app/nutri/' . $filename);

        $uFile = new \App\Models\UsersFiles();
        $uFile->id_user = $oUser->id;
        $uFile->file_name = $fName;
        $uFile->file_path = 'app/nutri/' . $filename;
        $uFile->id_coach = \Illuminate\Support\Facades\Auth::user()->id;
        $uFile->type = 'nutri';
        $uFile->save();

        $resp = \App\Services\MailsService::sendMailNutriFile($oUser, $path, $file->getClientMimeType(), $file->extension());

        return back()->with(['success' => 'archivo guardado']);
      }
    } else {
      return back()->withErrors(['Usuario no encontrado']);
    }



    return back()->withErrors(['No se ha llevado ninguna operación']);
  }

  public function delFilesNutri(Request $req) {
    $uID = $req->input('uid');
    $fID = $req->input('fid');
    $oFile = \App\Models\UsersFiles::find($fID);
    if ($oFile && $oFile->id_user == $uID){
      $oFile->delete();
      return back()->with(['success' => 'archivo eliminado']);
    } else {
      return back()->withErrors(['Archivo no encontrado']);
    }
  }

  function getFileNutri($code, $control) {


    $aCode = explode('-', $code);
    if (count($aCode) != 2)
      return 'error1';
    if ($control != getKeyControl($code))
      return 'error2';

    $fID = desencriptID($aCode[0]);
    $oFile = \App\Models\UsersFiles::find($fID);
    if ($oFile) {
      $path = storage_path($oFile->file_path);
      if (!File::exists($path)) {
        abort(404);
      }

      $file = File::get($path);
      $type = File::mimeType($path);

      $response = \Response::make($file, 200);
      $response->header("Content-Type", $type);

      return $response;
    } else {
      return back()->withErrors(['Archivo no encontrado']);
    }
  }
  
  function editEncuestaNutri($id){
    $oUser = User::find($id);
    return view('/admin/usuarios/clientes/encuestaNutri', [
        'user'=>$oUser,
        'encNutr'=>$this->get_encNutri($oUser)
        ]);
  }

}
