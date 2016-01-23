<?php

namespace Recruitment\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of RegistrationStatus
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="registration_status")
 * @ORM\Entity(readOnly=true)
 */
class RegistrationStatus
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

    /**
     *
     * @var integer
     * @ORM\Column(name="registration_status_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $statusId;

    /**
     *
     * @var DateTime
     * @ORM\Column(name="registration_status_datetime", type="datetime", nullable=false)
     */
    private $statusDate;

    /**
     * @var integer
     * @ORM\Column(name="registration_status_type", type="smallint", nullable=false, unique=true)
     */
    private $statusType;

}
