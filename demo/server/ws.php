<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/28
 * Time: 18:37
 */
class Ws {
    const PORT =9503;
    const HOST = '0.0.0.0';
    public $ws = null;
    public function __construct()
    {
        $this->ws = new swoole_websocket_server(self::HOST,self::PORT);
        //php回调函数写法，参考文档https://wiki.swoole.com/wiki/page/458.html
        //注意这个$this是$this->ws对象
        $this->ws->on('open',array($this,'onOpen'));
        $this->ws->on('message',array($this,'onMessage'));
        $this->ws->set([
            'worker_num'=>2,
            'task_worker_num'=>2
        ]);
        $this->ws->on("task", [$this, 'onTask']);
        $this->ws->on("finish", [$this, 'onFinish']);
        $this->ws->on('close',array($this,'onClose'));
        $this->ws->start();
    }

    /**
     * @param $ws
     * @param $request
     * 监听客户端链接事件
     */
    public function onOpen($ws,$request){
        print_r('客户端链接：'.$request->fd);
        if($request->fd == 1) {
            swoole_timer_tick(2000, function ($timer_id){
                echo "我是异步定时器函数swoole_timer_tick,我的timer_id:{$timer_id}\n";
            });
        }
    }

    /**
     * @param $ws
     * @param $request
     * 监听收到信息事件
     */
    public function onMessage($ws,$request){
       //TODO 10s
        $data = array(
            'task'=>1,
            'fd'=>$request->fd
        );
        //广播，发送邮件比较耗时的任务
        //$ws->task($data);
        //异步任务
        swoole_timer_after(5000,  function ()use($ws,$request){
            echo "我是5s之后在执行的";
            $ws->push($request->fd,'我是5s之后在执行的函数');
        });
        echo "receive from {$request->fd}:{$request->data},opcode:{$request->opcode},fin:{$request->finish}\n";
        $ws->push($request->fd, "this is server");
    }

    public function onTask($serv,$task_id,$src_worker_id,  $data){
        sleep(10);
        print_r($data);
        return " i am a task";
    }

    public function onFinish( $serv,  $task_id,  $data){
        echo "taskId:{$task_id}\n";
        echo $data;

    }

    /**
     * @param $ws
     * @param $request
     * 监听到客户端关闭的事件
     */
    public function onClose($ws,$fd){
        echo "client close:".$fd;
    }
}

new Ws();