<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Recruitment\Entity\Recruitment;
use Recruitment\Entity\RecruitmentStatus;

/**
 * Description of RegistrationRepository
 *
 * @author marcio
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

}
