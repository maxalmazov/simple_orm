<?php

namespace App\Entity;

use App\ORM\Table\Table;

class Faculty extends AbstractEntity
{
    private $id;

    private $name;

    private $university;

    private static $tableInstance;

    public function __construct() {
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUniversity() {
        return $this->university;
    }

    /**
     * @param mixed $university
     */
    public function setUniversity(University $university): void {
        $this->university = $university;
    }

    public function getTableStructure(): Table
    {
        if (self::$tableInstance === null) {
            $this->setTable();
        }
        
        return self::$tableInstance;
    }

    private function setTable()
    {
        self::$tableInstance = new Table(self::TABLE, get_object_vars($this));
    }

    public static function install()
    {
        $query = '
            CREATE TABLE faculties
            (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(100),
                university_id INT NOT NULL,
                CONSTRAINT faculties_university__fk FOREIGN KEY (university_id) REFERENCES university (id) ON DELETE CASCADE ON UPDATE CASCADE
            );
            ';
    }
}
