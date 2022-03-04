<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Models\User;
use App\Models\Rates;
use App\Models\TypesRate;
use Illuminate\Support\Facades\File;

class FunctionalControler extends Controller {

  function userRates() {

    $oTRates = TypesRate::all();

    $aRates = [];
    foreach ($oTRates as $i) {
      $aux = [];
      $aRates[] = [
          'lst' => $oRates = Rates::where('type', $i->id)->orderBy('name')->get(),
          'name' => $i->name
      ];
    }

    $aUsers = User::where('role', 'user')->orderBy('name')->pluck('name', 'id');

    return view('functional.user-rates', [
        'aRates' => $aRates,
        'aUsers' => $aUsers
    ]);
  }
  
   public function seeImg($rute,$file) {
    $path = storage_path('/app/'.$rute.'/'.$file);
    if (!File::exists($path)) {
      abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = \Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
  }

}
