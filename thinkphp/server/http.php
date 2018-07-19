<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/6/30
 * Time: 20:45
 */

class Http
{
    const PORT = 9503;
    CONST HOST = "0.0.0.0";
    private $http = '';

    public function __construct()
    {
        $this->http = new swoole_http_server(self::HOST,self::PORT);
        $this->http->set([
            'enable_static_handler' => true,
            'document_root' => "/var/www/html/swoole_imooc/thinkphp/public/static",
            'worker_num'=>8,
            'task_worker_num'=>100
        ]);
        $this->http->on('workerStart',[$this,'onWorkerStart']);
        $this->http->on('request',[$this,'onRequest']);
        $this->http->on('close',[$this,'onClose']);
        $this->http->on('task',[$this,'onTask']);
        $this->http->on('finish',[$this,'onFinish']);
        $this->http->start();
    }


    /**
     * @param $server
     * @param $worker_id
     * 创建worker进程，引入框架的核心文件
     */
    public function onWorkerStart($server,$worker_id){
        // 定义应用目录
        define('APP_PATH', __DIR__ . '/../application/');
        // ThinkPHP 引导文件
        // 加载基础文件
        //require_once  __DIR__ . '/../thinkphp/base.php';
        //require __DIR__ . '/../thinkphp/base.php';
        require __DIR__ . '/../thinkphp/start.php';
    }

    /**
     * @param $serv
     * @param $task_id
     * @param $src_worker_id
     * @param $data
     * @return string
     * 异步任务
     */
    public function onTask($serv,$task_id,$src_worker_id,  $data){
//        try{
//            $res = \app\common\lib\jhsj\JhSms::Sms($data['phone'],$data['code']);
//            print_r($res);
//        }catch (Exception $e){
//            echo $e->getMessage();
//        }
        //封装成一个类，工厂模式，分发task任务，走不同的逻辑
        $obj = new \app\common\lib\task\Task();
        $method = $data['method'];
        $obj->$method($data['data']);
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


    /**
     * request回调
     * 解决上一次输入的变量还存在的问题例：$_SERVER  =  []
     * @param $request
     * @param $response
     */
    public function onRequest($request,$response){
        //$_POST['http_server'] = $this->http;
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
        $_POST = [];
        if(isset($request->post)){
            foreach ($request->post as $k=>$v){
                $_POST[$k] = $v;
            }
        }
        $_POST['http_server'] = $this->http;
        //把数据先暂存缓冲区，不要输出到终端
        ob_start();
        // 执行应用并响应
        \think\Container::get('app', [defined('APP_PATH') ? APP_PATH : ''])
            ->run()
            ->send();
        $res = ob_get_contents();
        ob_end_clean();
        $response->end($res);
        //http_server进程不会注销掉变量，全局变量等等，还会访问上次的方法，所以需要调用close方法,控制台会报错
        //$this->http->close();
    }

    /**
     * @param $ws
     * @param $fd
     * 关闭连接
     */
    public function onClose($ws,$fd){
        echo "client close:".$fd;
    }
}
new Http();
