<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Recruitment\Entity\Recruitment;

/**
 * Description of Registration
 *
 * @author marcio
 */
class Registration extends EntityRepository
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
     * Busca todos as inscrições com status = $statusType do processo seletivo $rid
     * 
     * @param integer $rid
     * @param integer $statusType
     * @return array
     */
    public function findByStatusType($rid, $statusType)
    {
        return $this->_em
                ->createQuery('SELECT r FROM Recruitment\Entity\Registration r '
                    . 'JOIN r.registrationStatus rs WITH  rs.isCurrent = true '
                    . 'JOIN rs.recruitmentStatus res WITH res.statusType = :stype '
                    . 'WHERE r.recruitment = :rid')
                ->setParameters(array(
                    'rid' => $rid,
                    'stype' => $statusType,
                ))
                ->getResult();
    }

}
