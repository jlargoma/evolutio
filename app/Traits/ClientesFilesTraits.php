<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

trait ClientesFilesTraits {
  /* ----------------------------------------------------------------------- */
  /* ---- BEGIN:  ARCHIVOS        ------------------- */
  /* ----------------------------------------------------------------------- */

  /**
   * 
   * @param Request $req
   * @return type
   */
  public function saveFiles(Request $req) {
    $uID = $req->input('uid');
    $type = $req->input('type');
//    dd($req->all());
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
        $rute = $type.'/';
//        $oUser->setMetaContent($type.'_file', $filename);
        \Storage::disk('local')->put($rute . $filename, \File::get($file));
        $path = storage_path('app/' . $rute . $filename);

        $uFile = new \App\Models\UsersFiles();
        $uFile->id_user = $oUser->id;
        $uFile->file_name = $fName;
        $uFile->file_path = 'app/'. $rute .'/' . $filename;
        $uFile->id_coach = \Illuminate\Support\Facades\Auth::user()->id;
        $uFile->type = $type;
        $uFile->save();

        //$resp = \App\Services\MailsService::sendMailFile($oUser, $fName, $path, $file->getClientMimeType(), $file->extension());
        return back()->with(['success' => 'archivo guardado']);
      }
    } else {
      return back()->withErrors(['Usuario no encontrado']);
    }



    return back()->withErrors(['No se ha llevado ninguna operación']);
  }

  public function delFiles(Request $req) {
    $uID = $req->input('uid');
    $fID = $req->input('fid');
    $oFile = \App\Models\UsersFiles::find($fID);
    if ($oFile && $oFile->id_user == $uID) {
      $oFile->delete();
      return back()->with(['success' => 'archivo eliminado']);
    } else {
      return back()->withErrors(['Archivo no encontrado']);
    }
  }
  public function sendFileTo(Request $req) {
    $uID = $req->input('uid');
    $fID = $req->input('fid');
    $emailTo = $req->input('mail');
    $nameTo = $req->input('nameTo','');
    $uFile = \App\Models\UsersFiles::find($fID);
    if ($uFile && $uFile->id_user == $uID) {



      $path  = storage_path($uFile->file_path);
      $fName = $uFile->file_name;

      $ext = '';
      $aName = explode('.',$uFile->file_path);
      if (is_array($aName)) $ext = $aName[count($aName)-1];
      $mimeType = mime_content_type($path);
      $return = \App\Services\MailsService::sendMailFile($nameTo, $emailTo, $fName, $path, $mimeType, $ext);


      $log = new \App\Models\LogMailFile();

      $user = Auth::user();
      $log->user_id = $user->id;
      $log->customer_id = $uID;
      $log->file_id = $fID;
      $log->file_name = $fName;
      $log->email_to = $emailTo;
      $log->ip = getIP();
      $log->result = $return;
      $log->save();


      return $return;
    } else {
      return 'Archivo no encontrado';
    }
  }

  /**
   * 
   * @param type $uID
   * @param type $type
   * @return type
   */
  function getFileLst($uID, $type) {
    $lstFiles = [];
    $oFiles = \App\Models\UsersFiles::where('id_user',$uID)
            ->where('type',$type)->get();
    if ($oFiles){
      foreach ($oFiles as $item){
        $lstFiles[$item->id] = [
            'name'=>$item->file_name,
            'url'=>\App\Services\LinksService::getLinkNutriFile($item->id)
        ];
      }
    }
    return  $lstFiles;
  }
  
  
  function getFile($code, $control) {


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


  function getPhoto($uID) {

    $oUser = User::find($uID);
    if (!$oUser) return 'Usuario no encontrado';
    
    $photo = $oUser->getMetaContent('photo');
    if ($photo){
      $path = storage_path('/app/photos/' . $photo);
      if (!File::exists($path)) {
        abort(404);
      }

    //  $file = File::get($path);
      $type = File::mimeType($path);
    } else {
      //$file = File::get(public_path('/img/userIcondefault.png'));
      $path = public_path('/img/userIcondefault.png');
      $type = 'png';
    }
    // Creamos una imagen
    switch($type){ 
      case 'image/jpeg': 
          $image = imagecreatefromjpeg($path); 
          break; 
      case 'image/png': 
          $image = imagecreatefrompng($path); 
          break; 
      case 'image/gif': 
          $image = imagecreatefromgif($path); 
          break; 
      default: 
          $image = imagecreatefromjpeg($path); 
  } 


$calidad = 8; // Valor entre 0 y 100. Mayor calidad, mayor peso
header("Content-Type: image/jpeg");
imagejpeg($image, null, $calidad);
exit;


    // $response = \Response::make($file, 200);
    // $response->header("Content-Type", $type);
    // return $response;
  }

  /* ----------------------------------------------------------------------- */
  /* ---- END:  ARCHIVOS        ------------------- */
  /* ----------------------------------------------------------------------- */
}
