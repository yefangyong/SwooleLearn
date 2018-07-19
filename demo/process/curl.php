<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/6/30
 * Time: 11:06
 */
echo "process-start-time:".date("Ymd H:i:s");
$workers = [];
$urls = [
    'https://baidu.com',
    'https://sina.com.cn',
    'https://qq.com',
    'https://baidu.com?search=singwa',
    'https://baidu.com?search=singwa1',
    'https://baidu.com?search=singwa2',
];
//创建多个子进程分别模拟请求URL的内容,同步进行
for($i = 0; $i < 6; $i++){
    $process = new swoole_process(function(swoole_process $worker) use($i,$urls){
       $content = curlData($urls[$i]);
       //将内容写入管道
        $worker->write($content.PHP_EOL);
    },true);

    $pid = $process->start();
    $workers[$pid] = $process;
}

//获取管道的内容
foreach ($workers as $process){
    echo $process->read();
}

/**
 * @param $url
 * @return string
 * 模拟请求URL的内容
 */
function curlData($url){
    sleep(2);
    return $url . 'success'.PHP_EOL;
}
echo "process-end-time:".date("Ymd H:i:s");