<?php

namespace Wei\Io\Signal;

class Worker
{
    public $onReceive = null;
    public $onConnect = null;
    public $socket    = null;
    public $pool      = null;


    public function __construct($socket_address)
    {
        $this->socket = stream_socket_server($socket_address);

        stream_set_blocking($this->socket, 0);

        echo $socket_address . "\n";
    }


    public function accept()
    {
        while (true) {
            debug("accept start");

//            $client = stream_socket_accept($this->socket);

            if ($client = @stream_socket_accept($this->socket, empty($this->pool) ? -1 : 0, $peer)) {
                debug($peer . ' connected');
                send($client, "hello world {$peer}" . PHP_EOL);
                $this->pool[$peer] = $client;
            }

            $read = $this->pool;

            stream_select($read, $w, $e, 1);

            foreach ($read as $k => $socket) {
                if (feof($socket)) {
                    debug("Bye-Bye:" . $k);
                    fclose($socket);
                    unset($this->pool[$k]);
                    continue;
                }
                pcntl_signal(SIGIO, $this->sigHander($socket));
                posix_kill(posix_getpid(), SIGIO);
                pcntl_signal_dispatch();
            }

            debug("accept end");
        }
    }


    public function sigHander($client)
    {
        return function ($sig) use ($client) {
            $data = fread($client, 65535);
            if (is_callable($this->onReceive)) {
                ($this->onReceive)($this, $client, $data);
            }
        };
    }


    public function start()
    {
        $this->accept();
    }
}
