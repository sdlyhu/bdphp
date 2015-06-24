<?php

namespace app\home\controllers;

use system\core\View;
/**
* 默认控制器
*/
class IndexController{

  /**
   * 默认方法
   */
  function index() {
    View::make();
  }
}