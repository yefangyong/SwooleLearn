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
        'document_root' => "/var/www/html/swoole_imooc/demo/data",
        'worker_num'=>8
    ]
);
$http_server->on('request',function ($request,$response){
    $data = array(
            'date:' => date("Ymd H:i:s"),
            'get:' => $request->get,
            'post:' => $request->post,
            'header:' => $request->header,
    );
    /**
     * 异步写文件，调用此函数后会立即返回。当写入完成时会自动回调指定的callback函数
     */
    //swoole_async_writefile('./access.log', json_encode($data).PHP_EOL,function ($filename){},FILE_APPEND);
    //$response->cookie('yfyjsz','imooc',time()+1800);
    $response->end("sss".json_encode($request->get));
});
$http_server->start();