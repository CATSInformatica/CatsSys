<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of Registration
 *
 * @author marcio
 */
class Registration extends EntityRepository
{

    /**
     * Busca por todos as inscrições do processo seletivo $rid cuja data de aprovação não é nula
     * 
     * @param integer $rid
     * @return array
     */
    public function findByAccepted($rid)
    {

        return $this->_em
                ->createQuery('SELECT rg FROM Recruitment\Entity\Registration rg '
                    . 'WHERE rg.recruitment = :rid AND '
                    . ' rg.registrationAcceptanceDate IS NOT NULL'
                )
                ->setParameter('rid', $rid)
                ->getResult();
    }

    public function findOneByPersonCpf($cpf)
    {
        return $this->_em
        ->createQuery('SELECT r FROM Recruitment\Entity\Registration r '
            . 'JOIN r.person p WHERE p.personCpf = :cpf '
            . 'ORDER BY r.recruitment DESC')
            ->setParameter('cpf', $cpf)
            ->getOneOrNullResult();
    }

}
