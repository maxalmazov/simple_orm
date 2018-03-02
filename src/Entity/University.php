<?php

namespace App\Entity;

use App\Entity\AbstractEntity;
use App\Entity\Collection\FacultyCollection;
use App\ORM\Relationship\OneToMany;
use App\ORM\Table\Table;

class University extends AbstractEntity implements EntityInterface
{
    const TABLE = 'university';
    private static $tableInstance = null;
    private static $relationship = null; //TODO

    private $id;
    private $name;
    private $city;
    private $faculties;

    public function __construct($data = null) {
        if (!is_null($data)) {
            $this->id = isset($data['id']) ? $data['id'] : null;
            $this->name = isset($data['name']) ? $data['name'] : null;
            $this->city = isset($data['city']) ? $data['city'] : null;
            $this->faculties = isset($data['faculties']) ? $data['faculties'] : null;
        }
    }

    public function __toString() {
        return __CLASS__;
    }
    
    public function get(string $field)
    {
        return $this->{'get'.ucfirst($field)}();
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(int $id): void {
        $this->id = $id;
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
    public function getCity() {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity(string $city): void {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getFaculties() {
        if (is_null($this->faculties)) {
            $this->faculties = $this->collection();
        }
        return $this->faculties;
    }

    /**
     * @param mixed $faculties
     */
    public function setFaculties(/* TODO  FacultyCollection */$faculties): void {
        $this->faculties = $faculties;
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
    {//TODO
        $query = '
            CREATE TABLE university
            (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL,
                city VARCHAR(30) NOT NULL
            );
            CREATE UNIQUE INDEX university_name_uindex ON university (name);
            ';
    }
}
