<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PollController extends Controller {

  public function setEncNutri(Request $request) {
    $sPull = new \App\Services\EncuestaNutriService();
    $resp = $sPull->setEnc($request);
    if ($resp == 'OK')
      return redirect()->back()->with('success', 'Encuesta enviada.');
    return $resp;
  }

  public function setEncNutri_Admin(Request $request) {
    $sPull = new \App\Services\EncuestaNutriService();
    $resp = $sPull->setEnc_Admin($request);
    if ($resp == 'OK')
      return redirect()->back()->with('success', 'Encuesta enviada.');
    return $resp;
  }

  function seeEncuestaNutri($code, $control) {
    $sPull = new \App\Services\EncuestaNutriService();
    return view('customers.printEncuestaNutri', [
        'data' => $sPull->seeEncuesta($code, $control)
    ]);
  }

  function formEncuestaNutri($code, $control) {
    $sPull = new \App\Services\EncuestaNutriService();
    return view('customers.encuestaNutri', $sPull->formEncuesta($code, $control));
  }

  public function clearEncuestaNutri(Request $request) {
    $sPull = new \App\Services\EncuestaNutriService();
    return $sPull->clearEncuesta($request);
  }

  public function sendEncuestaNutri(Request $request) {
    $sPull = new \App\Services\EncuestaNutriService();
    return $sPull->sendEncuesta($request);
  }

  public function autosaveNutri(Request $req) {
    $sPull = new \App\Services\EncuestaNutriService();
    $sPull->autosave($req);
  }

  function editEncuestaNutri($id) {
    $sPull = new \App\Services\EncuestaNutriService();
    return view('/admin/usuarios/clientes/encuestaNutri', $sPull->editEncuesta($id));
  }

  /* --------------------------------------------------------- */
  /* BEGIN Historia clinica                 ------------------ */
  /* --------------------------------------------------------- */

  function formCliHistory($code, $control) {
    $sPull = new \App\Services\HClinicaService();
    return view('customers.ClinicalHistory.form', $sPull->formEncuesta($code, $control));
  }

}
