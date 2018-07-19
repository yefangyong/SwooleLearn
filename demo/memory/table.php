<?php
/**
 * Created by PhpStorm.
 * User: yefy
 * Date: 2018/6/30
 * Time: 12:27
 */
$table  = new swoole_table(1024);
$table->column('id', $table::TYPE_INT, 4);
$table->column('name', $table::TYPE_STRING, 64);
$table->column('num', $table::TYPE_FLOAT);
$table->create();
$table->set('yfyjsz', ['id' => 1, 'name' => 'test1', 'num' => 20]);
$table['singwa'] = ['id'=>2,'name'=>'singwa','num'=>123];
$table->incr('singwa','num',2);
print_r($table['singwa']);
print_r($table->get('yfyjsz'));