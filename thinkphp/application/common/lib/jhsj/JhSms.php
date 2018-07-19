<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/7/4
 * Time: 16:31
 */

namespace app\common\lib\jhsj;

class JhSms
{
    public static function Sms($phone,$code){
        $tpl_id = config('jhsj.tpl_id');
        $code = urlencode("#code#=".$code);
        $key = config('jhsj.key');
        $url = 'http://v.juhe.cn/sms/send?mobile='.$phone.'&tpl_id='.$tpl_id.'&tpl_value='.$code.'&key='.$key;
        $res = self::curl_get($url);
        $res = json_decode($res,true);
        return $res;
    }

    /**
     * @param $url
     * @param int $httpCode
     * @return mixed
     * 获取网咯资源
     */
    private static function curl_get($url,&$httpCode = 0) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,10);
        $file_contents = curl_exec($curl);
        $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $file_contents;
    }
}