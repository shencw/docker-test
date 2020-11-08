<?php
namespace Wei\Io\Blocking;

class Worker
{
    public $onReceive = null;
    public $onConnect = null;
    public $socket    = null;
    public $pool      = [];

    public function __construct($socket_address)
    {
        $this->socket = stream_socket_server($socket_address);

//        stream_set_blocking($this->socket,false);
    }

    public function accept()
    {
        while (true) {
            debug("accept start");

            if (empty($this->pool[(int)$this->socket])) {
                // 只能接受刚创建的服务端socket - 主要是用于建立通道使用
                $client = stream_socket_accept($this->socket);
                $this->pool[(int)$this->socket] = ['server' => $this->socket, 'client' => $client];
            } else {
                $client = $this->pool[(int)$this->socket]['client'];
            }

            $data = fread($client, 65535);

            if (is_callable($this->onReceive)) {
                ($this->onReceive)($this, $client, $data);
            }
            debug("accept end");

            if ($data == 'over' || empty($data)) {
                unset($this->pool[(int)$this->socket]);
                fclose($client);
            }

        }
    }

    // 启动服务的
    public function start()
    {
        $this->accept();
    }
}
