<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class EncuestaNutriService
{

  public function get_encFields_old()
  {

    $f = [];
    for ($i = 1; $i < 23; $i++)
      $f[] = 'nutri_q' . $i;
    for ($i = 1; $i < 5; $i++)
      $f[] = 'nutri_q22_1_' . $i;
    for ($i = 1; $i < 5; $i++)
      $f[] = 'nutri_q22_2_' . $i;

    return $f;
  }

  public function get_nutriQuestions_old()
  {

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

  public function get_enc($user)
  {
    $fields = $this->get_encFields();
    $data = $user->getMetaContentGroups($fields);
    foreach ($fields as $f)
      if (!isset($data[$f]))
        $data[$f] = null;


    $code = encriptID($user->id) . '-' . encriptID(time() * rand());
    $keys = $code . '/' . getKeyControl($code);
    $data['url'] = \App\Services\LinksService::getLinkEncuesta($user->id);;
    $data['url_dwnl'] = '/admin/ver-encuesta/' . $keys;
    $data['url_get'] = '/admin/ver-encuesta/' . $keys;

    return array_merge($data, $this->get_nutriQuestions());
  }


  public function setEnc(Request $request)
  {
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

  public function setEnc_Admin(Request $request)
  {
    $uid = $request->input('uid', '');
    $oUser = User::find($uid);
    if (!$oUser) {
      abort(404);
      exit();
    }
    $this->updEnc($request, $oUser);
  }

  public function updEnc(Request $request, $oUser)
  {
    $fields = $this->get_encFields();
    $data = $oUser->getMetaContentGroups($fields);
    $req = $request->all();
    $metaDataADD = $metaDataUPD = [];
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

  function seeEncuesta($code, $control)
  {
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

    $enc = $this->get_enc($oUser);
    return $enc;
  }

  function formEncuesta($code, $control)
  {


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

    //$nutri_q1 = $oUser->getMetaContent('nutri_q1');
    $nutri_q1 = $oUser->getMetaContent('nutri2_q1_1');
    if ($nutri_q1) {
      return ['already' => true];
    }

    $enc = $this->get_enc($oUser);
    return [
      'data' => $enc,
      'user' => $oUser,
      'code' => $code,
      'control' => $control,
      'url_dwnl' => '/descargar-enc/' . $code . '/' . $control,
    ];
  }

  public function clearEncuesta(Request $request)
  {
    $uID = $request->input('uID', null);

    $lstKeys = $this->get_nutriQuestions();
    $lstKeys = array_merge($lstKeys['qstion1'], $lstKeys['qstion2']);

    DB::table('user_meta')
      ->where('user_id', $uID)->whereIn('meta_key', array_keys($lstKeys))
      ->delete();

    return 'OK';
  }

  public function sendEncuesta(Request $request)
  {
    $uID = $request->input('uID', null);
    $oUser = User::find($uID);
    if (!$oUser)
      return 'Usuario no encontrado';
    $already = $oUser->getMetaContent('nutri2_q1_1');
    // $already = $oUser->getMetaContent('nutri_q1');
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
          'mime' => 'image/jpeg'
        ));
      });
    } catch (\Exception $ex) {
      return ($ex->getMessage());
    }
    return 'OK';
  }

  public function autosave(Request $req)
  {
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

  function editEncuesta($id)
  {
    $oUser = User::find($id);
    return [
      'user' => $oUser,
      'encNutr' => $this->get_enc($oUser)
    ];
  }



  public function get_encFields()
  {

    $questions = $this->get_nutriQuestions();
    $fields = [];
    foreach($questions as $q=>$qv){
      foreach($qv as $k=>$v) $fields[] = $k;
    }
    return $fields;
  }

  public function get_nutriQuestions()
  {

    $qstion1 = [ // Datos personales
      'nutri2_q1_1' => 'Nombre',
      'nutri2_q1_2' => 'Apellidos',
      'nutri2_q1_3' => 'Fecha de nacimiento',
      'nutri2_q1_4' => 'Teléfono',
      'nutri2_q1_5' => 'E-Mail',
    ];

    $qstion2 = [ //Datos laborales
      'nutri2_q2_1' => 'Profesión',
      'nutri2_q2_2' => 'Horarios',
      'nutri2_q2_3' => 'Turnos fijos o variables',
      'nutri2_q2_4' => 'Días de descanso',
      'nutri2_q2_5' => 'Si es trabajo físico o sedentario',
    ];

    $qstion3 = [ //Motivo de la consulta 
      'nutri2_q3_1' => 'Objetivo',
      'nutri2_q3_2' => 'Grado de motivación',
    ];
    $qstion4 = [ //Historia Ponderal 
      'nutri2_q4_1' => 'Peso actual',
      'nutri2_q4_2' => 'Peso estable/peso habitual',
      'nutri2_q4_3' => 'Peso máximo que recuerde',
      'nutri2_q4_4' => 'Peso mínimo que recuerde',
    ];
    $qstion5 = [ //Datos clínicos 
      'nutri2_q5_1' => 'Patologías',
      'nutri2_q5_2' => 'Alergias',
      'nutri2_q5_3' => 'Intolerancias',
      'nutri2_q5_4' => 'Sintomatología',
      'nutri2_q5_5' => 'Antecedentes familiares',
      'nutri2_q5_6' => 'Lesiones o molestias',
      'nutri2_q5_7' => 'Cirugías',
      'nutri2_q5_8' => 'Toma fármacos/motivo ',
      'nutri2_q5_9' => '(Analítica de sangre reciente)',
    ];
    $qstion6 = [ //Historial Dietético 
      'nutri2_q6_1' => 'Suplementacion',
      'nutri2_q6_2' => 'Tipo suplemento, dosis, motivo',
      'nutri2_q6_3' => 'Fuma, nº cigarrillos al día o paquetes',
      'nutri2_q6_4' => 'Bebe, nº bebidas toma',
      'nutri2_q6_5' => 'Otras sustancias',
      'nutri2_q6_6' => 'Dosis y frecuencia de consumo',
      'nutri2_q6_7' => 'Ex fumador, cuando lo dejó?',
      'nutri2_q6_8' => 'Sigue alguna dieta/cual',
      'nutri2_q6_9' => 'Si es vegetariano... Desde hace cuanto, si toma b12, cuanta, frecuencia, desde cuando',
      'nutri2_q6_10' => 'Número de comidas al día, si está cómodo con esa frecuencia ',
      'nutri2_q6_11' => 'Ayuno ',
      'nutri2_q6_12' => 'Momento del día con más apetito ',
      'nutri2_q6_13' => 'Nivel de estrés: Valorar del 1 al 10 ',
      'nutri2_q6_14' => 'Horario de levantarse',
      'nutri2_q6_15' => 'Calidad del sueño, nº horas de sueño, horario de sueño, cambio horarios de sueño fines de 
    semana, valoración del 1 al 10 del descanso, si realiza siesta, horarios, tiempo de la siesta ',
      'nutri2_q6_16' => 'Tiempo que se tarda en comer, si come sentado, de pie... con nervios, ansiedad o tranquilo 
    y relajado ',
      'nutri2_q6_17' => 'Tendencia a atracones, comer de forma compulsiva , ansiedad por comer dulces y 
    remordimiento después ',
      'nutri2_q6_18' => 'Frecuencia con que come fuera, donde, tipo de comida ',
      'nutri2_q6_19' => 'Come o pica entre horas, tipo de comida, motivo del picoteo: hambre real, aburrimiento, 
    socializar, por picar algo...',
      'nutri2_q6_20' => 'Si cocina, le gusta cocinar',
      'nutri2_q6_21' => 'Tiempo dedicado a la cocina, si poco tiempo proponer Batch-cooking ',
      'nutri2_q6_22' => 'Quien cocina en casa ',
      'nutri2_q6_23' => 'Establecimientos donde realiza la compra ',
      'nutri2_q6_24' => 'Tiempo dedica a comprar, lee etiquetas, compra con hambre...',
    ];
    $qstion7 = [ //Temas digestivos
      'nutri2_q7_1' => 'Si hay gases, distensión, pesadez',
      'nutri2_q7_2' => 'Si es crónico o con algunos alimentos',
      'nutri2_q7_3' => 'Exposición solar',
      'nutri2_q7_4' => 'Preferencias, gustos y aversiones de alimentos',
      'nutri2_q7_5' => 'Actividad física,: tipo, duración y que días los practica, si es muy ligera, ligera, moderada, 
    alta o intensa o muy intensa',
    ];
    $qstion8 = [ //Si es mujer
      'nutri2_q8_1' => 'Nº de embarazos ',
      'nutri2_q8_2' => 'Alteraciones o no ciclo menstrual ',
      'nutri2_q8_3' => 'Edad fértil o menopausia ',
      'nutri2_q8_4' => 'Si hay amenorrea ',
      'nutri2_q8_5' => 'Si toma anticonceptivos ',
    ];
    $qstion9 = [ //Si es mujer
      'nutri2_q9_1' => 'Describir un día estándar en su semana y un día estándar en el fin de semana (Recuerdo 
  24h)',
    ];
    $qstion10 = [ //Antropometria
      'nutri2_q10_1' => 'Peso',
      'nutri2_q10_2' => 'Talla',
      'nutri2_q10_3' => 'IMC (kg/m2)',
      'nutri2_q10_4' => 'Circunferencia brazo',
      'nutri2_q10_5' => 'Circunferencia pecho',
      'nutri2_q10_6' => 'Circunferencia cintura ',
      'nutri2_q10_7' => 'Circunferencia cadera ',
      'nutri2_q10_8' => 'Circunferencia muslo',
    ];
    $options = [];

    return [
      'qstion1' => $qstion1,
      'qstion2' => $qstion2,
      'qstion3' => $qstion3,
      'qstion4' => $qstion4,
      'qstion5' => $qstion5,
      'qstion6' => $qstion6,
      'qstion7' => $qstion7,
      'qstion8' => $qstion8,
      'qstion9' => $qstion9,
      'qstion10' => $qstion10,
      'options' => $options,
    ];
  }
}