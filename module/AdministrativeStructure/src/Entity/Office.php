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

namespace AdministrativeStructure\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Recruitment\Entity\Registration;

/**
 * Mapeamento da tabela `office`
 *
 * Classe de Associação de cargos com voluntários.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 *
 * @ORM\Table(name="office")
 * @ORM\Entity
 */
class Office
{

    /**
     * @var integer
     * @ORM\Column(name="office_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $officeId;

    /**
     * @var Recruitment\Entity\Registration
     * @ORM\ManyToOne(targetEntity="Recruitment\Entity\Registration")
     * @ORM\JoinColumn(name="registration", referencedColumnName="registration_id", nullable=false)
     */
    protected $registration;

    /**
     * @var Job
     * @ORM\ManyToOne(targetEntity="Job", inversedBy="offices")
     * @ORM\JoinColumn(name="job", referencedColumnName="job_id", nullable=false)
     */
    protected $job;

    /**
     * @var DateTime
     * @ORM\Column(name="office_begin", type="date", nullable=false)
     */
    protected $begin;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="office_end", type="date", nullable=true)
     */
    protected $end;

    public function __construct()
    {
        $this->begin = new DateTime();
    }

    /**
     * Define a pessoa que recebe o cargo.
     *
     * @param Registration $registration Voluntário que ganhará o cargo
     * @return Self
     */
    public function setRegistration(Registration $registration)
    {
        $this->registration = $registration;
        return $this;
    }

    /**
     * Define qual o trabalho do cargo.
     *
     * @param AdministrativeStructure\Entity\Job $job Trabalho ao qual o cargo estará associado
     * @return Self
     */
    public function setJob(Job $job)
    {
        $job->addOffices(new ArrayCollection([$this]));
        $this->job = $job;
        return $this;
    }

    /**
     * Finaliza o cargo.
     *
     * @param DateTime $date Data de saída do cargo
     * @return \AdministrativeStructure\Entity\Office
     */
    public function setEnd(\DateTime $date)
    {
        $this->end = $date;
        return $this;
    }

    /**
     * Retorna o trabalho associado ao cargo.
     *
     * @return Job
     */
    public function getJob()
    {
        return $this->job;
    }

}
