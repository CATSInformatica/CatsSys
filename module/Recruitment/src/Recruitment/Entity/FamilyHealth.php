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

/**
 * Representa a tabela que lista possíveis problemas de saúde na família do candidato ao psa.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="family_health")
 * @ORM\Entity
 */
class FamilyHealth
{

    /**
     * Identificador do objeto.
     * 
     * @var int Identificador do objeto
     * @ORM\Column(name="family_health_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $familyHealthId;

    /**
     * Nome do membro da familia que possui algum problema de saúde.
     * 
     * @var string Nome do membro da familia que possui algum problema de saúde
     * @ORM\Column(name="family_health_name", type="string", length=100, nullable=false)
     */
    private $familyHealthName;

    /**
     * Problema de saúde.
     *
     * @var string Problema de saúde
     * @ORM\Column(name="family_health_hproblem", type="string", length=150, nullable=false)
     */
    private $healthProblem;

    /**
     * O problema impede a pessoa de trabalhar?
     *
     * @var bool Se sim true senão false.
     * @ORM\Column(name="family_health_dforwork", type="boolean", nullable=false)
     */
    private $disableForWork;

    /**
     * A pessoa precisa de acompanhamento diário?
     *
     * @var bool Se sim true senão false.
     * @ORM\Column(name="family_health_daily_dependency", type="boolean", nullable=false)
     */
    private $dailyDependency;

    /**
     * Relacionamento N:1 com o PreInterview.
     * 
     * @var PreInterview Relacionamento N:1 com o PreInterview
     * @ORM\ManyToOne(targetEntity="PreInterview", inversedBy="familyHealth")
     * @ORM\JoinColumn(name="pre_interview_id", referencedColumnName="pre_interview_id", nullable=false)
     */
    private $preInterview;

    public function getFamilyHealthId()
    {
        return $this->familyHealthId;
    }

    public function getFamilyHealthName()
    {
        return $this->familyHealthName;
    }

    public function setFamilyHealthName($familyHealthName)
    {
        $this->familyHealthName = $familyHealthName;
        return $this;
    }

    public function getHealthProblem()
    {
        return $this->healthProblem;
    }

    public function setHealthProblem($healthProblem)
    {
        $this->healthProblem = $healthProblem;
        return $this;
    }

    public function getDisableForWork()
    {
        return $this->disableForWork;
    }

    public function setDisableForWork($disableForWork)
    {
        $this->disableForWork = $disableForWork;
        return $this;
    }

    public function getDailyDependency()
    {
        return $this->dailyDependency;
    }

    public function setDailyDependency($dailyDependency)
    {
        $this->dailyDependency = $dailyDependency;
        return $this;
    }

    public function getPreInterview()
    {
        return $this->preInterview;
    }

    public function setPreInterview(PreInterview $preInterview = null)
    {
        $this->preInterview = $preInterview;
        return $this;
    }
}
