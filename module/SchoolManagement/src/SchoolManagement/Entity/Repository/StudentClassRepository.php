<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of StudentClass
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class StudentClassRepository extends EntityRepository
{

    public function findByEndDateGratherThan(\DateTime $endDate)
    {
        return $this->_em
                        ->createQuery('SELECT sc FROM SchoolManagement\Entity\StudentClass sc '
                                . 'WHERE sc.classEndDate > :date ORDER BY sc.classId DESC'
                        )
                        ->setParameters(array(
                            'date' => $endDate,
                        ))
                        ->getResult();
    }

}
