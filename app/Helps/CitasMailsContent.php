<?php

namespace App\Helps;
use Illuminate\Support\Facades\File;

class CitasMailsContent {

  function get_urlEntrevista($oUser){
    $urlEntrevista = null;
    $already = $oUser->getMetaContent('nutri_q1');
    if (!$already){
      $code = encriptID($oUser->id).'-'.encriptID(time()*rand());
      $keys = $code.'/'.getKeyControl($code);
      $urlEntrevista = \URL::to('/encuesta-nutricion').'/'.$keys; 
    }
    return $urlEntrevista;
  }
  
  
  function get_urlIndiba($oUser,$oRate){
    $url = null;
    $url = $this->get_urlContracts($oUser,'fisioIndiba');
    return $url;
  }
  
  function get_urlSuelPelv($oUser,$oRate){
    $url = null;
    if ($oRate->subfamily == 'f03') //Familia: suelo Pelvico
      $url = $this->get_urlContracts($oUser,'sueloPelvico');
    return $url;
  }
  
  
  function get_urlContracts($oUser,$type){
    $url = null;
        
    $fileName = $oUser->getMetaContent('sign_'.$type);
    if ($fileName){
      $path = storage_path('/app/' . $fileName);
      if (File::exists($path))  return null;
    }
    
    $url = \App\Services\LinksService::getLinkContracts($oUser->id,$type);
    return $url;
  }
  
}
