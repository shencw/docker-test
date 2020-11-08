<?php
require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

$client = stream_socket_client("tcp://127.0.0.1:9001");

$i = 1;
stream_set_blocking($client,false);

do {
//    $i++;
    fwrite($client, "hello world");
    debug("client");
    debug("服务端响应：" . fread($client, 65535));
    sleep(2);
} while ($i<3);

fwrite($client, "over");

fclose($client);