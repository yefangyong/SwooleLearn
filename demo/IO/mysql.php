<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/6/29
 * Time: 15:04
 */
class AsyncMysql{
    /**
     * @var array
     * 数据库的配置
     */
    private $dbConfig = [];

    public $dbSource = '';

    /**
     * AsyncMysql constructor.
     * 构造函数
     */
    public function __construct()
    {
        $this->dbConfig = array(
            'host' => '127.0.0.1',
            'port' => 3306,
            'user' => 'root',
            'password' => '8912878yfy',
            'database' => 'test',
            'charset' => 'utf8',
        );
        $this->dbSource = new Swoole\Mysql;
    }

    /**
     * @param $id
     * @param $username
     * 执行主函数
     */
    public function execute($id,$username){
        $this->dbSource->connect($this->dbConfig,function ($db,$result)use($id,$username){
            if($result === false) {
                var_dump($db->connect_error);
                // todo
                die;
            }
            $sql = "select * from user ";
            //$sql = "show tables";
            $db->query($sql,function ($db,$result){
                if($result === false){
                    var_dump($db->error);
                }elseif($result === true){ //add update
                    var_dump($db->affected_rows);
                }else{
                    print_r($result);
                }
                $db->close();
            });
            return true;
        });
    }
}
$obj = new AsyncMysql();
$flag = $obj->execute(1,'yfyjsz');
echo 'start';
var_dump($flag);
