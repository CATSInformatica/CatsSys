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
use Recruitment\Entity\Registration;

/**
 * Contém informações da entrevista de candidatos ao processo seletivo de alunos
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="student_interview")
 * @ORM\Entity
 */
class StudentInterview
{

    /**
     * Identificador da entrevista.
     * 
     * @var int
     * @ORM\Column(name="student_interview_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY");
     */
    private $volunteerInterviewId;

    /**
     * Associação 1:1 com o objeto de registro de inscrição.
     *
     * @var Registration
     * @ORM\OneToOne(targetEntity="Registration", mappedBy="studentInterview")
     */
    private $registration;

    /**
     * Nomes dos Entrevistadores separados por ";".
     * 
     * @var string
     * @ORM\Column(name="student_interview_interviewers", type="string", 
     * length=300, nullable=false)
     */
    private $interviewers;

    public function __construct()
    {
        
    }

    /**
     * 
     * @return int
     */
    public function getVolunteerInterviewId()
    {
        return $this->volunteerInterviewId;
    }

    /**
     * 
     * @return Registration
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * 
     * @param Registration $registration
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setRegistration(Registration $registration)
    {
        $this->registration = $registration;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getInterviewers()
    {
        return $this->interviewers;
    }

    /**
     * 
     * @param string $interviewers
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewers($interviewers)
    {
        $this->interviewers = $interviewers;
        return $this;
    }
}
