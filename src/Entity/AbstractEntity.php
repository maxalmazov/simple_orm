<?php

namespace App\Entity;

abstract class AbstractEntity implements EntityInterface
{
    static function getCollection($class)
    {
        return 0;
    }

    public function collection()
    {
        return self::getCollection(get_class($this));
    }

    abstract public function getTableStructure();
}
