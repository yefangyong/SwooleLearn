<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/6/30
 * Time: 20:45
 */

class Ws
{
    const PORT = 9503;
    CONST HOST = "0.0.0.0";
    private $ws = '';
    CONST CHAT_PORT = '9504';

    public function __construct()
    {
        //ws server http也可以使用，即ws是http的父类
        $this->ws = new swoole_websocket_server(self::HOST,self::PORT);

        //同时监听新的端口
        $this->ws->listen(self::HOST,self::CHAT_PORT,SWOOLE_SOCK_TCP);

        $this->ws->set([
            'enable_static_handler' => true,
            'document_root' => "/var/www/html/swoole_imooc/thinkphp/public/static",
            'worker_num'=>8,
            'task_worker_num'=>100
        ]);
        $this->ws->on('workerStart',[$this,'onWorkerStart']);
        $this->ws->on('message',[$this,'onMessage']);
        $this->ws->on('open',[$this,'onOpen']);
        $this->ws->on('start',[$this,'onStart']);
        $this->ws->on('request',[$this,'onRequest']);
        $this->ws->on('close',[$this,'onClose']);
        $this->ws->on('task',[$this,'onTask']);
        $this->ws->on('finish',[$this,'onFinish']);
        $this->ws->start();
    }

    /**
     * @param $ws
     * @param $request
     * 监听客户端链接事件
     */
    public function onOpen($ws,$request){
        \app\common\lib\redis\Predis::get_instance()->sAdd(config('redis.live_game_key'),$request->fd);
        print_r('客户端链接：'.$request->fd);
    }

    /**
     * @param $server
     * 主进程启动的方法
     */
    public function onStart($server){
        //设置主进程的名字，用于平滑重启
        swoole_set_process_name("live_master");
    }
    /**
     * @param $ws
     * @param $request
     * 监听收到信息事件
     */
    public function onMessage($ws,$request){
        echo "receive from {$request->fd}:{$request->data},opcode:{$request->opcode},fin:{$request->finish}\n";
        $ws->push($request->fd, "this is server");
    }


    /**
     * @param $server
     * @param $worker_id
     * 创建worker进程，引入框架的核心文件
     */
    public function onWorkerStart($server,$worker_id){
        // 定义应用目录
        define('APP_PATH', __DIR__ . '/../application/');
        require __DIR__ . '/../thinkphp/start.php';

        //先获取redis里面有序集合的值，如果有值先清空
        $clients = \app\common\lib\redis\Predis::get_instance()->sMembers(config('redis.live_game_key'));
        if(!empty($clients)){
            foreach ($clients as $v){
                \app\common\lib\redis\Predis::get_instance()->sRem(config('redis.live_game_key'),$v);
            }
        }
    }

    /**
     * @param $serv
     * @param $task_id
     * @param $src_worker_id
     * @param $data
     * @return string
     * 异步任务
     */
    public function onTask($serv,$task_id,$src_worker_id,$data){
//        try{
//            $res = \app\common\lib\jhsj\JhSms::Sms($data['phone'],$data['code']);
//            print_r($res);
//        }catch (Exception $e){
//            echo $e->getMessage();
//        }
        //封装成一个类，工厂模式，分发task任务，走不同的逻辑
        $obj = new \app\common\lib\task\Task();
        $method = $data['method'];
        $obj->$method($data['data'],$serv);
        return "on task finish"; //返回给worker进程
    }

    /**
     * @param $serv
     * @param $task_id
     * @param $data
     * 接收异步任务的返回值，结束异步任务
     */
    public function onFinish( $serv,  $task_id,  $data){
        echo "taskId:{$task_id}\n";
        echo $data;
    }

    public function writeLog(){
        $data = array_merge(
            array('date'=>date("Y-m-d H:i:s"),time()),$_GET,$_POST,$_SERVER
        );
        $log = "";
        foreach ($data as $k=>$v){
            $log = $log.$k.":".$v." ";
        }
        swoole_async_writefile(APP_PATH."../runtime/log/".date("Ym",time())."/".date("d",time()).'_access.log', $log.PHP_EOL,function ($filename){},FILE_APPEND);
    }


    /**
     * request回调
     * 解决上一次输入的变量还存在的问题例：$_SERVER  =  []
     * @param $request
     * @param $response
     */
    public function onRequest($request,$response){
        if($request->server['request_uri'] == '/favicon.ico'){
            $response->status(404);
            $response->end();
            return ;
        }
        //$_POST['ws_server'] = $this->ws;
        $_SERVER = [];
        if(!empty($_SERVER)) { unset($_SERVER); }
        if(isset($request->server)){
            foreach ($request->server as $k=>$v){
                $_SERVER[$k] = $v;
            }
        }
        $_GET = [];
        if(isset($request->get)){
            foreach ($request->get as $k=>$v){
                $_GET[$k] = $v;
            }
        }
        $_FILES = [];
        if(isset($request->files)){
            foreach ($request->files as $k=>$v){
                $_FILES[$k] = $v;
            }
        }
        $_POST = [];
        if(isset($request->post)){
            foreach ($request->post as $k=>$v){
                $_POST[$k] = $v;
            }
        }
        //写日志到文件
        $this->writeLog();
        $_POST['ws_server'] = $this->ws;
        //把数据先暂存缓冲区，不要输出到终端
        ob_start();
        // 执行应用并响应
        try {
            think\Container::get('app', [APP_PATH])
                ->run()
                ->send();
        }catch (\Exception $e) {
            // todo
        }

        $res = ob_get_contents();
        ob_end_clean();
        $response->end($res);
    }

    /**
     * @param $ws
     * @param $fd
     * 关闭连接
     */
    public function onClose($ws,$fd){
        \app\common\lib\redis\Predis::get_instance()->sRem(config('redis.live_game_key'),$fd);
        echo "client close:".$fd;
    }
}
new Ws();
