<?php

namespace SchoolManagement\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of AttendanceType
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class AttendanceType extends EntityRepository
{

    public function findByAttendanceTypeIds($ids)
    {

        $qb = $this->_em->createQueryBuilder();

        $qb->select('t')
            ->from('SchoolManagement\Entity\AttendanceType', 't')
            ->where('t.attendanceTypeId = :id')
            ->setParameter('id', $ids[0]);

        $idLen = count($ids);

        for ($i = 1; $i < $idLen; $i++) {
            $qb
                ->orWhere('t.attendanceTypeId = :id' . $i)
                ->setParameter('id' . $i, $ids[$i]);
        }

        return $qb->getQuery()->getResult();
    }

}
