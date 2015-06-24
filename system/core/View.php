<?php
namespace system\core;
use system\core\Router;
class View {

  protected static $data = array();

  static function assign($key,$value) {
    self::$data[$key] = $value;
  }

  static function make($tpl = null) {
    extract(self::$data);
    $moudle     = Router::getMoudle();
    $controller = ucfirst(Router::getController());
    $action     = Router::getAction();
    if (empty($tpl)) {
      include "../app/{$moudle}/views/{$controller}/{$action}.html";
    } else {
      $tpl = rtrim($tpl,'.html');
      include "../app/{$moudle}/views/{$tpl}.html";
    }
  }
}
