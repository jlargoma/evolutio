<?php

namespace App\Services;

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
}
