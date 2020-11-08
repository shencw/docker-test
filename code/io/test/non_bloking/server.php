<?php
require __DIR__ . '/../../vendor/autoload.php';

use Wei\Io\NonBlocking\Worker;

$host   = "tcp://127.0.0.1:9001";
$server = new Worker($host);

// 接收和处理信息
$server->onReceive = function ($socket, $client, $data) {
    debug("接收到客户端消息：" . $data);
    send($client, "hello world client \n");
    sleep(2);
};

$server->start();
