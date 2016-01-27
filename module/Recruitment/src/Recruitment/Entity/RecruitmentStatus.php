<?php

namespace Recruitment\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Description of RecruitmentStatus
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="recruitment_status")
 * @ORM\Entity(readOnly=true)
 */
class RecruitmentStatus
{

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
                $status = 'INSCRITO';
                break;
            case self::STATUSTYPE_CALLEDFOR_INTERVIEW:
                $status = 'CONVOCADO PARA ENTREVISTA';
                break;
            case self::STATUSTYPE_CANCELED_REGISTRATION:
                $status = 'INSCRIÇÃO CANCELADA';
                break;
            case self::STATUSTYPE_INTERVIEWED:
                $status = 'ENTREVISTADO';
                break;
            case self::STATUSTYPE_INTERVIEW_WAITINGLIST:
                $status = 'LISTA DE ESPERA DA ENTREVISTA';
                break;
            case self::STATUSTYPE_INTERVIEW_APPROVED:
                $status = 'APROVADO EM ENTREVISTA';
                break;
            case self::STATUSTYPE_INTERVIEW_DISAPPROVED:
                $status = 'REPROVADO EM ENTREVISTA';
                break;
            case self::STATUSTYPE_VOLUNTEER:
                $status = 'VOLUNTÁRIO';
                break;
            case self::STATUSTYPE_CALLEDFOR_TESTCLASS:
                $status = 'CONVOCADO PARA AULA TESTE';
                break;
            case self::STATUSTYPE_TESTCLASS_COMPLETE:
                $status = 'AULA TESTE COMPLETA';
                break;
            case self::STATUSTYPE_TESTCLASS_WAITINGLIST:
                $status = 'LISTA DE ESPERA DA AULA TESTE';
                break;
            case self::STATUSTYPE_CONFIRMED:
                $status = 'CONFIRMADO';
                break;
            case self::STATUSTYPE_CALLEDFOR_PREINTERVIEW:
                $status = 'CONVOCADO PARA PRÉ-ENTREVISTA';
                break;
            case self::STATUSTYPE_PREINTERVIEW_COMPLETE:
                $status = 'PRÉ-ENTREVISTA CONCUÍDA';
                break;
            default:
                $status = 'INVÁLIDO';
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
