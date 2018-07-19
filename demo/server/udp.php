<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/6/22
 * Time: 18:10
 */
$udp_server  = new swoole_server("127.0.0.1", 9502, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);
//监听数据接收事件
$udp_server->on('Packet', function ($serv, $data, $clientInfo) {
    echo "client:".print_r($clientInfo);
    $serv->sendto($clientInfo['address'], $clientInfo['port'], "Server ".$data);
    var_dump($clientInfo);
});

//启动服务器
$udp_server->start();
