<?php

namespace SchoolManagement\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Repositorio para ExamResult
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class ExamResultRepository extends EntityRepository
{
    public function findAllAnswersForClassOrRecruitment(int $examId, bool $isStudent)
    {
        if($isStudent) {
            return $this->_em
                ->createQuery('SELECT er FROM SchoolManagement\Entity\ExamResult er '
                        . 'WHERE er.exam = :exam and er.enrollment is not null'
                )
                ->setParameters([
                    'exam' => $examId,
                ])
                ->getResult();
        }

        return $this->_em
            ->createQuery('SELECT er FROM SchoolManagement\Entity\ExamResult er '
                    . 'WHERE er.exam = :exam and er.registration is not null'
            )
            ->setParameters([
                'exam' => $examId,
            ])
            ->getResult();
    }
}
