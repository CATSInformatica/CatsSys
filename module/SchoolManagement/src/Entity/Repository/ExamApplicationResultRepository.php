<?php

namespace SchoolManagement\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use SchoolManagement\Entity\ExamApplicationResult;

/**
 * Repositorio para ExamApplicationResult
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class ExamApplicationResultRepository extends EntityRepository
{
    public function findAllAnswersForClassOrRecruitment(int $appId, bool $isStudent)
    {
        if ($isStudent) {
            return $this->_em
                ->createQuery(
                    'SELECT er FROM SchoolManagement\Entity\ExamApplicationResult er '
                        . 'WHERE er.application = :app and er.enrollment is not null'
                )
                ->setParameters([
                    'app' => $appId,
                ])
                ->getResult();
        }

        return $this->_em
            ->createQuery(
                'SELECT er FROM SchoolManagement\Entity\ExamApplicationResult er '
                    . 'WHERE er.application = :app and er.registration is not null'
            )
            ->setParameters([
                'app' => $appId,
            ])
            ->getResult();
    }

    public function deleteAllOfApplication(int $applicationId)
    {
        $qb = $this
            ->_em
            ->createQueryBuilder();

        $qb
            ->delete(ExamApplicationResult::class, 'a')
            ->where('a.application = :application')
            ->setParameter('application', $applicationId)
            ->getQuery()
            ->execute();
    }
}
