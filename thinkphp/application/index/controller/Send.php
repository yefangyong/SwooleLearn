<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/7/4
 * Time: 17:30
 */

namespace app\index\controller;


use app\common\lib\Redis;
use app\common\lib\Util;
class Send
{
    public function index(){
        $phone = $_GET['phone'];
        if(empty($phone)){
           return  Util::show(config('code.error'),'手机号码不得为空');
        }else{
            $code = rand(1000,9999);
            //抽离封装起来
            $taskData = array(
                'method'=>'sendSms',
                'data'=>array(
                    'phone'=>$phone,
                    'code'=>$code
                )
            );
            //异步发送验证码，异步任务,使用场景发送邮件，短信等等
            $_POST['ws_server']->task($taskData);
            return Util::show(config('code.success'),'验证码发送成功');
            //同步发送验证码
//            try{
//                $res = JhSms::Sms($phone,$code);
//            }catch (Exception $e){
//                return Util::show(config('code.error','阿里大鱼内部错误'));
//            }
//            if($res){
            //swoole中协程redis
//                $redis = new \Swoole\Coroutine\Redis();
//                $redis->connect(config('redis.host'),config('redis.port'));
//                $redis->set(Redis::smsKey($phone),$code,config('redis.dead_time'));
//                return Util::show(config('code.success'),'验证码发送成功');
//            }else{
//                return Util::show(config('code.error'),'验证码发送失败');
//            }
        }
    }
}