<?php
namespace app\admin\controller;


use app\common\lib\redis\Predis;
use app\common\lib\Util;

class Live
{
    public function __construct()
    {

    }

    public function push(){
        if(empty($_GET)) {
            return Util::show(config('code.error'), 'error');
        }
        $teams = [
            1 => [
                'name' => '马刺',
                'logo' => '/live/imgs/team1.png',
            ],
            4 => [
                'name' => '火箭',
                'logo' => '/live/imgs/team2.png',
            ],
        ];

        $data = [
            'type' => intval($_GET['type']),
            'title' => !empty($teams[$_GET['team_id']]['name']) ?$teams[$_GET['team_id']]['name'] : '直播员',
            'logo' => !empty($teams[$_GET['team_id']]['logo']) ?$teams[$_GET['team_id']]['logo'] : '',
            'content' => !empty($_GET['content']) ? $_GET['content'] : '',
            'image' => !empty($_GET['image']) ? $_GET['image'] : '',
        ];

        $taskData = array(
            'method'=>'pushData',
            'data'=>json_encode($data)
        );
        $_POST['ws_server']->task($taskData);
        return Util::show(config('code.success'),'发布成功');
   }
}
