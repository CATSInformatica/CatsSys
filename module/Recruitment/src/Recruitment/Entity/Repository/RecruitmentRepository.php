<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of RecruitmentRepository
 *
 * @author marcio
 */
class RecruitmentRepository extends EntityRepository
{

    /**
     * Indica a quantidade de dias de antecedência na qual o processo seletivo poderá ser divulgado publicamente.
     * 
     * Ex: Divulgar automaticamente no site.
     */
    const RECRUITMENT_DAYOFFSET = 5;

    /**
     * Busca um processo seletivo aberto do tipo $type na data especificada $date
     * 
     * $type é um dos valores:
     *  - Recruitment\Entity\Recruitment::STUDENT_RECRUITMENT_TYPE
     *  - Recruitment\Entity\Recruitment::VOLUNTEER_RECRUITMENT_TYPE
     * 
     * @param integer $type é um dos seguintes valores:
     *  - Recruitment\Entity\Recruitment::STUDENT_RECRUITMENT_TYPE
     *  - Recruitment\Entity\Recruitment::VOLUNTEER_RECRUITMENT_TYPE
     * @param \DateTime $date
     * @return mixed \Recruitment\Entity\Recruitment or null
     */
    public function findByTypeAndBetweenBeginAndEndDates($type, \DateTime $date)
    {
        return $this->_em
                ->createQuery('SELECT r FROM Recruitment\Entity\Recruitment r '
                    . 'WHERE r.recruitmentType = :type AND '
                    . ':date BETWEEN r.recruitmentBeginDate and r.recruitmentEndDate'
                )
                ->setParameters(array(
                    'type' => $type,
                    'date' => $date,
                ))
                ->getOneOrNullResult();
    }

    /**
     * Busca um processo seletivo aberto do tipo $type na data especificada $date.
     * 
     * $type é um dos valores:
     *  - Recruitment\Entity\Recruitment::STUDENT_RECRUITMENT_TYPE
     *  - Recruitment\Entity\Recruitment::VOLUNTEER_RECRUITMENT_TYPE
     * 
     * @param integer $type é um dos seguintes valores:
     *  - Recruitment\Entity\Recruitment::STUDENT_RECRUITMENT_TYPE
     *  - Recruitment\Entity\Recruitment::VOLUNTEER_RECRUITMENT_TYPE
     * @param \DateTime $date
     * @return array|null
     */
    public function findByTypeAndBetweenBeginAndEndDatesAsArray($type, \DateTime $date)
    {
        return $this->_em
                ->createQuery('SELECT r.recruitmentId, r.recruitmentNumber, r.recruitmentYear, r.recruitmentBeginDate, '
                    . 'r.recruitmentEndDate, r.recruitmentPublicNotice '
                    . 'FROM Recruitment\Entity\Recruitment r '
                    . 'WHERE r.recruitmentType = :type AND '
                    . ':date BETWEEN r.recruitmentBeginDate and r.recruitmentEndDate'
                )
                ->setParameters(array(
                    'type' => $type,
                    'date' => $date,
                ))
                ->getOneOrNullResult();
    }

}
