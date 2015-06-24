<?php
namespace system\core;

use Predis\Client;

class Redis {

  protected static $config = array();

  protected static $redis;

  public static function init ()
  {
    if (empty(self::$config)) {
      self::$config = config('redis');
    }
    if (empty(self::$redis)) {
      self::$redis = new Client(self::$config);
    }
  }

  public static function set ($key,$value,$time=null,$unit=null)
  {
    self::init();
    //检测值是否存在
    if (self::$redis->exists($key)) {
      self::$redis->del($key);
    }
    if ($time) {
      switch ($unit) {
        case 'h':
          $time *= 3600;
          break;
        case 'm':
          $time *= 60;
          break;
        case 's':
          break;
        case 'ms':
          break;
        default:
          throw new InvalidArgumentException('单位只能是 h m s ms');
          break;
      }
      if ($unit=='ms') {
        self::_psetex($key,$value,$time);
      } else {
        self::_setex($key,$value,$time);
      }
    } else {
      self::$redis->set($key,$value);
    }
  }

  public static function get($key)
  {
    self::init();
    return self::$redis->get($key);
  }

  public static function delete($key)
  {
    self::init();
    return self::$redis->del($key);
  }

  public static function has($key)
  {
    self::init();
    if (self::$redis->exists($key)) {
      return self::$redis->get($key);
    } else {
      return false;
    }
  }

  private static function _setex($key,$value,$time)
  {
    self::$redis->setex($key,$time,$value);
  }

  private static function _psetex($key,$value,$time)
  {
    self::$redis->psetex($key,$time,$value);
  }
}