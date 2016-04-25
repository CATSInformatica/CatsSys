<?php

namespace Database\Accessor;

use Doctrine\DBAL\Connection;

/**
 * Description of DbalService
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
trait DbalConnectionAccessor
{

    protected $dbalConnection;

    public function setDbalConnection(Connection $conn)
    {
        $this->dbalConnection = $conn;
    }

    protected function getDbalConnection()
    {
        return $this->dbalConnection;
    }

}
