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
class ExamQuestionRepository extends EntityRepository
{
    public function count($subjectId, $questionType = null)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
            ->select('count(e.examQuestionId)')
            ->from('SchoolManagement\Entity\ExamQuestion', 'e')
            ->where('e.subject = :subject')
            ->setParameter(':subject', $subjectId);

        if ($questionType > 0) {
            $qb
                ->andWhere('e.examQuestionType = :examQuestionType')
                ->setParameter(':examQuestionType', $questionType);
        }

        return (int)$qb->getQuery()->getSingleScalarResult();
    }
}
