<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/7/5
 * Time: 20:57
 */

namespace app\common\lib;


class Util
{
    /**
     * @param int $status
     * @param string $message
     * @param array $data
     * @return mixed
     * 返回json数据方法
     */
    public static function show($status = 1,$message = '',$data = array()){
        echo json_encode(array('status'=>$status,'message'=>$message,'data'=>$data));
    }
}