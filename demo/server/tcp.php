<?php
//创建Server对象，监听 127.0.0.1:9501端口
$serv = new swoole_server("127.0.0.1", 9501);
//swoole_server->set函数用于设置swoole_server运行时的各项参数
$serv->set([
    'worker_num' => 6 , // worker进程数，cpu 1-4倍
    'max_request' => 10000,
]);
/**
 * 监听连接进入事件
 * $fd 客户端连接的唯一标示
 * $reactor_id 线程id
 */
$serv->on('connect', function ($serv, $fd, $reactor_id) {
    echo "Client: {$reactor_id} - {$fd}-Connect.\n";
});
/**
 * 监听数据接收事件
 * $reactor_id = $from_id
 */
$serv->on('receive', function ($serv, $fd, $reactor_id, $data) {
    echo "client:".$data;
    $serv->send($fd, "Server: {$reactor_id} - {$fd}".$data);
});
//监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

//启动服务器
$serv->start();