<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Entity\University;
use App\Entity\AbstractEntity;
use App\Mapper\AbstractMapper;

class UniversityMapper extends AbstractMapper
{
    public function getCollection()
    {
        //TODO
    }
    
    public function doCreateObject(array $obj) {
        $university = new University($obj);
        $this->_em->addToIdentityMap($university);

        $university->getTableStructure();

        return $university;
    }

    public function delete(AbstractEntity $entity)
    {
        // TODO: Implement doDelete() method.
    }

    public function insert(AbstractEntity $entity)
    {
        $table = $entity->getTableStructure();
        $builder = $this->_queryBuilder;
        $builder->insert(get_class($entity)::TABLE);
        $i=0;
        foreach ($table->getColumns() as $column) {
            if ($column == 'id' || $column == 'faculties') { //TODO faculties is collection of Faculty entity, it should be insert not there
                continue;
            }
            $builder->setValue($column, '?');
            $builder->setParameter($i++, $entity->get($column));
        }
        $builder->execute();
    }

    public function update(AbstractEntity $entity)
    {
        $table = $entity->getTableStructure();
        $builder = $this->_queryBuilder;
        $builder->update(get_class($entity)::TABLE);
        foreach ($table->getColumns() as $column) {
            if ($column == 'id' || $column == 'faculties') { //TODO collection, join table
                continue;
            }
            $builder->set($column, '"'.$entity->get($column).'"');
        }
        $builder->where('id = ?');
        $builder->setParameter(0, $entity->getId());
        $builder->execute();
    }

    public function select(int $id)
    {
        $table = $this->targetClass()::TABLE;
        $builder = $this->_queryBuilder;
        $builder->select('*') //TODO join faculties
            ->from($table, 'u')
            ->where('u.id = ?');
        $builder->setParameter(0, $id);
        $row= $builder->execute();
        
        return $this->createObject($row[0]);
    }

    public function selectAll()
    {
        $table = $this->targetClass()::TABLE;
        $builder = $this->_queryBuilder;
        $builder->select('*') //TODO join faculties
            ->from($table, 'u');
        $row= $builder->execute();

        $obj = [];
        foreach ($row as $item) {
            $obj[] = $this->createObject($item);
        }
        return $obj;
    }

    public function targetClass()
    {
        return University::class;
    }
}
