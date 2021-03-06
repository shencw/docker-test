<?php
namespace SwoStar\Server;

use SwoStar\Supper\Inotify;
use Swoole\Server as SwooleServer;
use SwoStar\Foundation\Application;

/**
 * 所有服务的父类， 写一写公共的操作
 */
abstract class Server
{

    /**
     * @var \Swoole\Http\Server
     */
    protected $swooleServer;


    /**
     * @var Application
     */
    protected $app ;

    protected $inotify = null;

    protected $port = 9001;

    protected $host = "127.0.0.1";

    protected $watchFile = false;

    protected $config = [
        'task_worker_num' => 0,
    ];

    /**
     * 用于记录pid的信息
     * @var array
     */
    protected $pidMap = [
        'masterPid'  => 0,
        'managerPid' => 0,
        'workerPids' => [],
        'taskPids'   => []
    ];
    /**
     * 注册的回调事件
     * [
     *   // 这是所有服务均会注册的时间
     *   "server" => [],
     *   // 子类的服务
     *   "sub" => [],
     *   // 额外扩展的回调函数
     *   "ext" => []
     * ]
     *
     * @var array
     */
    protected $event = [
        // 这是所有服务均会注册的时间
        "server" => [
            // 事件   =》 事件函数
            "start"        => "onStart",
            "managerStart" => "onManagerStart",
            "managerStop"  => "onManagerStop",
            "shutdown"     => "onShutdown",
            "workerStart"  => "onWorkerStart",
            "workerStop"   => "onWorkerStop",
            "workerError"  => "onWorkerError",
        ],
        // 子类的服务
        "sub" => [],
        // 额外扩展的回调函数
        // 如 ontart等
        "ext" => []
    ];

    public function __construct(Application $app )
    {
        $this->app = $app;
    }

    /**
     * 创建服务
     */
    protected abstract function createServer();

    /**
     * 初始化监听的事件
     */
    protected abstract function initEvent();


    public function start()
    {
        // 1. 创建 swoole server
        $this->createServer();
        // 2. 设置配置信息
        $this->swooleServer->set($this->config);
        // 3. 设置需要注册的回调函数
        $this->initEvent();
        // 4. 设置swoole的回调函数
        $this->setSwooleEvent();
        // 5. 启动
        $this->swooleServer->start();
    }

    /**
     * 设置swoole的回调事件
     */
    protected function setSwooleEvent()
    {
        foreach ($this->event as $type => $events) {
            foreach ($events as $event => $func) {
                $this->swooleServer->on($event, [$this, $func]);
            }
        }
    }
    protected function watchEvent()
    {
        return function($event){
            $action = 'file:';
            switch ($event['mask']) {
                case IN_CREATE:
                  $action = 'IN_CREATE';
                  break;
                case IN_DELETE:
                  $action = 'IN_DELETE';
                  break;
                case \IN_MODIFY:
                  $action = 'IN_MODIF';
                  break;
                case \IN_MOVE:
                  $action = 'IN_MOVE';
                  break;
            }
            $this->swooleServer->reload();
        };
    }


    /**
     * 启动时可以获取进程ID
     *
     * @param SwooleServer $server
     */
    public function onStart(SwooleServer $server)
    {
        $this->pidMap['masterPid'] = $server->master_pid;
        $this->pidMap['managerPid'] = $server->manager_pid;

        if ($this->watchFile ) {
            $this->inotify = new Inotify($this->app->getBasePath(), $this->watchEvent());
            $this->inotify->start();
        }
    }
    public function onManagerStart(SwooleServer $server)
    {

    }
    public function onManagerStop(SwooleServer $server)
    {

    }
    public function onShutdown(SwooleServer $server)
    {

    }
    public function onWorkerStart(SwooleServer $server, int $worker_id)
    {
        $this->pidMap['workerPids'] = [
            'id'  => $worker_id,
            'pid' => $server->worker_id
        ];
    }
    public function onWorkerStop(SwooleServer $server, int $worker_id)
    {

    }
    public function onWorkerError(SwooleServer $server, int $workerId, int $workerPid, int $exitCode, int $signal)
    {
    }


    /**
     * @param array
     *
     * @return static
     */
    public function setEvent($type, $event)
    {
        // 暂时不支持直接设置系统的回调事件
        if ($type == "server") {
            return $this;
        }
        $this->event[$type] = $event;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     *
     * @return static
     */
    public function setConfig($config)
    {
        $this->config = array_map($this->config, $config);
        return $this;
    }
}
