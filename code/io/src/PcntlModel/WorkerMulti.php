<?php

namespace Wei\Io\PcntlModel;


class WorkerMulti
{
    public $onReceive = null;
    public $onConnect = null;

    public $socket = null;

    protected $pool = [];

    // 创建多个子进程 -》 是不是可以自定义
    protected $config = [
        'worker_num' => 2
    ];

    public function __construct($socket_address)
    {
        $this->socket = stream_socket_server($socket_address);
//        stream_set_blocking($this->socket, 0);
    }

    // 启动服务的
    public function start()
    {
        debug("开始");
        $this->fork();
        debug("结束");
    }

    // 创建多个子进程，并且让子进程可以去运行accept函数
    public function fork()
    {
        for ($i = 0; $i < $this->config['worker_num']; $i++) {
            $son11 = pcntl_fork();
            if ($son11 > 0) {
                // 父进程空间
            } else if ($son11 < 0) {
                // 进程创建失败的时候
            } else {
                $this->debug("fork子进程：" . posix_getpid()); // 阻塞
                $this->accept();
                // 处理接收请求
                exit;
            }
        }
        for ($i = 0; $i < $this->config['worker_num']; $i++) {echo 111;
            $status = 0;
            $son    = pcntl_wait($status);
            $this->debug($son); // 阻塞
        }
    }

    // 需要处理事情
    public function accept()
    {
        // 接收连接和处理使用
        while (true) {
            $pid = posix_getpid();

            $this->debug("$pid: accept start");

            if (empty($this->pool[(int)$this->socket])) {
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

            $this->debug("accept end");
        }
    }


    public function sendMessage($client)
    {
        if (feof($client)) {
            debug("Bye-Bye:" . (int)$client);
            fclose($client);
            unset($this->pool[(int)$client]);
            return null;
        }
        $data = fread($client, 65535);
        if (is_callable($this->onReceive)) {
            ($this->onReceive)($this, $client, $data);
        }
    }

    public function set($value)
    {
        // ..
    }

    public function debug($data, $flag = false)
    {
        if ($flag) {
            var_dump($data);
        } else {
            echo "==== >>>> : " . $data . " \n";
        }
    }



}
