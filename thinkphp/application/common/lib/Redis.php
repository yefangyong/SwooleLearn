<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/7/5
 * Time: 21:41
 */
namespace app\common\lib;
class Redis{
    const PRE = 'y_';

    /**
     * @param $phone
     * @return string
     * 存储redis的key值
     */
    public static function smsKey($phone){
        return self::PRE.$phone;
    }


}