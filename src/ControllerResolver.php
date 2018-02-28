<?php

namespace App;
use App\Request\Request;
use App\Routing\MatchedRoute;
use App\Controller\ControllerInterface;


class ControllerResolver
{

    public function __construct() {
    
    }

    public function getController(MatchedRoute $matchedRoute)
    {
        $controllerName = $matchedRoute->getController();
        
        $controller = new $controllerName;
        if ($controller instanceof ControllerInterface) {
            return $controller;
        }
        
        return null;
    }

    public function getAction(MatchedRoute $matchedRoute)
    {
        return $matchedRoute->getAction();
    }
}
