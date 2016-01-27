<?php

namespace Recruitment\Entity;

use Doctrine\ORM\Mapping as ORM;
use Recruitment\Entity\Registration;

/**
 * Description of VolunteerInterview
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="student_interview")
 * @ORM\Entity
 */
class StudentInterview
{

    /**
     *
     * @var integer
     * @ORM\Column(name="student_interview_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY");
     */
    private $volunteerInterviewId;

    /**
     *
     * @var Registration
     * @ORM\OneToOne(targetEntity="Registration", mappedBy="studentInterview")
     */
    private $registration;

}
