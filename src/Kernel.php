<?php

declare(strict_types=1);

namespace App;

use App\Exception\AppException;

class Kernel
{
    /**
     * @var $application \App\Application
     */
    private $application;

    private function __construct()
    {

    }

    public static function run()
    {
        $instance = new Kernel();
        try {
            $instance->init();
        } catch (AppException $e) {
        //TODO
        }
        $instance->handleRequest();
    }
    
    public function init()
    {
        $this->application = Application::instance();
        $this->application->init();
    }
    
    public function handleRequest()
    {
        $request = $this->application->getRequest();
        $router = $this->application->getRouter();
        
        $mathedRoute = $router->match($request->getMethod(), $request->getPathInfo());
        
        $controllerResolver = new ControllerResolver();
        $controller = $controllerResolver->getController($mathedRoute);
        $action = $controllerResolver->getAction($mathedRoute);
        
        $controller->$action($request);
    }
}