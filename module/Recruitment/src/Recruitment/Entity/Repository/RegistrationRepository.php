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
use Doctrine\ORM\Query\Expr;
use Recruitment\Entity\Recruitment;
use Recruitment\Entity\RecruitmentStatus;

/**
 * Contém consultas específicas para a entidade Registration
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class RegistrationRepository extends EntityRepository
{

    /**
     * Busca a última inscrição com cpf $cpf do processo seletivo de alunos
     * 
     * @param string $cpf
     * @return mixed Recruitment\Entity\Registration | null
     */
    public function findOneByPersonCpf($cpf)
    {
        return $this->_em
                ->createQuery('SELECT r FROM Recruitment\Entity\Registration r '
                    . 'JOIN r.person p '
                    . 'JOIN r.recruitment re '
                    . 'WHERE p.personCpf = :cpf '
                    . 'AND re.recruitmentType = :rtype '
                    . 'ORDER BY r.registrationId DESC')
                ->setParameters(array(
                    'cpf' => $cpf,
                    'rtype' => Recruitment::STUDENT_RECRUITMENT_TYPE,
                ))
                ->setMaxResults(1)
                ->getOneOrNullResult();
    }

    /**
     * Busca todas as inscrições do processo seletivo $rid com a situação $statusType.
     * 
     * @param type $rid id do processo seletivo ou constante para todos os processos seletivos
     * @param type $statusType id da situação do candidato ou constante para todas as situações
     * @return array Registrations
     */
    public function findByStatusType($rid, $statusType)
    {

        if ($rid == Recruitment::ALL_VOLUNTEER_RECRUITMENTS) {

            if ($statusType == RecruitmentStatus::STATUSTYPE_ALL) {
                return $this->_em
                        ->createQuery('SELECT r FROM Recruitment\Entity\Registration r '
                            . 'JOIN r.recruitment rc WITH rc.recruitmentType = :type '
                            . 'ORDER BY r.registrationId DESC')
                        ->setParameter('type', Recruitment::VOLUNTEER_RECRUITMENT_TYPE)
                        ->getResult();
            } else {
                return $this->_em
                        ->createQuery('SELECT r FROM Recruitment\Entity\Registration r '
                            . 'JOIN r.registrationStatus rs WITH  rs.isCurrent = true '
                            . 'JOIN rs.recruitmentStatus res WITH res.statusType = :stype '
                            . 'JOIN r.recruitment rc '
                            . 'WHERE rc.recruitmentType = :type '
                            . 'ORDER BY r.registrationId DESC'
                        )
                        ->setParameters(array(
                            'type' => Recruitment::VOLUNTEER_RECRUITMENT_TYPE,
                            'stype' => $statusType,
                        ))
                        ->getResult();
            }
        } else {
            if ($statusType == RecruitmentStatus::STATUSTYPE_ALL) {
                return $this->_em
                        ->createQuery('SELECT r FROM Recruitment\Entity\Registration r WHERE r.recruitment = :rid '
                            . 'ORDER BY r.registrationId DESC'
                        )
                        ->setParameter('rid', $rid)
                        ->getResult();
            } else {
                return $this->_em
                        ->createQuery('SELECT r FROM Recruitment\Entity\Registration r '
                            . 'JOIN r.registrationStatus rs WITH  rs.isCurrent = true '
                            . 'JOIN rs.recruitmentStatus res WITH res.statusType = :stype '
                            . 'WHERE r.recruitment = :rid '
                            . 'ORDER BY r.registrationId DESC'
                        )
                        ->setParameters(array(
                            'rid' => $rid,
                            'stype' => $statusType,
                        ))
                        ->getResult();
            }
        }
    }

    /**
     * Busca todas as inscrições do processo seletivo $rid cuja situação 
     * corrente é $status.
     * 
     * @param int $rid Identificador do processo seletivo
     * @param array $status Situações da inscrição
     * @return array Inscrições do processo seletivo $rid cuja situação corrente
     * é $status
     * @throws \BadMethodCallException Método não implementado
     */
    public function findByStatusSimplified($rid, array $status)
    {

        $qb = $this
            ->_em
            ->createQueryBuilder();

        $qb
            ->select('r.registrationId, CONCAT(CONCAT(p.personFirstName, \' \'), '
                . 'p.personLastName) as personFullName, p.personRg, p.personCpf, '
                . 'p.personEmail, res.statusType')
            ->from('Recruitment\Entity\Registration', 'r')
            ->join('r.person', 'p')
            ->join('r.registrationStatus', 'rs', Expr\Join::WITH, 'rs.isCurrent = true')
            ->join('rs.recruitmentStatus', 'res')
            ->where('r.recruitment = :recruitment')
            ->setParameter('recruitment', $rid);
        
        if (!empty($status)) {
            $qb
                ->andWhere($qb->expr()->in('res.statusType', ':statusArray'))
                ->setParameter('statusArray', $status);
        }
        
        return $qb->getQuery()->getResult();
    }
}
