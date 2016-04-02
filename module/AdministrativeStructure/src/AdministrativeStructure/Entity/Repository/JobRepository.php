<?php

/*
 * Copyright (C) 2016 Márcio Dias <marciojr91@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace AdministrativeStructure\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of JobRepository
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class JobRepository extends EntityRepository
{

    /**
     * Busca todos os cargos ignorando o cargo cujo id = $ignoredId
     * @param integer $ignoredId
     */
    public function findIgnoring($ignoredId = null)
    {

        if ($ignoredId !== null) {
            $result = $this->
                _em
                ->createQuery('SELECT j FROM AdministrativeStructure\Entity\Job j WHERE j.jobId <> :id')
                ->setParameter('id', $ignoredId)
                ->getResult();
        } else {
            $result = $this->
                _em
                ->createQuery('SELECT j FROM AdministrativeStructure\Entity\Job j')
                ->getResult();
        }

        return $result;
    }

}
