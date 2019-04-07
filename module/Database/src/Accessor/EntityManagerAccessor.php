<?php

namespace Database\Accessor;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of DbalService
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
trait EntityManagerAccessor
{

    protected $entityManager;

    public function setEntityManager(ObjectManager $obj)
    {
        $this->entityManager = $obj;
    }

    protected function getEntityManager() : ObjectManager
    {
        return $this->entityManager;
    }

}
