<?php
include __DIR__ . '/../../vendor/autoload.php';


// 是建立连接
$client = stream_socket_client("tcp://127.0.0.1:9001");

stream_set_blocking($client, 0);

while (true) {
    fwrite($client, "hello world");
    debug("服务端响应:" . fread($client, 65535));
    sleep(2);
}
