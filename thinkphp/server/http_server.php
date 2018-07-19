<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/6/23
 * Time: 10:53
 */

$http_server =  new swoole_http_server("0.0.0.0",9503);
/**
 * https://wiki.swoole.com/wiki/page/783.html
 * 配置静态文件根目录，与enable_static_handler配合使用。
 * 设置document_root并设置enable_static_handler为true后，
 * 底层收到Http请求会先判断document_root路径下是否存在此文件，
 * 如果存在会直接发送文件内容给客户端，不再触发onRequest回调。
 */
$http_server->set(
    [
        'enable_static_handler' => true,
        'document_root' => "/var/www/html/swoole_imooc/thinkphp/public/static",
        'worker_num'=>8
    ]
);
$http_server->on('WorkerStart',function (swoole_server $server,$worker_id){
    // 定义应用目录
    define('APP_PATH', __DIR__ . '/../application/');
    // ThinkPHP 引导文件
    // 加载基础文件
    require __DIR__ . '/../thinkphp/base.php';
    //require __DIR__ . '/../thinkphp/start.php';
});
$http_server->on('request',function ($request,$response)use ($http_server){
    if(!empty($_SERVER)) { unset($_SERVER); }
    if(isset($request->server)){
        foreach ($request->server as $k=>$v){
            $_SERVER[$k] = $v;
        }
    }
    $_GET = [];
    if(isset($request->get)){
        foreach ($request->get as $k=>$v){
            $_GET[$k] = $v;
        }
    }
    $_POST = [];
    if(isset($request->post)){
        foreach ($request->post as $k=>$v){
            $_POST[$k] = $v;
        }
    }

    ob_start();
    // 执行应用并响应
    \think\Container::get('app', [defined('APP_PATH') ? APP_PATH : ''])
        ->run()
        ->send();
    $res = ob_get_contents();
    ob_clean();
    $response->end($res);
    //http_server进程不会注销掉变量，全局变量等等，还会访问上次的方法，所以需要调用close方法,控制台会报错
    //$http_server->close();
});
$http_server->start();