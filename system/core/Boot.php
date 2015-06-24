<?php
namespace system\core;

use system\core\Router;

class Boot {

  protected static $config;

  static function run()
  {
    //获得配置
    self::$config = config('app');
    //设置时区
    date_default_timezone_set(self::$config['TIMEZONE']);
    //开启session
    self::$config['SESSION_AUTO_START'] && session_start();
    //定义请求方式
    define('IS_AJAX',ajax_request());
    define('IS_POST',post_request());
    //错误提示
    self::openWhoops();
    //载入ORM
    self::loadOrm();
    //运行路由
    Router::init();
  }

  private static function loadOrm () {
    // Eloquent ORM
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection(require '../config/database.php');
    $capsule->bootEloquent();
  }

  private static function openWhoops ()
  {
    if (self::$config['DEBUG'] === true) {
      error_reporting(E_ALL);
      $whoops = new \Whoops\Run;
      $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
      $whoops->register();
    } else {
      error_reporting(0);
    }
  }

}