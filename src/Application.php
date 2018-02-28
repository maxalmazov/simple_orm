<?php

namespace App;

use App\Connection\DatabaseConnection;
use App\Request\Request;
use App\Routing\Router;

class Application
{
    private static $instance;
    
    private $request;
    private $router;
    private $connection;

    private function __construct()
    {

    }

    public static function instance()
    {
        if (!isset (self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function init()
    {
        $request = new Request();
        $router = new Router($request->getHost());
        
        $this->setRequest($request);
        $this->setRouter($router);
        $this->setConnection();
    }
    
    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    private function setRequest($request): void
    {
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param mixed $router
     */
    private function setRouter($router): void
    {
        $this->router = $router;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    private function setConnection(): void
    {
        $this->connection = DatabaseConnection::getInstance();
    }

    
}
