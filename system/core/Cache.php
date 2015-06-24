<?php
namespace system\core;

class Cache {

  //缓存目录
  protected static $dir = '../temp/cache/';
  /**
   * 设置缓存
   */
  static function set($key, $value, $time = 0)
  {
    $file = self::_file_name($key);

    //检测是否有该缓存
    if (is_file($file)) {
      unlink($file);
    }
    //编辑缓存时间
    if ($time != '0') {
      $cache_over_time = time() + $time;
    } else {
      $cache_over_time = 0;
    }

    $data = [$key => $value, 'cache_over_time' => $cache_over_time];
    $data = serialize($data);
    //写入缓存
    if (file_put_contents($file, $data) === false) {
      throw new Exception("temp 缓存目录没有写入权限");
    }
  }

  /**
   * 取得缓存
   */
  static function get($key) {
    $file = self::_file_name($key);

    //检测是否有该缓存
    if (!is_file($file)) {
      return null;
    }
    //检测缓存是否过期
    if (self::file_over_time($file)) {
      unlink($file);
      return null;
    }

    $data = file_get_contents($file);
    $data = unserialize($data);
    return $data[$key];
  }

  /**
   * 删除缓存
   */
  static function del($key) {
    $file = self::_file_name($key);
    if (is_file($file)) {
      unlink($file);
    }
  }

  /**
   * 清空缓存
   */
  static function clear()
  {
    $path = self::$dir . '*.php';
    foreach (glob($path) as $v) {
      unlink($v);
    }
  }

  /**
   * 检测文件是否过期
   */
  protected static function file_over_time($file)
  {
    $data = file_get_contents($file);
    $data = unserialize($data);
    if ($data['cache_over_time'] < time() && $data['cache_over_time'] != '0') {
      return true;
    } else {
      return false;
    }
  }

  /**
   * 获得文件名
   */
  protected static function _file_name($key)
  {
    $file = self::$dir . $key . '.php';
    return $file;
  }
}