<?php
namespace app\admin\controller;


use app\common\lib\Util;

class Image
{
    public function index()
    {
         $file = request()->file('file');
         $info = $file->move('../public/static/upload');
         if(!empty($info)){
             Util::show(config('code.success'),'ok',array('image'=>config('image.imgUrl').$info->getSaveName()));
         }else{
             Util::show(config('code.error'),'error');
         }
    }

    public function test(){
        echo time();
    }
}
