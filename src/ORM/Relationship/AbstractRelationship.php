<?php

declare(strict_types=1);

namespace App\ORM\Relationship;

abstract class AbstractRelationship
{

    protected $name;

    protected $nativeMapperClass;

    protected $nativeMapper;

    protected $foreignMapperClass;

    protected $foreignMapper;

    protected $foreignTableName;

    protected $on = [];

    protected $initialized = false;

    public function __construct(
      string $name,
      string $nativeMapperClass,
      string $foreignMapperClass,
      string $throughName = null
    ) {
        $this->name = $name;
        $this->nativeMapperClass = $nativeMapperClass;
        $this->foreignMapperClass = $foreignMapperClass;
    }
}
