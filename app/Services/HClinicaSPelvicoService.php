<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class HClinicaSPelvicoService {

  public function get_encFields() {

    $f = [];
    for ($i = 1; $i <30; $i++)
      $f[] = 'hclinicSP_q' . $i;
   
//    $f[] = 'hclinicSP_PainImg';
    return $f;
  }

  public function get_Options() {
    return [ ];
  }
  public function get_Questions() {
    return [
        'hclinicSP_q1' => 'Nombre',
        'hclinicSP_q2' => 'Apellidos',
        'hclinicSP_q3' => 'Fecha de nacimiento',
        'hclinicSP_q4' => 'edad',
        'hclinicSP_q5' => 'Código postal',
        'hclinicSP_q6' => 'Población',
        'hclinicSP_q7' => 'Dirección',
        'hclinicSP_q8' => 'Teléfono',
        'hclinicSP_q9' => 'Móvil',
        'hclinicSP_q10' => 'DNI',
        'hclinicSP_q11' => 'e-mail',
        'hclinicSP_q12' => 'Profesión',
        'hclinicSP_q13' => 'Deportes',
        'hclinicSP_q14' => 'Motivo de la consulta',
        'hclinicSP_q15' => 'Duración de los síntomas',
        'hclinicSP_q16' => 'Antecedentes personales',
        'hclinicSP_q17' => 'Antecedentes quirúrgicos',
        'hclinicSP_q18' => 'Medicación / Alergias',
        'hclinicSP_q19' => 'Antecedentes urológicos',
        'hclinicSP_q20' => 'Antecedentes obstétricos',
        'hclinicSP_q21' => 'Antecedentes ginecológicos',
        'hclinicSP_q22' => 'Antecedentes coloproctológicos',
        'hclinicSP_q23' => 'Pruebas diagnosticas',
        'hclinicSP_q24' => 'Exploración'
    ];
  }

  public function get_enc($user) {

    $fields = $this->get_encFields();
    $data = $user->getMetaContentGroups($fields);
    foreach ($fields as $f)
      if (!isset($data[$f]))
        $data[$f] = null;

    $code = encriptID($user->id) . '-' . encriptID(time() * rand());
    $keys = $code . '/' . getKeyControl($code);
    $data['url'] = \App\Services\LinksService::getLinkHClinicaSPelv($user->id);
    $data['urlCode'] = \App\Services\LinksService::getLinkBasic($user->id);
    $data['url_get'] = '/admin/ver-historia-clinica-suelo-pelvico/' . $keys;

    return['resps'=>$data, 'questions'=>$this->get_Questions()];
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
    foreach ($fields as $f) {
      if (isset($req[$f])) {
        if(is_array($req[$f])) $req[$f] = json_encode($req[$f]);
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

    $hclinicSP_q1 = $oUser->getMetaContent('hclinicSP_q1');
    if ($hclinicSP_q1) {
      return ['already' => true];
    }

    $enc = $this->get_enc($oUser);
    return [
        'data' => $enc['questions'],
        'resp' => $enc['resps'],
        'user' => $oUser,
        'code' => $code,
        'control' => $control,
        'options' => $this->get_Options(),
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
    $already = $oUser->getMetaContent('hclinicSP_q1');
    if ($already)
      return 'La historia clínica ya se encuentra completada';

    $url = \App\Services\LinksService::getLinkHClinicaSPelv($oUser->id);

    $email = $oUser->email;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
      return $email . ' no es un mail válido';
    try {
      $subj = 'Historia clínica Suelo Pélvico - evolutio';
      $content = 'Hola! '.$oUser->name.'<br><br>
        <p style="color: black">
          Necesitamos que nos completes tu historia clínica de Suelo Pélvico en <strong> Evolutio</strong>
        </p>
        <p>
        Para ello puede hacer click en el siguiente enlace ó cópielo y péguelo en su navegador de confianza
        <a href="'.$url.'" title="historia clínica Suelo Pélvico">'.$url.'</a>
        </p>
        <h5 style="color: black ;margin-bottom: 5px;">
            Muchas gracias por tu confianza en nosotros!! Tú compromiso es el nuestro
        </h5>';
      $sended = \Illuminate\Support\Facades\Mail::send('emails.base', [
                  'tit' => $subj,
                  'mailContent' => $content,
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
    if (str_contains($val,',,,')){
      $val = json_encode(explode(',,,',$val));
    }
    $oUser = User::find($uID);
    if ($oUser) {
      $oUser->setMetaContent($field, $val);
      die('OK');
    }
    die('error');
  }

  function editEncuesta($id){
    $oUser = User::find($id);
    $obj = $this->get_enc($oUser);
    return [
        'user'=>$oUser,
        'resp' => $obj['resps'],
        'data' => $obj['questions'],
        'options' => $this->get_Options(),
        ];
  }

}
