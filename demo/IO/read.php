<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/6/29
 * Time: 14:31
 */
$result = swoole_async_readfile(__DIR__."/1.txt", function($filename, $content) {
    echo "filename is {$filename}".PHP_EOL;
    echo "content is {$content}".PHP_EOL;
});
var_dump($result);
echo "start";