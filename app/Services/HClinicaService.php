<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class HClinicaService {

  public function get_encFields() {

    $f = [];
    for ($i = 1; $i < 51; $i++)
      $f[] = 'hclinic_q' . $i;
   
    $f[] = 'hclinic_img';
    return $f;
  }

  public function get_Questions() {

    return [
        'hclinic_q1' => 'Nombre',
        'hclinic_q2' => 'Apellidos',
        'hclinic_q3' => 'Fecha de nacimiento',
        'hclinic_q4' => 'edad',
        'hclinic_q5' => 'Código postal',
        'hclinic_q6' => 'Población',
        'hclinic_q7' => 'Dirección',
        'hclinic_q8' => 'Teléfono',
        'hclinic_q9' => 'Móvil',
        'hclinic_q10' => 'DNI',
        'hclinic_q11' => 'e-mail',
        'hclinic_q12' => 'Ocupación',
        'hclinic_q13' => 'Sexo',
        'hclinic_q14' => 'Altura',
        'hclinic_q15' => 'Peso',
        'hclinic_q16' => 'Fumador',
        'hclinic_q17' => 'Bebedor',
        'hclinic_q18' => 'Deporte',
        'hclinic_q19' => '¿Has tenido alguna enfermedad previa?',
        'hclinic_q20' => '¿Indique cual?',
        'hclinic_q21' => '¿Problemas cardiovasculares?',
        'hclinic_q22' => '¿Indique cual?',
        'hclinic_q23' => 'Hipertensión',
        'hclinic_q24' => 'Medicación',
        'hclinic_q25' => '¿Alergias?',
        'hclinic_q26' => '¿Cual?',
        'hclinic_q27' => '¿Sufre Diabetes?',
        'hclinic_q28' => '¿Cual?',
        'hclinic_q29' => '¿Toma medicación?',
        'hclinic_q30' => '¿Cual?',
        'hclinic_q31' => '¿Ha sido intervenido quirúrgicamente?',
        'hclinic_q32' => '¿Dónde y cuándo?',
        'hclinic_q33' => '¿Ha tenido algún antecedente traumático o lesión discal?',
        'hclinic_q34' => '¿Dónde y cuándo?',
        'hclinic_q35' => 'Describa brevemente el problema que le ha traído a consultarnos',
        'hclinic_q36' => '¿Cuándo comenzó? (en meses o años)',
        'hclinic_q37' => 'Comenzó de forma',
        'hclinic_q38' => '¿Con qué frecuencia lo tiene?',
        'hclinic_q39' => '¿Cómo describiría su dolor? (Señale los términos adecuados)',
        'hclinic_q40' => 'Otros',
        'hclinic_q41' => '¿Qué mejora su dolor?',
        'hclinic_q42' => '¿Qué empeora su dolor?',
        'hclinic_q43' => '¿El dolor le desierta por la noche?',
        'hclinic_q44' => '¿Piensa que se va a recuperar?',
        'hclinic_q45' => '¿Por qué?',
        'hclinic_q46' => '¿Tiene miedo a moverse? ',
        'hclinic_q47' => '¿Por qué?',
        'hclinic_q48' => '¿Piensa que su dolor tiene solución?',
        'hclinic_q49' => '¿Por qué?',
        'hclinic_q50' => '¿Qué espera del tratamiento?',
        'hclinic_q51' => '¿Piensa que se va a recuperar?'
    ];
    
    
    
    //hclinic_q38
    
    
    
  }

  public function get_enc($user) {

    $fields = $this->get_encFields();
    $data = $user->getMetaContentGroups($fields);
    foreach ($fields as $f)
      if (!isset($data[$f]))
        $data[$f] = null;


    $code = encriptID($user->id) . '-' . encriptID(time() * rand());
    $keys = $code . '/' . getKeyControl($code);
    $data['url'] = \App\Services\LinksService::getLinkHClinica($user->id);
    $data['url_get'] = '/admin/ver-historia-clinica/' . $keys;

    return array_merge($data, $this->get_Questions());
  }

  public function show_enc($user) {

    $fields = $this->get_encFields();
    $data = $user->getMetaContentGroups($fields);
    foreach ($fields as $f)
      if (!isset($data[$f]))
        $data[$f] = null;

    return array_merge($data, $this->get_Questions());
  }

  public function setEnc(Request $request) {

    $fields = $this->get_encFields();
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
    $this->updEnc($request, $oUser);
    return 'OK';
  }

  public function setEnc_Admin(Request $request) {

    $uid = $request->input('uid', '');
    $oUser = User::find($uid);
    if (!$oUser) {
      abort(404);
      exit();
    }
    $this->updEnc($request, $oUser);
    return 'OK';
  }

  public function updEnc(Request $request, $oUser) {
    $fields = $this->get_encFields();
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

  function seeEncuesta($code, $control) {
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

    return $this->get_enc($oUser);
  }

  function formEncuesta($code, $control) {


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

    $hclinic_q1 = $oUser->getMetaContent('hclinic_q1');
    if ($hclinic_q1) {
      return ['already' => true];
    }

    $enc = $this->get_enc($oUser);
//    $sino = [
//        'hclinic_q19',
//        'hclinic_q21',
//        'hclinic_q23',
//        'hclinic_q24',
//        'hclinic_q26',
//        'hclinic_q28',
//        'hclinic_q30',
//        'hclinic_q32',
//        'hclinic_q41',
//        'hclinic_q42',
//        'hclinic_q43',
//        'hclinic_q44',
//        'hclinic_q45',
//        'hclinic_q46'
//    ];
    
    return [
        'data' => $enc,
        'user' => $oUser,
        'code' => $code,
        'control' => $control,
        'url_dwnl' => '/descargar-enc/' . $code . '/' . $control,
    ];
  }

  public function clearEncuesta(Request $request) {
    $uID = $request->input('uID', null);

    $lstKeys = $this->get_Questions();
    DB::table('user_meta')
            ->where('user_id', $uID)->whereIn('meta_key', array_keys($lstKeys))
            ->delete();

    return 'OK';
  }

  public function sendEncuesta(Request $request) {
    $uID = $request->input('uID', null);
    $oUser = User::find($uID);
    if (!$oUser)
      return 'Usuario no encontrado';
    $already = $oUser->getMetaContent('hclinic_q1');
    if ($already)
      return 'La historia clínica ya se encuentra completada';

    $urlEncuesta = \App\Services\LinksService::getLinkEncuesta($oUser->id);

    $email = $oUser->email;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
      return $email . ' no es un mail válido';
    try {
      $subj = 'Historia clínica - evolutio';
      $sended = \Illuminate\Support\Facades\Mail::send('emails._encuesta', [
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

  public function autosave(Request $req) {
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

  function editEncuesta($id){
    $oUser = User::find($id);
    return [
        'user'=>$oUser,
        'encNutr'=>$this->get_enc($oUser)
        ];
  }

}
