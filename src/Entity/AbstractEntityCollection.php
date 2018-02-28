<?php

namespace App\Entity\Collection;

use App\Entity\AbstractEntity;
use App\Exception\AppException;
use App\Mapper\AbstractMapper;

abstract class AbstractEntityCollection implements \Iterator
{
    protected $mapper;
    protected $total = 0;
    protected $raw = [];

    private $result;
    private $pointer = 0;
    private $objects = [];

    public function __construct(array $raw = [], AbstractMapper $mapper = null) {
        if ($raw !== [] && !is_null($mapper)) {
            $this->raw = $raw;
            $this->total = count($raw);
        }

        $this->mapper = $mapper;
    }

    public function add(AbstractEntity $entity)
    {
        $class = $this->targetClass();
        if (!($entity instanceof $class)) {
            throw new AppException('This is collection of ' . $class);
        }

        $this->notifyAccess();
        $this->objects[$this->total] = $entity;
        $this->total++;
    }

    abstract public function targetClass();

    protected function notifyAccess()
    {
//    TODO Lazy Load pattern see
    }

    private function getRow($num)
    {
        $this->notifyAccess();
        
        if ($num >= $this->total || $num < 0) {
            return null;
        }

        if (isset($this->objects[$num])) {
            return $this->objects[$num];
        }

        if (isset($this->raw[$num])) {
            $this->objects[$num] = $this->mapper->createObject($this->raw[$num]);
            return $this->objects[$num];
        }
    }

    public function rewind()
    {
        $this->pointer = 0;
    }

    public function current() {
        return $this->getRow($this->pointer);
    }

    public function key() {
        return $this->pointer;
    }

    public function next() {
        $row = $this->current();
        if ($row) {
            $this->pointer++;
        }

        return $row;
    }

    public function valid() {
        return (!is_null($this->current()));
    }
}
