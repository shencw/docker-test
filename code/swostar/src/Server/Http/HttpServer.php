<?php
namespace SwoStar\Server\Http;

use Swoole\Http\Request;
use Swoole\Http\Response;
use SwoStar\Server\Server;
use Swoole\Http\Server as SwooleServer;

/**
 * Class HttpServer
 *
 * @package SwoStar\Server\Http
 */
class HttpServer extends Server
{
    /**
     * 创建服务
     */
    public function createServer()
    {
        $this->swooleServer = new SwooleServer($this->host, $this->port);
    }


    /**
     * 初始化事件
     */
    protected function initEvent(){
        $this->setEvent('sub', [
            'request' => 'onRequest',
        ]);
    }

    /**
     * 响应事件
     *
     * @param Request  $request
     * @param Response $response
     */
    public function onRequest($request, $response)
    {
        $response->end("<h1>Hello swostar </h1>");
    }
}
