<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/6/29
 * Time: 17:46
 */
$process = new swoole_process(function (swoole_process $pro){
   $pro->exec("/home/work/study/soft/php/bin/php",[__DIR__.'/'.'../server/http_server.php']);
},false);
$pid = $process->start();
echo $pid.PHP_EOL;
swoole_process::wait();