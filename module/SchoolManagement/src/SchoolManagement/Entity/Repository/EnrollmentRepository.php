<?php

namespace SchoolManagement\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of Enrollment
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class EnrollmentRepository extends EntityRepository
{

    public function findAllCurrentStudents($params)
    {
        return $this->_em
                ->createQuery('SELECT e.enrollmentId, p.personFirstName, p.personLastName '
                    . 'FROM SchoolManagement\Entity\Enrollment e '
                    . 'JOIN e.registration r '
                    . 'JOIN r.person p '
                    . 'WHERE e.class = :class AND e.enrollmentEndDate IS NULL '
                    . 'ORDER BY p.personFirstName ASC'
                )
                ->setParameters($params)
                ->getResult();
    }

}
