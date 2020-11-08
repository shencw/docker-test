<?php
require __DIR__ . '/../../vendor/autoload.php';

$client = stream_socket_client("tcp://127.0.0.1:9001");

while (true) {
    fwrite($client, "hello world");

    var_dump(fread($client, 65535));
    sleep(2);
}