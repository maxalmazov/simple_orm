<?php

declare(strict_types=1);

namespace App\Request;

use App\Application;

class Request
{
    private $data;
    private $pathInfo;
    private $postVar = [];
    private $getVar = [];
    private $uriParams = [];

    public function __construct()
    {
        $this->init();
    }

    public function init($data = null)
    {
        $this->data = $data != null ? $data : $_SERVER;
        $this->inGet();
        $this->inPost();
        $this->uriParams = explode('/', $this->getPathInfo());
    }

    public function isPost()
    {
        return $this->get('REQUEST_METHOD') == 'POST';
    }

    public function isMethod(string $method)
    {
        return $this->getMethod() == strtoupper($method);
    }

    public function getMethod()
    {
        $method = $this->get('REQUEST_METHOD');
        if ($this->isPost()) {
            if ($this->has('X-HTTP-METHOD-OVERRIDE')) {
                $method = strtoupper($this->get('X-HTTP-METHOD-OVERRIDE'));
            }
        }
        return $method;
    }

    public function getHTTPHost()
    {
        return $this->getHost();
    }

    public function getHost()
    {
        $host = $this->get('HTTP_HOST');
        $host = strtolower(preg_replace('/:\d+$/', '', trim($host)));
        if ($host && !preg_match('/^\[?(?:[a-zA-Z0-9-:\]_]+\.?)+$/', $host)) {
            throw new \UnexpectedValueException(sprintf('Invalid Host "%s"', $host));
        }
        return $host;
    }

    public function getPathInfo($baseUrl = null)
    {
        if (null != $this->pathInfo) {
            return $this->pathInfo;
        }

        $pathInfo = $this->get('REQUEST_URI');
        if (!$pathInfo) {
            $pathInfo = '/';
        }

        $schemeAndHttpHost = 'http://';
        $schemeAndHttpHost .= $this->get('HTTP_HOST');
        if (strpos($pathInfo, $schemeAndHttpHost) === 0) {
            $pathInfo = substr($pathInfo, strlen($schemeAndHttpHost));
        }

        if ($pos = strpos($pathInfo, '?')) {
            $pathInfo = substr($pathInfo, 0, $pos);
        }

        if (null != $baseUrl) {
            $pathInfo = substr($pathInfo, strlen($pathInfo));
        }

        if (!$pathInfo) {
            $pathInfo = '/';
        }

        return $this->pathInfo = $pathInfo;
    }

    public function get(string $name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    public function has(string $name)
    {
        return $this->get($name) != null;
    }

    private function inPost()//TODO add validator?
    {
        foreach ($_POST as $key => $value) {
            $this->postVar[strip_tags($key)] = strip_tags(htmlentities($value));
        }
    }

    /**
     * @return array
     */
    public function getPostVar(): array
    {
        return $this->postVar;
    }

    private function inGet()
    {
        foreach ($_GET as $key => $value) {
            $this->postVar[strip_tags($key)] = strip_tags(htmlentities($value));
        }
    }

    /**
     * @return array
     */
    public function getGetVar(): array
    {
        return $this->getVar;
    }
    
    public function redirectToRoute(string $name)
    {
        $url = Application::instance()->getRouter()->generate($name);
        
        header('Location: ' . $url);
    }
    
    public function getUriParams()
    {
        return $this->uriParams;
    }
}
