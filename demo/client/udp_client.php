<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/6/22
 * Time: 18:12
 */
$client = new swoole_client(SWOOLE_SOCK_UDP);
if (!$client->connect('127.0.0.1', 9502))
{
    exit("connect failed. Error: {$client->errCode}\n");
}
fwrite(STDOUT,"请输入消息:");
$msg  = fgets(STDIN);
$client->send($msg);
echo $client->recv();
$client->close();