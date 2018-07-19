<?php
namespace app\index\controller;


use app\common\lib\jhsj\JhSms;

class Index
{
    public function index()
    {
       return '';
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello' . $name;
    }

    public function singwa() {
        echo time();
    }



    public function sms(){
        if(JhSms::Sms(13053112897,"1245")){
            echo "success";
        }else{
            echo "error";
        }
    }


}
