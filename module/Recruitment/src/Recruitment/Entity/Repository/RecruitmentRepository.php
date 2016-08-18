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

namespace Recruitment\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Contém consultas específicas para a entidade Recruitment.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
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
                ->setMaxResults(1)
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
                ->setMaxResults(1)
                ->getOneOrNullResult();
    }

    /**
     * Busca o último processo seletivo do tipo $type já encerrado.
     * 
     * @param int $type Tipo de processo seletivo [aluno, voluntário]
     * @return array|null Informações do processo seletivo encontrado ou null.
     */
    public function findLastClosed($type)
    {
        return $this->_em
                ->createQuery('SELECT r.recruitmentId, r.recruitmentNumber, r.recruitmentYear, r.recruitmentBeginDate, '
                    . 'r.recruitmentEndDate, r.recruitmentSocioeconomicTarget, r.recruitmentVulnerabilityTarget, r.recruitmentStudentTarget '
                    . 'FROM Recruitment\Entity\Recruitment r '
                    . 'WHERE r.recruitmentType = :type AND '
                    . 'r.recruitmentEndDate < CURRENT_DATE() '
                    . 'ORDER BY r.recruitmentId DESC'
                )
                ->setParameter('type', $type)
                ->setMaxResults(1)
                ->getOneOrNullResult();
    }
}
