##BDPHP

一个开箱即用的快速开发框架。
基于类似thinkphp的路由机制，laravel 的数据库操作ORM，缓存方式有文件缓存和 redis，没有包含模板引擎，有强大丰富的参数验证类库，漂亮的错误提示，等等。

###环境要求
- php >= 5.4
- 服务端（apache/nginx）开启 rewrite

###概述

####0.配置

主机域名请解析到 `public` 目录下。
config 目录下的 app.php 是项目的基本配置

####1.视图

引用视图 `use system\core\View;`
分配变量 `View::assign($name,$value);`
调用模板 `View::make();`

####2.会话

cookie($name, $value, $option);
session($key,$value);

####3.数据库操作

model文件中引用基类 `use Illuminate\Database\Eloquent\Model;`并继承它。
详情 http://www.golaravel.com/laravel/docs/5.0/eloquent/

####4.变量验证

引用基类 `use Respect\Validation\Validator;`

    $number = 123;
    Validator::numeric()->validate($number); //true

详情 http://respect.li/Validation/docs/validators.html

####5.请求

使用 input() 接收 post 或者 get 传值。并且会自动处理参数（处理方法可以在config/app.php）中配置。
`IS_POST` `IS_AJAX` 来判断请求方法。

####6.时间处理
引用基类 `use Carbon\Carbon;`
详情 http://carbon.nesbot.com/

####7.缓存

1.文件缓存
引用 `use system\core\Cache;`


    Cache::set($key,$value,$time) //设置缓存
    Cache::get($key)              //取得缓存
    Cache::del($key)              //删除缓存
    Cache::clear()                //清空缓存


2.redis缓存

引用 `use system\core\Redis;`

    Redis::set($key,$value,$time,$unit) //设置缓存
    Redis::get($key)                    //取得缓存
    Redis::delete($key)                 //删除缓存
    
####8.调试

`p()` 方法用于打印数据 并exit。

####9.辅助函数

`url('moudle/controller/action')` 生成对应链接。
`asset('css/style.css')` 模板中生成引用资源链接 （css js等文件请放到 public文件下）
`redirect($url,$time = 0)` 跳转地址。
`config($config_name, $name = null)` 读取配置项。

欢迎大家尝鲜使用，有问题可以联系 250810491@qq.com

