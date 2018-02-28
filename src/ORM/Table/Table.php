<?php
namespace App\ORM\Table;

class Table
{
    private $tableName;
    private $columns = [];
    
    public function __construct($tableName, $columns) {
        $this->tableName = $tableName;
        
        foreach ($columns as $column => $value) {
            $this->columns[] = $column;
        }
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function getColumn($name)
    {
        return $this->columns[$name];
    }

    public function getColumns()
    {
        return $this->columns;
    }
}
