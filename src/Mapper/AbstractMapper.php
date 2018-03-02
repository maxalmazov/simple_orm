<?php

namespace App\Mapper;

use App\Application;
use App\Entity\AbstractEntity;
use App\ORM\Manager\EntityManager;
use App\ORM\Query\QueryBuilder;

abstract class AbstractMapper
{
    protected $_conn;
    protected $_em;
    protected $_queryBuilder;

    public function __construct()
    {
        $this->_conn = Application::instance()->getConnection();
        $this->_em = EntityManager::instance();
        $this->_queryBuilder = new QueryBuilder($this->_conn);
    }

    public function find(int $id)
    {
        $object = $this->getFormMap($id);
        if (!is_null($object)) {
            return $object;
        }

        $object = $this->select($id);
        return $object;
    }

    public function findAll()
    {
//        $object = $this->getFormMap();TODO: return UniversityCollection
//        if (!is_null($object)) {
//            return $object;
//        }
    
        $object = $this->selectAll();
        return $object;
    }

    public function createObject(array $array) //TODO это функционал вынести на отдельный класс Factory нужно
    {
        $object = $this->getFormMap($array['id']);
        if (!is_null($object)) {
            return $object;
        }
        $object = $this->doCreateObject($array);

        return $object;
    }
    
    private function getFormMap(int $id)
    {
        return $this->_em->isInIdentityMap($id, $this->targetClass()) ? : null;
    }
    
    private function addToMap(AbstractEntity $entity)
    {
        $this->_em->addToIdentityMap($entity);
    }
    
    abstract public function insert(AbstractEntity $entity);
    abstract public function update(AbstractEntity $entity);
    abstract public function delete(AbstractEntity $entity);
    abstract public function doCreateObject(array $obj);
    abstract public function select(int $id);
    abstract public function selectAll();
    abstract public function targetClass();
}
