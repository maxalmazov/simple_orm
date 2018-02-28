<?php

namespace App\Routing;

use App\Routing\UrlMatcher;
use App\Routing\UrlGenerator;

class Router
{
    private $routes = array();
    private $host;
    private $matcher;
    private $generator;

    public function __construct($host = null)
    {
        $this->host = $host;
        $this->getRoutes();
    }

    public function add($name, $pattern, $controller, $method = [])
    {
        $this->routes[$name] = array(
          'pattern' => $pattern,
          'controller' => $controller,
          'method' => $method,
        );
    }

    public function getRoutes()
    {
        $routes =  require_once __DIR__ . '/../config/routing.php';

        foreach ($routes as $name => $route) {
            $this->add($name, $route['pattern'], $route['controller'], $route['method']);
        }
    }

    /**
     * @param $method
     * @param $uri
     * @return MatchedRoute
     */
    public function match($method, $uri)
    {
        return $this->getMatcher()->match($method, $uri);
    }

    /**
     * @return UrlMatcher
     */
    public function getMatcher() {
        if (null == $this->matcher) {
            $this->matcher = new UrlMatcher();
            foreach ($this->routes as $route) {
                $this->matcher->register($route['method'], $route['pattern'], $route['controller']);
            }
        }

        return $this->matcher;
    }

    public function generate($name, array $parameters = array(), $absolute = false)
    {
        return $this->getGenerator()->generate($name, $parameters, $absolute);
    }
    
    
    /**
     * @return UrlGenerator
     */
    public function getGenerator()
    {
        if (null == $this->generator) {
            $this->generator = new UrlGenerator($this->host);

            foreach ($this->routes as $name => $route) {
                $pattern = preg_replace('#\((\w+):(\w+):\?\)#', '(:$1:?)', $route['pattern']);
                $pattern = preg_replace('#\((\w+):(\w+)\)#', '(:$1)', $pattern);
                $this->generator->add($name, $pattern);
            }
        }
        return $this->generator;
    }
}
