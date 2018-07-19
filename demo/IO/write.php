<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/6/29
 * Time: 14:41
 */
$data = array(
    'header'=>'yfyjsz',
    'name'=>'qwert',
    'time'=>time()
);
Swoole\Async::writeFile(__DIR__."/1.txt", json_encode($data).PHP_EOL, function ($filename){}, FILE_APPEND);