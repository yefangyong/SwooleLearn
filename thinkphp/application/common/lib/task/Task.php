<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/7/6
 * Time: 15:09
 */

namespace app\common\lib\task;


use app\common\lib\Redis;
use app\common\lib\redis\Predis;
use think\Exception;

class Task
{
    /**
     * @param $data
     * @return bool
     * 异步发送验证码
     */
    public function sendSms($data){
        try{
            $res = \app\common\lib\jhsj\JhSms::Sms($data['phone'],$data['code']);
        }catch (Exception $e){
            echo $e->getMessage();
        }
        //如果发送成功记录到redis中，使用同步的方式
        if($res['error_code'] == 0){
            Predis::get_instance()->set(Redis::smsKey($data['phone']),$data['code'],config('redis.dead_time'));
        }else{
            return false;
        }
        return true;
    }

    /**
     * @param $data
     * @param $ser
     * 异步发送数据到客户端
     */
    public function pushData($data,$serv){
        $clients = Predis::get_instance()->sMembers(config('redis.live_game_key'));
        if(!empty($clients)){
            foreach ($clients as $fd) {
                $serv->push($fd,$data);
            }
        }
    }
}