<?php

declare(strict_types=1);

namespace App\ORM\Manager;

use App\Application;
use App\Entity\AbstractEntity;

/**
 * Class EntityManager (Identity Map and Unit of work patterns)
 * @package App\ORM\Manager
 */
class EntityManager
{
    private static $instance = null;

    private $identityMap = [];
    private $entityIdentifiers = [];

    private $entityInsertions = [];
    private $entityUpdates = [];
    private $entityDeletions = [];

    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getEntityIdentifier(AbstractEntity $entity)
    {
        return spl_object_hash($entity);
    }

    public function addToIdentityMap(AbstractEntity $entity)
    {
        self::instance()->identityMap[$this->getEntityIdentifier($entity)] = $entity;
        self::instance()->entityIdentifiers[get_class($entity)][$entity->getId()] = $entity;
    }

    public function isInIdentityMap(int $id, string $className)
    {
        return isset($this->identityMap[$className][$id]) ? $this->identityMap[$className][$id] : false;
    }

    public function exist(AbstractEntity $entity)
    {
        $inst = self::instance();

        return isset($inst->identityMap[$this->getEntityIdentifier($entity)]);
    }

    public function flush()
    {
        $this->commit();
    }

    public function persist(AbstractEntity $entity)
    {
        if ($this->exist($entity) && !is_null($entity->getId())) {
            $this->addToUpdate($entity);
        } else {
            $this->addToInsert($entity);
        }
    }

    public function remove(AbstractEntity $entity)
    {
        $this->addToDelete($entity);
    }

    private function addToInsert(AbstractEntity $entity)
    {
        $inst = self::instance();
        $inst->entityInsertions[$inst->getEntityIdentifier($entity)] = $entity;
    }

    private function addToUpdate(AbstractEntity $entity)
    {
        $inst = self::instance();
        $inst->entityUpdates[$inst->getEntityIdentifier($entity)] = $entity;
    }

    private function addToDelete(AbstractEntity $entity)
    {
        $inst = self::instance();
        $inst->entityDeletions[$inst->getEntityIdentifier($entity)] = $entity;
    }

    public function commit()
    {
        try {
            if ($this->entityInsertions) {
                foreach ($this->entityInsertions as $entityInsertion) {
                    $this->executeInserts($entityInsertion);
                }
            }

            if ($this->entityUpdates) {
                foreach ($this->entityUpdates as $entityUpdate) {
                    $this->executeUpdates($entityUpdate);
                }
            }

            if ($this->entityDeletions) {
                foreach ($this->entityDeletions as $entityDeletion) {

                    $this->executeDeletions($entityDeletion);
                }
            }
        } catch (\Throwable $e) {
            throw $e;
        }

        $this->entityInsertions            =
        $this->entityUpdates               =
        $this->entityDeletions             = [];
    }

    private function executeInserts(AbstractEntity $entityInsertion)
    {
        $mapper = $this->getMapper(get_class($entityInsertion));
        $mapper->insert($entityInsertion);
    }

    private function executeUpdates(AbstractEntity $entityUpdate)
    {
        $mapper = $this->getMapper(get_class($entityUpdate));
        $mapper->update($entityUpdate);
    }

    private function executeDeletions(AbstractEntity $entityDeletion)
    {
        //TODO
    }
    
    public function getMapper(string $entity)
    {
        $num = strrpos($entity, '\\')+1;
        $class = substr($entity, $num);
        $mapper = sprintf('App\Mapper\%sMapper', $class);
    
        return new $mapper;
    }
}
