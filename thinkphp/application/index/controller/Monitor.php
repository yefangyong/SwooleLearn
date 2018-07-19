<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/7/17
 * Time: 17:38
 */

namespace app\index\controller;


use app\common\lib\jhsj\JhSms;

class Monitor
{
    const PORT = 9503;

    public function swoole(){
        $shell = "netstat -ntul|grep ".self::PORT."| grep LISTEN | wc -l";
        $result = shell_exec($shell);
        if($result == 0){
            //报警，发送短信或者邮箱给负责人
            //重启
            $restart_shell = "nohup php /var/www/html/swoole_imooc/thinkphp/server/ws.php > /var/www/html/swoole_imooc/thinkphp/server/t.txt &";
            $time = date("Y-m-d H:i:s",time());
            $date = date("Ymd",time());
            $file = "find /var/log/swoole -name {$date}.log | wc -l";
            if($file == 0){
                shell_exec("touch /var/log/swoole/{$date}.log");
            }
            $log ="echo {$time}.'|restart' >> /var/log/swoole/{$date}.log";
            shell_exec($log);
            shell_exec($restart_shell);
        }else{
            echo date("Y-m-d H:i:s",time()).'success';
        }
    }
}
//$monitor = new Monitor();
//$monitor->swoole();
swoole_timer_tick(2000,function ($time_id){
    $monitor = new Monitor();
    $monitor->swoole();
});
