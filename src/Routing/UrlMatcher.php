<?php

namespace App\Routing;

use App\Routing\MatchedRoute;

class UrlMatcher
{
    private $methods = [
      'GET',
      'POST',
      'PUT',
      'DELETE',
      'HEAD',
    ];

    private $routes = [
      'GET' => [],
      'POST' => [],
      'PUT' => [],
      'DELETE' => [],
      'PATCH' => [],
      'HEAD' => [],
    ];

    private $patterns = array(
      'num' => '[0-9]+',
      'str' => '[a-zA-Z\.\-_%]+',
      'any' => '[a-zA-Z0-9\.\-_%]+',
    );

    private $redirectUrl;

    public function addPattern($name, $pattern)
    {
        $this->patterns[$name] = $pattern;
    }

    public function register($method, $route, $controller)
    {
        $methods = strtoupper($method);
        if (false !== strpos($methods, '|')) {
            $methods = explode('|', $methods);
        }

        if ($methods == '*') {
            $methods = $this->methods;
        }

        $methods = (array)$methods;
        $converted = $this->convertRoute($route);

        foreach ($methods as $m) {
            $this->routes[$m][$converted] = $controller;
        }
    }

    private function convertRoute($route)
    {
        if (false === strpos($route, '{')) {
            return $route;
        }

        $parse = preg_replace_callback('#\/(\w+)\/{:(\w+)}#', [$this, 'replaceOptionalRoute'], $route);

        return preg_replace_callback('#\/(\w+)\/(\w+)#', [$this, 'replaceRoute'], $parse);
    }

    private function replaceOptionalRoute($match)
    {
        $name = $match[1];
        $pattern = $match[2];

        return sprintf('/\/%s\/(%s)/', $name, strtr($pattern, $this->patterns));
    }

    private function replaceRoute($match)
    {
        $name = $match[1];
        $pattern = $match[2];

        return sprintf('(?<%s>%s)', $name, strtr($pattern, $this->patterns));
    }

    /**
     * @param $method
     * @param $uri
     * @return MatchedRoute
     */
    public function match($method, $uri)
    {
        $this->redirectUrl = null;
        $method = strtoupper($method);
        $route = $this->doMatch($method, $uri);
        if ($route != null)
            return $route;
        if ($method == 'GET')
            $this->tryFindUrlToRedirect($uri);
    }

    /**
     * Try find similar url, e.g.
     *   /blog/ -> /blog if /blog exists
     *   /blog -> /blog/ if /blog/ exists
     * @param $uri
     */
    private function tryFindUrlToRedirect($uri)
    {
        $tmpUri = $uri . '/';
        if (substr($uri, -1) === '/')
            $tmpUri = rtrim($uri, '/');
        $route = $this->doMatch('GET', $tmpUri);
        if ($route)
            $this->redirectUrl = $tmpUri;
    }

    public function isNeedRedirect()
    {
        return $this->redirectUrl != null;
    }

    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    private function routes($method)
    {
        return isset($this->routes[$method]) ? $this->routes[$method] : array();
    }

    private function doMatch($method, $uri)
    {
        $routes = $this->routes($method);
        if (array_key_exists($uri, $routes)) {
            return new MatchedRoute($routes[$uri]);
        }
        foreach ($routes as $route => $controller) {
            if (false !== strpos($route, '(')) {
                $pattern = $route;
                if (preg_match($pattern, $uri, $parameters)) {
                    return new MatchedRoute($controller, $this->processParameters($parameters));
                }
            }
        }
    }

    private function processParameters($parameters)
    {
        foreach ($parameters as $k => $v) {
            if (is_int($k)) {
                unset($parameters[$k]);
            }
        }
        return $parameters;
    }
}
