<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/6/29
 * Time: 16:30
 */
$redis_client = new swoole_redis;
$redis_client->connect('127.0.0.1',6379,function (swoole_redis $redis_client,$result){
   var_dump($result.PHP_EOL);
   echo "client_connect ok";
   $redis_client->set('yfyjsz',time(),function (swoole_redis $redis_client,$result){
       if($result == 'OK'){
           echo "yfyjsz设置成功";
       }else{
           echo "yfyjsz设置失败";
       }
   });
    $redis_client->set('yfyjsz1',time(),function (swoole_redis $redis_client,$result){
        if($result == 'OK'){
            echo "yfyjsz设置成功";
        }else{
            echo "yfyjsz设置失败";
        }
    });
    $redis_client->get('yfyjsz',function (swoole_redis $redis_client,$result){
        var_dump($result);
    });
    //模糊匹配
    $redis_client->keys('*fy*',function (swoole_redis $redis_client,$result){
        var_dump($result);
    });
   echo "start\n";
});