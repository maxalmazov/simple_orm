<?php

declare(strict_types=1);

namespace App\ORM\Table;

class Table
{
    private $tableName;
    private $columns = [];
    
    public function __construct(string $tableName, array $columns) {
        $this->tableName = $tableName;
        
        foreach ($columns as $column => $value) {
            $this->columns[] = $column;
        }
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function getColumn(string $name)
    {
        return $this->columns[$name];
    }

    public function getColumns()
    {
        return $this->columns;
    }
}
