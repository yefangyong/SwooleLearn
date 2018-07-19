<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/6/30
 * Time: 12:52
 */
$http = new swoole_http_server('0.0.0.0',9503);
$http->on('request',function ($request,$response){
    $redis = new Swoole\Coroutine\Redis();
    $redis->connect('127.0.0.1', 6379);
    $val = $redis->get($request->get['a']);
    $response->header('Content-type','text/plain');
    $response->end($val);
});
$http->start();
