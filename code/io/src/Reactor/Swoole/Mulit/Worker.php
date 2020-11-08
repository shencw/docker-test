<?php
namespace ShineYork\Io\Reactor\Swoole\Mulit;

use Swoole\Event;
// 这是等会自个要写的服务
class Worker
{

    // 自定义服务的事件注册函数，
    // 这三个是闭包函数
    public $onReceive = null;
    public $onConnect = null;
    public $onClose = null;

    // 连接
    public $socket = null;
    // 创建多个子进程 -》 是不是可以自定义
    protected $config = [
        'worker_num' => 4
    ];
    protected $socket_address = null;
    public function __construct($socket_address)
    {
        $this->socket_address = $socket_address;
    }
    // 需要处理事情
    public function accept()
    {
        $this->socket = stream_socket_server($this->socket_address);
        Event::add($this->socket, $this->createSocket());
    }
    // 启动服务的
    public function start()
    {
        debug('start 开始');
        $this->fork();
        debug('start 结束');
    }
    public function fork()
    {
        for ($i=0; $i < $this->config['worker_num']; $i++) {
            $son11 = pcntl_fork();
            if ($son11 > 0) {
                // 父进程空间
            } else if($son11 < 0){
                // 进程创建失败的时候
            } else {
                debug(posix_getpid()); // 阻塞
                $this->accept();
                // 处理接收请求
                // exit;
            }
        }
        for ($i=0; $i < $this->config['worker_num']; $i++) {
            $status = 0;
            $son = pcntl_wait($status);
            debug($son); // 阻塞
        }
    }

    public function createSocket()
    {
        return function($socket){
            debug(posix_getpid());
            // $client 是不是资源 socket
            $client = stream_socket_accept($this->socket);
            // is_callable判断一个参数是不是闭包
            if (is_callable($this->onConnect)) {
                // 执行函数
                ($this->onConnect)($this, $client);
            }
            // 默认就是循环操作
            Event::add($client, $this->sendClient());
        };
    }

    public function sendClient()
    {
        return function($socket){
            //从连接当中读取客户端的内容
            $buffer=fread($socket,1024);
            //如果数据为空，或者为false,不是资源类型
            if(empty($buffer)){
                if(feof($socket) || !is_resource($socket)){
                    //触发关闭事件
                    swoole_event_del($socket);
                    fclose($socket);
                }
            }
            //正常读取到数据,触发消息接收事件,响应内容
            if(!empty($buffer) && is_callable($this->onReceive)){
                ($this->onReceive)($this, $socket, $buffer);
            }
        };
    }

}
