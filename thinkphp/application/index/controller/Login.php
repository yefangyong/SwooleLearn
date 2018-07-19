<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/7/5
 * Time: 22:43
 */

namespace app\index\controller;


use app\common\lib\Redis;
use app\common\lib\redis\Predis;
use app\common\lib\Util;
use think\Exception;

class Login
{
    /**
     * @return mixed
     * 登录接口
     */
    public function index(){
        $phone = $_GET['phone'];
        $code = $_GET['code'];
        if(empty($phone) || !$code){
            return  Util::show(config('code.error'),'参数错误');
        }
        $redisCode = Predis::get_instance()->get(Redis::smsKey($phone));
        if($redisCode == $code){
            $data = array(
                'user'=>$phone,
                'src'=>md5($phone),
                'time'=>time()
            );
            try{
                Predis::get_instance()->set($phone,$data,1000);
                return Util::show(config('code.success'),'login success',$data);
            }catch (Exception $e){
                return Util::show(config('code.error'),'login error');
            }
        }else{
            return Util::show(config('code.error'),'login_error');
        }
    }
}