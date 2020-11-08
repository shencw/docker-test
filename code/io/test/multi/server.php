<?php
require __DIR__ . '/../../vendor/autoload.php';

use Wei\Io\Multi\Worker;

$host   = "tcp://127.0.0.1:9001";
$server = new Worker($host);

// 接收和处理信息
$server->onReceive = function ($socket, $client, $data) {
    debug("接收到客户端消息：" . $data);
    send($client, "hello world client");
    sleep(3);
};

echo $host . "\n";
$server->start();
