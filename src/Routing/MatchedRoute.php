<?php

declare(strict_types=1);

namespace App\Routing;

class MatchedRoute
{
    private $controller;
    private $parameters;

    public function __construct($controller, array $parameters = array())
    {
        $this->controller = $controller;
        $this->parameters = $parameters;
    }

    public function getController()
    {
        $controller = substr($this->controller, 0, strpos($this->controller, '::'));
        $controller = sprintf('App\Controller\%s%s', $controller, 'Controller');

        return $controller;
    }

    public function getAction()
    {
        $action = str_replace('::', '', substr($this->controller, strpos($this->controller, '::')));
        $action = sprintf('%sAction', $action);

        return $action;
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}
