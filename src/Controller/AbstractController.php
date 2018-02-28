<?php

namespace App\Controller;


use App\ORM\Manager\EntityManager;
use App\Request\Request;

abstract class AbstractController implements ControllerInterface
{
    protected $mapper;

    public function __construct() {
        $this->mapper = $this->getMapper(get_class($this));
    }

    private function getMapper($class)
    {
        $mapper = $this->parseClass($class);

        return new $mapper;
    }

    private function parseClass($class)
    {
        $num = strrpos($class, '\\')+1;
        $class = substr($class, $num);
        $class = str_replace('Controller', '', $class);

        return sprintf('App\Mapper\%sMapper', $class);
    }

    public function getEntityManager()
    {
        return EntityManager::instance();
    }
}

