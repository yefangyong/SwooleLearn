<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/7/5
 * Time: 22:32
 */

namespace app\common\lib\redis;


use think\Exception;

class Predis
{
    public $redis = null;
    private static $_instance = null;

    /**
     * Predis constructor.
     * @throws Exception
     * 构造方法
     */
    private function __construct()
    {
        $this->redis = new \redis();
        $result = $this->redis->connect(config('redis.host'),config('redis.port'),config('redis.timeOut'));
        if($result == false){
            throw new Exception("redis connect error");
        }
    }

    /**
     * @return Predis|null
     *
     */
    public static function get_instance(){
        if(self::$_instance){
            return self::$_instance;
        }else{
            self::$_instance = new self();
            return self::$_instance;
        }
    }


    /**
     * @param $key
     * @param $value
     * @param int $time
     * @return bool|null
     * 设置缓存
     */
    public function set($key,$value,$time = 0){
        if(empty($key)){
            return null;
        }
        if(is_array($value)){
            $value = json_encode($value);
        }
        if(!$time){
            return $this->redis->set($key,$value);
        }else{
            return $this->redis->setex($key,$time,$value);
        }
    }

    /**
     * @param $key
     * @return string
     * 获取缓存
     */
    public function get($key){
        if(empty($key)){
            return '';
        }else{
            return $this->redis->get($key);
        }
    }

//    /**
//     * @param $key
//     * @param $value
//     * @return int
//     * redis有序集合添加
//     */
//    public function sadd($key,$value){
//        return $this->redis->sAdd($key,$value);
//    }
//
//    /**
//     * @param $key
//     * @param $value
//     * @return int
//     * 删除redis有序集合中的值
//     */
//    public function sdel($key,$value){
//        return $this->redis->sRem($key,$value);
//    }


    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * 重载方法，当调用类中的方法不存在的时候运行
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        return $this->redis->$name($arguments[0],$arguments[1]);
    }

    /**
     * @param $key
     * @return array
     * 获取redis里面的集合
     */
    public function sMembers($key){
        return $this->redis->sMembers($key);
    }
}