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

namespace Recruitment\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * ORM da tabela `recruitment_status`.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="recruitment_status")
 * @ORM\Entity(readOnly=true)
 */
class RecruitmentStatus
{

    const STATUSTYPE_ALL = -1;
    const STATUSTYPE_REGISTERED = 0;
    const STATUSTYPE_CALLEDFOR_INTERVIEW = 1;
    const STATUSTYPE_CANCELED_REGISTRATION = 2;
    const STATUSTYPE_INTERVIEWED = 3;
    const STATUSTYPE_INTERVIEW_WAITINGLIST = 4;
    const STATUSTYPE_INTERVIEW_APPROVED = 5;
    const STATUSTYPE_INTERVIEW_DISAPPROVED = 6;
    const STATUSTYPE_VOLUNTEER = 7;
    const STATUSTYPE_CALLEDFOR_TESTCLASS = 8;
    const STATUSTYPE_TESTCLASS_COMPLETE = 9;
    const STATUSTYPE_TESTCLASS_WAITINGLIST = 10;
    const STATUSTYPE_CONFIRMED = 11;
    const STATUSTYPE_CALLEDFOR_PREINTERVIEW = 12;
    const STATUSTYPE_PREINTERVIEW_COMPLETE = 13;

    /**
     * Descrição das constantes anteriores
     */
    const STATUSTYPEDESC_ALL = 'TODOS';
    const STATUSTYPEDESC_REGISTERED = 'INSCRITO';
    const STATUSTYPEDESC_CALLEDFOR_INTERVIEW = 'CONVOCADO PARA ENTREVISTA';
    const STATUSTYPEDESC_CANCELED_REGISTRATION = 'INSCRIÇÃO CANCELADA';
    const STATUSTYPEDESC_INTERVIEWED = 'ENTREVISTADO';
    const STATUSTYPEDESC_INTERVIEW_WAITINGLIST = 'LISTA DE ESPERA DA ENTREVISTA';
    const STATUSTYPEDESC_INTERVIEW_APPROVED = 'APROVADO EM ENTREVISTA';
    const STATUSTYPEDESC_INTERVIEW_DISAPPROVED = 'REPROVADO EM ENTREVISTA';
    const STATUSTYPEDESC_VOLUNTEER = 'VOLUNTÁRIO';
    const STATUSTYPEDESC_CALLEDFOR_TESTCLASS = 'CONVOCADO PARA AULA TESTE';
    const STATUSTYPEDESC_TESTCLASS_COMPLETE = 'AULA TESTE COMPLETA';
    const STATUSTYPEDESC_TESTCLASS_WAITINGLIST = 'LISTA DE ESPERA DA AULA TESTE';
    const STATUSTYPEDESC_CONFIRMED = 'CONFIRMADO';
    const STATUSTYPEDESC_CALLEDFOR_PREINTERVIEW = 'CONVOCADO PARA PRÉ-ENTREVISTA';
    const STATUSTYPEDESC_PREINTERVIEW_COMPLETE = 'PRÉ-ENTREVISTA CONCUÍDA';
    const STATUSTYPEDESC_INVALID = 'INVÁLIDO';

    /**
     *
     * @var integer
     * @ORM\Column(name="recruitment_status_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $statusId;

    /**
     * @var integer
     * @ORM\Column(name="recruitment_status_type", type="smallint", nullable=false, unique=true)
     */
    private $statusType;

    /**
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="RegistrationStatus", mappedBy="recruitmentStatus")
     */
    private $regStatus;

    public function __construct()
    {
        $this->regStatus = new ArrayCollection();
    }

    public function getStatusType()
    {
        return self::statusTypeToString($this->statusType);
    }

    /**
     * 
     * @param integer $sType
     */
    public static function statusTypeToString($sType)
    {
        switch ($sType) {
            case self::STATUSTYPE_REGISTERED:
                $status = self::STATUSTYPEDESC_REGISTERED;
                break;
            case self::STATUSTYPE_CALLEDFOR_INTERVIEW:
                $status = self::STATUSTYPEDESC_CALLEDFOR_INTERVIEW;
                break;
            case self::STATUSTYPE_CANCELED_REGISTRATION:
                $status = self::STATUSTYPEDESC_CANCELED_REGISTRATION;
                break;
            case self::STATUSTYPE_INTERVIEWED:
                $status = self::STATUSTYPEDESC_INTERVIEWED;
                break;
            case self::STATUSTYPE_INTERVIEW_WAITINGLIST:
                $status = self::STATUSTYPEDESC_INTERVIEW_WAITINGLIST;
                break;
            case self::STATUSTYPE_INTERVIEW_APPROVED:
                $status = self::STATUSTYPEDESC_INTERVIEW_APPROVED;
                break;
            case self::STATUSTYPE_INTERVIEW_DISAPPROVED:
                $status = self::STATUSTYPEDESC_INTERVIEW_DISAPPROVED;
                break;
            case self::STATUSTYPE_VOLUNTEER:
                $status = self::STATUSTYPEDESC_VOLUNTEER;
                break;
            case self::STATUSTYPE_CALLEDFOR_TESTCLASS:
                $status = self::STATUSTYPEDESC_CALLEDFOR_TESTCLASS;
                break;
            case self::STATUSTYPE_TESTCLASS_COMPLETE:
                $status = self::STATUSTYPEDESC_TESTCLASS_COMPLETE;
                break;
            case self::STATUSTYPE_TESTCLASS_WAITINGLIST:
                $status = self::STATUSTYPEDESC_TESTCLASS_WAITINGLIST;
                break;
            case self::STATUSTYPE_CONFIRMED:
                $status = self::STATUSTYPEDESC_CONFIRMED;
                break;
            case self::STATUSTYPE_CALLEDFOR_PREINTERVIEW:
                $status = self::STATUSTYPEDESC_CALLEDFOR_PREINTERVIEW;
                break;
            case self::STATUSTYPE_PREINTERVIEW_COMPLETE:
                $status = self::STATUSTYPEDESC_PREINTERVIEW_COMPLETE;
                break;
            default:
                $status = self::STATUSTYPEDESC_INVALID;
        }

        return $status;
    }

    public function getNumericStatusType()
    {
        return $this->statusType;
    }

    public function addRegStatus(RegistrationStatus $rs)
    {
        if (!$this->hasRegStatus($rs)) {
            $this->regStatus->add($rs);
        }
    }

    /**
     * 
     * @param RegistrationStatus $rs
     * @return bool
     */
    public function hasRegStatus(RegistrationStatus $rs)
    {
        return $this->regStatus->contains($rs);
    }

    /**
     * 
     * @param integer $sType
     */
    public static function statusTypeExists($sType)
    {
        if ($sType >= self::STATUSTYPE_REGISTERED && $sType <= self::STATUSTYPE_PREINTERVIEW_COMPLETE) {
            return true;
        }

        return false;
    }
}
