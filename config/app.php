<?php
$config = [
  // 调试模式
  'DEBUG'              => true,
  // 时区
  'TIMEZONE'           => 'PRC',
  // 分组（默认访问的写在第一个）
  'MOUDLE_LIST'        => ['home','admin'],
  // 请求过滤处理方法 用于input方法
  'FILTER_FUNCTION'    => ['htmlspecialchars','strip_tags'],
  //自动开启SESSION
  'SESSION_AUTO_START' => true,
  // cookie 名称前缀
  'COOKIE_PREFIX'      => '',
  // cookie 有效期
  'COOKIE_EXPIRE'      => '0',
  // cookie 保存路径
  'COOKIE_PATH'        => '/',
  // cookie 有效域名
  'COOKIE_DOMAIN'      => '',
];

return $config;