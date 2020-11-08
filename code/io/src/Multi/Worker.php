<?php

namespace Wei\Io\Multi;

class Worker
{
    public $onReceive = null;
    public $onConnect = null;

    protected $pool = [];

    public $socket = null;

    public function __construct($socket_address)
    {
        $this->socket = stream_socket_server($socket_address);

        stream_set_blocking($this->socket, 0);
    }

    // 需要处理事情
    public function accept()
    {
        // 接收连接和处理使用
        while (true) {
            if ($c = @stream_socket_accept($this->socket, empty($this->pool) ? -1 : 0, $peer)) {
                debug($peer . ' connected');
                send($c, "hello world {$peer}" . PHP_EOL);
                $this->pool[$peer] = $c;
            }

            $read = $this->pool;
            // 有缺陷-连接数数量
            stream_select($read, $w, $e, 1);

            if (empty($read)) {
                debug("等待新的链接进入...");
                sleep(1);
            }

            foreach ($read as $k => $socket) {
                $this->sendMessage($socket, $k);
            }
        }
    }


    public function sendMessage($client, $peer)
    {
        if (feof($client)) {
            debug("Bye-Bye:" . $peer);
            fclose($client);
            unset($this->pool[$peer]);
            return null;
        }
        $data = fread($client, 65535);
        if (is_callable($this->onReceive)) {
            ($this->onReceive)($this, $client, $data);
        }
    }


    // 启动服务的
    public function start()
    {
        $this->accept();
    }
}
