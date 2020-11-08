<?php
require __DIR__.'/../../vendor/autoload.php';
use Wei\Io\PcntlModel\Worker;
$host = "tcp://127.0.0.1:9001";
$server = new Worker($host);
//$server->onConnect = function($socket, $client){
//    echo "有一个连接进来了\n";
//};
// 接收和处理信息
$server->onReceive = function($socket, $client, $data){
    echo "给连接发送信息\n";
    send($client, "hello world client \n");
};
$server->start();
