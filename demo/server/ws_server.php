<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/28
 * Time: 17:19
 */
$server = new swoole_websocket_server("0.0.0.0", 9503);

$server->set(
    [
        'enable_static_handler' => true,
        'document_root' => "/var/www/html/swoole_imooc/demo/data",
    ]
);
$server->on('open', 'onOpen');

//客户端链接时
function onOpen($server,$request){
    echo "server: handshake success with fd{$request->fd}\n";
}

//ws收到信息时候
$server->on('message', function (swoole_websocket_server $server, $frame) {
    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    $server->push($frame->fd, "this is server");
});

//客户端关闭连接时
$server->on('close', function ($ser, $fd) {
    //
    echo "client {$fd} closed\n";
});

$server->start();