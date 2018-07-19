<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/7/16
 * Time: 18:02
 */

namespace app\index\controller;


use app\common\lib\Util;

class Chat
{
    public function index(){
        if(empty($_POST) || empty($_POST['game_id']) || empty($_POST['content'])){
            return Util::show(config('code.error'),'参数错误');
        }
        $data = array(
            'user'=>'用户'.rand(0,200),
            'content'=>$_POST['content']
        );
        //可以使用connection也可以使用redis存储，推荐使用connection,因为链接redis消耗资源
       foreach ($_POST['ws_server']->ports[1]->connections as $fd){
           $_POST['ws_server']->push($fd,json_encode($data));
       }
    }
}