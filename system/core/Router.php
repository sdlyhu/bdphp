<?php
namespace system\core;

class Router {

  private static $uri;
  private static $moudle;
  private static $controller;
  private static $action;

  static function init ()
  {
    //获得uri
    self::getUri();
    //解析uri
    self::praseUri();
    //运行路由
    self::run();
  }

  private static function run()
  {
    $moudle = self::$moudle;
    $controller = self::$controller;
    $action = self::$action;
    $class_name = "app\\{$moudle}\\controllers\\{$controller}Controller";
    $obj = new $class_name;
    $obj->$action();
  }

  private static function praseUri()
  {
    $moudle_list = config('app','moudle_list');
    $uri = self::$uri;
    $uri_count = count($uri);
    //默认路由
    $moudle     = strtolower($moudle_list[0]);
    $controller = 'Index';
    $action     = 'index';

    if ($uri_count == 1) {
      if (!empty($uri[0])) {
        if (in_array($uri[0], $moudle_list)) {
          $moudle = $uri[0];
        }
      }
    }
    if ($uri_count == 2) {
      if (in_array($uri[0], $moudle_list)) {
        $moudle = $uri[0];
        $controller = ucfirst($uri[1]);
      } else {
        $controller = ucfirst($uri[0]);
        $action = $uri[1];
      }
    }
    if ($uri_count >= 3) {
      if (in_array($uri[0], $moudle_list)) {
        $moudle = $uri[0];
        $controller = ucfirst($uri[1]);
        $action = $uri[2];
        unset($uri[2]);
      } else {
        $controller = ucfirst($uri[0]);
        $action = $uri[1];
      }

      //填充get参数
      unset($uri[0]);
      unset($uri[1]);

      $uri = array_values($uri);
      for ($i=0; $i < count($uri); $i++) {
        if (!empty($uri[$i+1]) && !($i % 2)) {
          $_GET[$uri[$i]] = $uri[$i+1];
        }
      }
    }
    self::$moudle     = $moudle;
    self::$controller = $controller;
    self::$action     = $action;
  }

  private static function getUri()
  {
    $uri = strtolower($_SERVER['REQUEST_URI']);
    $uri = strip_tags($uri);

    if (is_numeric(strpos($uri, 'index.php'))) {
      $uri = str_replace('/index.php/', '', $uri);
    }
    $uri = trim($uri,'/');
    self::$uri = explode('/', $uri);
  }

  static function getMoudle()
  {
    return self::$moudle;
  }

  static function getController()
  {
    return self::$controller;
  }

  static function getAction()
  {
    return self::$action;
  }
}