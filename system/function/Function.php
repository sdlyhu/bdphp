<?php
/**
 * 函数库
 * @Author: dawn
 * @Date:   2015-06-23 13:18:37
 * @Last Modified by:   dawn
 * @Last Modified time: 2015-06-24 14:43:32
 */

if (!function_exists('config')) {
  /**
   * 获取配置项
   * @param  string $config_name 配置文件名
   * @param  string $name        参数名
   * @return array or string
   */
  function config($config_name, $name = null) {
    $config = include '../config/'.strtolower($config_name).'.php';
    if ($name) {
      $name = strtoupper($name);
      return $config[$name];
    } else {
      return $config;
    }
  }
}

if (!function_exists('p')) {
  /**
   * 打印输出数据
   * @param void $var
   */
  function p ($var) {
    if ( is_bool($var) ) {
      var_dump($var);
    }
    else if ( is_null($var) ) {
      var_dump(NULL);
    }
    else {
      echo "<pre style='position:relative;z-index:1000;padding:10px;border-radius:5px;background:#F5F5F5;border:1px solid #aaa;font-size:14px;line-height:18px;opacity:0.9;'>" . print_r($var, true) . "</pre>";
    }
    die;
  }
}

if (!function_exists('input')) {
  /**
   * 获取与设置请求参数
   * @param      $var     参数如 input("cid) input("get.cid") input("get.")
   * @param null $default 默认值 当变量不存在时的值
   * @param null $filter 过滤函数
   * @return array|null
   */
  function input($var, $default = null, $filter = null)
  {
    //拆分，支持get.id  或 id
    $var = explode(".", $var);
    if (count($var) == 1) {
      array_unshift($var, 'request');
    }
    $var[0] = strtolower($var[0]);
    //获得数据并执行相应的安全处理
    switch (strtolower($var[0])) {
      case 'get' :
        $data = &$_GET;
        break;
      case 'post' :
        $data = &$_POST;
        break;
      case 'request' :
        $data = &$_REQUEST;
        break;
      case 'files' :
        $data = &$_FILES;
        break;
      case 'session' :
        $data = &$_SESSION;
        break;
      case 'cookie' :
        $data = &$_COOKIE;
        break;
      case 'server' :
        $data = &$_SERVER;
        break;
      case 'globals' :
        $data = &$GLOBALS;
        break;
      default :
        throw_exception($var[0] . 'input方法参数错误');
    }
    //没有执行参数如input("post.")时返回所有数据
    if (empty($var[1])) {
      return $data;
      //如果存在数据如$this->_get("page")，$_GET中存在page数据
    } else if (isset($data[$var[1]])) {
      //要获得参数如$this->_get("page")中的page
      $value = $data[$var[1]];
      //对参数进行过滤的函数
      $funcArr = is_null($filter) ? config('app',"FILTER_FUNCTION") : $filter;
      //参数过滤函数
      if (is_string($funcArr) && !empty($funcArr)) {
        $funcArr = explode(",", $funcArr);
      }
      //是否存在过滤函数
      if (!empty($funcArr) && is_array($funcArr)) {
        //对数据进行过滤处理
        foreach ($funcArr as $func) {
          if (!function_exists($func))
            continue;
          $value = is_array($value) ? array_map($func, $value) : $func($value);
        }
        $data[$var[1]] = $value;
        return $value;
      }
      return $value;

    } else {
      $data[$var[1]] = $default;
      return $default;
    }
  }
}

if (!function_exists('is_ssl')) {
  /**
   * 是否为SSL协议
   * @return boolean
   */
  function is_ssl()
  {
      if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
          return true;
      } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
          return true;
      }
      return false;
  }
}

if (!function_exists('protocol')) {
  /**
   * 检测访问协议
   * @return string
   */
  function protocol()
  {
    if (!is_ssl()) {
      return 'http';
    } else {
      return 'https';
    }
  }
}

if (!function_exists('asset')) {
  /**
   * 模板页面生成资源链接
   * @param  string $path asset('css/style.css')
   * @return string 资源路径
   */
  function asset($path)
  {
    return protocol() . '://' . $_SERVER['SERVER_NAME'] . '/' . ltrim($path,'/');
  }
}

if (!function_exists('redirect')) {

  /**
   * 跳转url
   * @param  string $url 要跳转的地址
   * @param  string $time 多少秒后跳转
   */
  function redirect($url,$time = 0)
  {
    if (!empty($url) && is_numeric(strpos($url, 'http'))) {
      $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
      exit($str);
    }
  }
}

if (!function_exists('url')) {
  /**
   * 组合url
   * @param  string $path moudle/controller/action
   * @param  string $param 链接参数
   * @return string
   */
  function url($path,$param = array())
  {
    //获得当前模块控制器方法
    $moudle     = system\core\Router::getMoudle();
    $controller = system\core\Router::getController();
    $action     = system\core\Router::getAction();
    //解析
    $path      = strtolower(trim($path,'/'));
    $pathArr   = explode('/', $path);
    $pathCount = count($pathArr);
    if ($pathCount == 1) {
      $action = $pathArr[0];
    } else if ($pathCount == 2) {
      $controller = $pathArr[0];
      $action = $pathArr[1];
    } else if ($pathCount == 3) {
      $moudle = $pathArr[0];
      $controller = $pathArr[1];
      $action = $pathArr[2];
    } else {
      throw new Exception('url()参数错误');
    }
    //组合url
    $url = protocol() . "://" . $_SERVER['SERVER_NAME'] . '/'
    . $moudle . '/' . ucfirst($controller) . '/' . $action;
    //解析参数
    if (!empty($param)) {
      $param_str = parse_url_array($param);
      $url .= '/' . $param_str;
    }
    return $url;
  }
}

if (!function_exists('parse_url_query')) {
  /**
   * 转换数组参数为url形式（id/1/cid/2）
   * @param  array $arr 数组参数
   * @return string
   */
  function parse_url_array($arr)
  {
    $str = '';
    foreach ($arr as $k => $v) {
      $str .= $k . '/' . $v . '/';
    }
    $str = rtrim($str,'/');
    return $str;
  }
}

if (!function_exists('cookie')) {
  /**
   * cookie处理
   * @param        $name   名称
   * @param string $value 值
   * @param mixed $option 选项
   * @return mixed
   */
  function cookie($name, $value = '', $option = array())
  {
    // 获取默认配置
    $config = config('app');
    // 默认设置
    $config = [
      'prefix' => $config['COOKIE_PREFIX'], // cookie 名称前缀
      'expire' => $config['COOKIE_EXPIRE'], // cookie 保存时间
      'path' => $config['COOKIE_PATH'], // cookie 保存路径
      'domain' => $config['COOKIE_DOMAIN'], // cookie 有效域名
    ];
    //判断是否传入参数 并覆盖
    if (!empty($option) && is_array($option)) {
      $config = array_merge($config, $option);
    }
    //设置cookie
    if (!empty($value)) {
      if ($config['expire'] == '0') {
        $time = 0;
      } else {
        $time = time() + $config['expire'];
      }
      setcookie($config['prefix'].$name,$value,$time,$config['path'],$config['domain']);
    }
    //删除cookie
    if (is_null($value)) {
      setcookie($config['prefix'].$name,'',time()-3600);
    }
    //清空cookie
    if (is_null($name)) {
      unset($_COOKIE);
    }
    //读取cookie
    if ($value == '' && empty($option)) {
      return $_COOKIE[$config['prefix'].$name];
    }
  }
}

if (!function_exists('session')) {
  /**
   * session处理
   * @param string $name 名称
   * @param string $value 值
   * @return mixed
   */
  function session($name = '', $value = '')
  {
    //读取session
    if (empty($value) && $name == '' && !is_null($name)) {
      return $_SESSION;
    }
    if (empty($value) && !empty($name)) {
      return $_SESSION[$name];
    }
    //设置session
    if (!empty($value) && !empty($name)) {
      $_SESSION[$name] = $value;
    }
    //删除session
    if (is_null($value) && !empty($name)) {
      unset($_SESSION[$name]);
    }
    //清空session
    if (is_null($name)) {
      session_unset();
      session_destroy();
    }
  }
}

if (!function_exists('ajax_request')) {
  /**
   * 是否为AJAX提交
   * @return boolean
   */
  function ajax_request()
  {
      if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
          return true;
      return false;
  }
}

if (!function_exists('post_request')) {
  /**
   * 是否为POST提交
   * @return boolean
   */
  function post_request()
  {
      if (strtolower($_SERVER['REQUEST_METHOD']) == 'post')
          return true;
      return false;
  }
}








