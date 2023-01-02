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

  public function sendClinicHist(Request $request) {
    $sPull = new \App\Services\HClinicaService();
    return $sPull->sendEncuesta($request);
  }

  public function setCliHistory(Request $request) {
    $sPull = new \App\Services\HClinicaService();
    $resp = $sPull->setEnc($request);
    if ($resp == 'OK')
      return redirect()->back()->with('success', 'Historia Clínica enviada');
    return $resp;
  }

  public function setCliHistory_Admin(Request $request) {
    $sPull = new \App\Services\HClinicaService();
    $resp = $sPull->setEnc_Admin($request);
    if ($resp == 'OK')
      return redirect()->back()->with('success', 'Historia Clínica editada');
    return $resp;
  }

  function seeClinicHist($code, $control) {
    $sPull = new \App\Services\HClinicaService();
    $obj = $sPull->seeEncuesta($code, $control);
    return view('customers.ClinicalHistory.print', [
        'resp' => $obj['resps'],
        'data' => $obj['questions'],
        'options' => $sPull->get_Options(),
    ]);
  }

  function editClinicHist($id) {
    $sPull = new \App\Services\HClinicaService();
    return view('/admin/usuarios/clientes/clinicalHistory', $sPull->editEncuesta($id));
  }

  public function clearClinicHist(Request $request) {
    $sPull = new \App\Services\HClinicaService();
    return $sPull->clearEncuesta($request);
  }

  public function autosaveClinicHist(Request $req) {
    $sPull = new \App\Services\HClinicaService();
    $sPull->autosave($req);
  }
  /* --------------------------------------------------------- */
  /* BEGIN Historia clinica Suelo  Pelvico    ---------------- */
  /* --------------------------------------------------------- */

  function formCliHistorySPelv($code, $control) {
    $sPull = new \App\Services\HClinicaSPelvicoService();
    return view('customers.ClinicalHistorySPelv.form', $sPull->formEncuesta($code, $control));
  }

  public function sendClinicHistSPelv(Request $request) {
    $sPull = new \App\Services\HClinicaSPelvicoService();
    return $sPull->sendEncuesta($request);
  }

  public function setCliHistorySPelv(Request $request) {
    $sPull = new \App\Services\HClinicaSPelvicoService();
    $resp = $sPull->setEnc($request);
    if ($resp == 'OK')
      return redirect()->back()->with('success', 'Historia Clínica enviada');
    return $resp;
  }

  public function setCliHistorySPelv_Admin(Request $request) {
    $sPull = new \App\Services\HClinicaSPelvicoService();
    $resp = $sPull->setEnc_Admin($request);
    if ($resp == 'OK')
      return redirect()->back()->with('success', 'Historia Clínica editada');
    return $resp;
  }

  function seeClinicHistSPelv($code, $control) {
    $sPull = new \App\Services\HClinicaSPelvicoService();
    $obj = $sPull->seeEncuesta($code, $control);
    return view('customers.ClinicalHistorySPelv.print', [
        'resp' => $obj['resps'],
        'data' => $obj['questions'],
        'options' => $sPull->get_Options(),
    ]);
  }

  function editClinicHistSPelv($id) {
    $sPull = new \App\Services\HClinicaSPelvicoService();
    return view('/admin/usuarios/clientes/clinicalHistorySPelv', $sPull->editEncuesta($id));
  }

  public function clearClinicHistSPelv(Request $request) {
    $sPull = new \App\Services\HClinicaSPelvicoService();
    return $sPull->clearEncuesta($request);
  }

  public function autosaveClinicHistSPelv(Request $req) {
    $sPull = new \App\Services\HClinicaSPelvicoService();
    $sPull->autosave($req);
  }

}
