<?php
namespace SwoStar\Foundation;

use SwoStar\Server\Http\HttpServer;

/**
 * Class Application
 *
 * @package SwoStar\Foundation
 */
class Application
{

    protected const SWOSTAR_WELCOME = "
      _____                     _____     ___
     /  __/             ____   /  __/  __/  /__   ___ __    __  __
     \__ \  | | /| / / / __ \  \__ \  /_   ___/  /  _`  |  |  \/ /
     __/ /  | |/ |/ / / /_/ /  __/ /   /  /_    |  (_|  |  |   _/
    /___/   |__/\__/  \____/  /___/    \___/     \___/\_|  |__|
    ";

    protected $basePath = "";

    /**
     * Application constructor.
     *
     * @param null $path
     */
    public function __construct($path = null)
    {
        if (!empty($path)) {
            $this->setBasePath($path);
        }
        echo self::SWOSTAR_WELCOME."\n";
    }

    /**
     * 启动
     */
    public function run()
    {
        $httpServer = new HttpServer($this);
        $httpServer->start();
    }

    /**
     * @param $path
     */
    public function setBasePath($path)
    {
        $this->basePath = \rtrim($path, '\/');
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }
} 
