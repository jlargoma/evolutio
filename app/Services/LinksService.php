<?php

namespace App\Services;
use URL;

class LinksService {

  /**
   * 
   * @param array $data
   * @return type
   */
  static function getLink($data) {
    foreach ($data as $k=>$v) $data[$k] = encriptID ($v);
    $code = implode('-', $data);
    return $code.'/'.getKeyControl($code);
  }

  /**
   * 
   * @param type $code
   * @param type $control
   * @return boolean
   */
  static function getLinkData($code,$control){
    if (getKeyControl($code) !== $control)return false;
    $data = explode('-', $code);
    foreach ($data as $k=>$v) $data[$k] = desencriptID($v);
    return $data;
  }
  /**
   * 
   * @param type $uID
   * @param type $type
   * @return boolean
   */
  static function getLinkContracts($uID,$type){
    $link = '' ;
    $code = 0;
    switch ($type){
      case 'fisioIndiba':
        $code = 1001;
        $link = URL::to('/firmar-consentimiento/').'/';
        break;
      case 'sueloPelvico':
        $code = 2002;
        $link = URL::to('/firmar-consentimiento/').'/';
        break;
      case 'contrato':
        $code = 3003;
        $link = URL::to('/firmar-contrato/').'/';
        break;
      default :
        return null;
        
    }
    
   
    $link .= self::getLink([$uID,$code,time()]);
    return $link;
  }
  
  static function getLinkEncuesta($uID){
    $code = encriptID($uID).'-'.encriptID(time()*rand());
    $keys = $code.'/'.getKeyControl($code);
    $link = URL::to('/encuesta-nutricion/').'/'.$keys;
    return $link;
  }
  static function getLinkNutriFile($uID){
    $code = encriptID($uID).'-'.encriptID(time()*rand());
    $keys = $code.'/'.getKeyControl($code);
    $link = URL::to('/archivo-nutricion/').'/'.$keys;
    return $link;
  }
  
  
}
