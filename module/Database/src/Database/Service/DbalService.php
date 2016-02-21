<?php

namespace Database\Service;

/**
 * Description of DbalService
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
trait DbalService
{

    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $dbal;

    protected function getDbalConnection()
    {
        if (null == $this->dbal) {
            $this->dbal = $this->getServiceLocator()->get('doctrine.connection.orm_default');
        }

        return $this->dbal;
    }

}
