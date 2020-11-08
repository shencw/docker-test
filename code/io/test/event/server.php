<?php
require __DIR__ . '/../../vendor/autoload.php';

use Wei\Io\AsyncModel\Worker;

$host   = "tcp://127.0.0.1:9001";
$server = new Worker($host);

$server->onReceive = function ($socket, $client, $data) {
    debug("接收到客户端消息：" . $data);
    send($client, "hello world client");
    sleep(1);
};

debug($host);
$server->start();
