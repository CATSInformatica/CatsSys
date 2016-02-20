<?php

namespace SchoolManagement\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of Attendance
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class Attendance extends EntityRepository
{

    public function findAttendanceOfCurrentStudentsByClass($classId, array $dates)
    {
//        $this->_em->createQuery('SELECT a.enrollmentId, a.attendanceType,  p.personFirstName, p.personLastName'
//                . 'FROM SchoolManagement\Entity\Attendance a '
//                . 'RIGHT JOIN a.enrollment e '
//                . 'JOIN e.registration r '
//                . 'JOIN r.person p '
//                . 'WHERE e.class = :id AND e.enrollmentEndDate IS NULL '
//                . 'ORDER BY a.enrollmentId ASC, a.'
//            )
//            ->setParameters($classId)
//            ->getResult();
    }

}
