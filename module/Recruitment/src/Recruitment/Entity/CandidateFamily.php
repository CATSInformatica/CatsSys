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
 * Representa a tabela de membros do grupo familiar do candidato ao psa.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="candidate_family")
 * @ORM\Entity
 */
class CandidateFamily
{

    /**
     *
     * @var int
     * @ORM\Column(name="candidate_family_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $candidateFamilyId;

    /**
     *
     * @var string Nome da pessoa
     * @ORM\Column(name="candidate_family_name", type="string", length=100, nullable=false)
     */
    private $candidateFamilyName;

    /**
     *
     * @var int Idade
     * @ORM\Column(name="candidate_family_age", type="smallint", nullable=false)
     */
    private $candidateFamilyAge;

    const MA_STATUS_SINGLE = 'Solteiro(a)';
    const MA_STATUS_MARRIED = 'Casado(a) ou união estável';
    const MA_STATUS_WIDOWER = 'Viúvo(a)';
    const MA_STATUS_LSEPARETED = 'Separado(a) legalmente';
    const MA_STATUS_SEPARETED = 'Separado(a) sem legalização';

    /**
     *
     * @var string Estado civil
     * @ORM\Column(name="candidate_family_marital_status", type="string", length=50, nullable=false)
     */
    private $maritalStatus;

    const RELATIONSHIP_PARENTS = 'Pai/mãe';
    const RELATIONSHIP_PARENTINLAW = 'Padastro/madastra';
    const RELATIONSHIP_SIBLINGS = 'Irmão/Irmã';
    const RELATIONSHIP_WIFEHUSB = 'Cônjuge/Companheiro(a)';
    const RELATIONSHIP_GRANDPARENTS = 'Avô/Avó';
    const RELATIONSHIP_CHILDRENS = 'Filho';
    const RELATIONSHIP_OTHER = 'Outro';
    
    /**
     *
     * @var string Parentesco
     * @ORM\Column(name="candidate_family_relationship", type="string", length=100, nullable=false)
     */
    private $relationship;

    const SCHOLARITY_NONE = 'Sem escolaridade';
    const SCHOLARITY_ELEMENTARY_I = 'Fundamental I incompleto (até 5º ano)';
    const SCHOLARITY_ELEMENTARY_II = 'Fundamental I completo (até 5º ano)';
    const SCHOLARITY_ELEMENTARY_INCOMPLETE = 'Fundamental II incompleto (6º ao 9º ano)';
    const SCHOLARITY_ELEMENTARY_COMPLETE = 'Fundamental II completo (6º ao 9º ano)';
    const SCHOLARITY_HIGHSCHOOL_INCOMPLETE = 'Ensino médio incompleto';
    const SCHOLARITY_HIGHSCHOOL_COMPLETE = 'Ensino médio completo';
    const SCHOLARITY_UNDERGRADUATION_INCOMPLETE = 'Ensino superior incompleto';
    const SCHOLARITY_UNDERGRADUATION_COMPLETE = 'Ensino superior completo, especialização, mestrado ou doutorado';
    /**
     * 
     * @var string Escolaridade
     * @ORM\Column(name="candidate_family_scholarity", type="string", length=200, nullable=false)
     */
    private $scholarity;

    const WORK_SIT_NOTWORK = 'Não trabalha';
    const WORK_SIT_UNEMPLOYED = 'Desempregado(a)';
    const WORK_SIT_TRAINEE = 'Estagiário(a)';
    const WORK_SIT_FEMPLOYED = 'Trabalha com carteira assinada';
    const WORK_SIT_FREELANCER = 'Trabalhador autônomo';
    const WORK_SIT_PENSIONEER = 'Aposentado por tempo de serviço';
    const WORK_SIT_DPENSIONEER = 'Aposentado por invalidez';
    const WORK_SIT_AEMPLOYED = 'Afastado  (auxílio doença ou seguro acidente)';
    const WORK_SIT_EMPLOYER = 'Empregador';

    /**
     *
     * @var string Situação de trabalho
     * @ORM\Column(name="candidate_family_work_situation", length=60, nullable=false)
     */
    private $workSituation;

    /**
     * @var PreInterview
     * @ORM\ManyToOne(targetEntity="PreInterview", inversedBy="familyMembers")
     * @ORM\JoinColumn(name="pre_interview_id", referencedColumnName="pre_interview_id", nullable=false)
     */
    private $preInterview;

    /**
     * Busca pelo vetor de tipos de situação de trabalho.
     * 
     * @return array Tipos de situação de trabalho
     */
    public static function getWorkSituationArray()
    {
        return [
            self::WORK_SIT_NOTWORK => self::WORK_SIT_NOTWORK,
            self::WORK_SIT_UNEMPLOYED => self::WORK_SIT_UNEMPLOYED,
            self::WORK_SIT_TRAINEE => self::WORK_SIT_TRAINEE,
            self::WORK_SIT_FEMPLOYED => self::WORK_SIT_FEMPLOYED,
            self::WORK_SIT_FREELANCER => self::WORK_SIT_FREELANCER,
            self::WORK_SIT_PENSIONEER => self::WORK_SIT_PENSIONEER,
            self::WORK_SIT_DPENSIONEER => self::WORK_SIT_DPENSIONEER,
            self::WORK_SIT_AEMPLOYED => self::WORK_SIT_AEMPLOYED,
            self::WORK_SIT_EMPLOYER => self::WORK_SIT_EMPLOYER,
        ];
    }

    /**
     * Busca pelo vetor de tipos de estados civis
     * 
     * @return array Estados civis
     */
    public static function getMaritalStatusArray()
    {
        return [
            self::MA_STATUS_SINGLE => self::MA_STATUS_SINGLE,
            self::MA_STATUS_MARRIED => self::MA_STATUS_MARRIED,
            self::MA_STATUS_WIDOWER => self::MA_STATUS_WIDOWER,
            self::MA_STATUS_LSEPARETED => self::MA_STATUS_LSEPARETED,
            self::MA_STATUS_SEPARETED => self::MA_STATUS_SEPARETED,
        ];
    }

    public function getCandidateFamilyId()
    {
        return $this->candidateFamilyId;
    }

    public function getCandidateFamilyName()
    {
        return $this->candidateFamilyName;
    }

    public function setCandidateFamilyName($candidateFamilyName)
    {
        $this->candidateFamilyName = $candidateFamilyName;
        return $this;
    }

    public function getCandidateFamilyAge()
    {
        return $this->candidateFamilyAge;
    }

    public function setCandidateFamilyAge($candidateFamilyAge)
    {
        $this->candidateFamilyAge = $candidateFamilyAge;
        return $this;
    }

    public function getMaritalStatus()
    {
        return $this->maritalStatus;
    }

    public function setMaritalStatus($maritalStatus)
    {
        $this->maritalStatus = $maritalStatus;
        return $this;
    }

    public function getRelationship()
    {
        return $this->relationship;
    }
    
    /**
     * 
     * @return array Possibilidades de parentesco
     */
    public static function getRelationshipArray()
    {
        
        return [
            self::RELATIONSHIP_PARENTS => self::RELATIONSHIP_PARENTS,
            self::RELATIONSHIP_PARENTINLAW => self::RELATIONSHIP_PARENTINLAW,
            self::RELATIONSHIP_SIBLINGS => self::RELATIONSHIP_SIBLINGS,
            self::RELATIONSHIP_WIFEHUSB => self::RELATIONSHIP_WIFEHUSB,
            self::RELATIONSHIP_GRANDPARENTS => self::RELATIONSHIP_GRANDPARENTS,
            self::RELATIONSHIP_CHILDRENS => self::RELATIONSHIP_CHILDRENS,
            self::RELATIONSHIP_OTHER => self::RELATIONSHIP_OTHER,
        ];
    }

    public function setRelationship($relationship)
    {
        $this->relationship = $relationship;
        return $this;
    }

    public function getScholarity()
    {
        return $this->scholarity;
    }
    
    /**
     * 
     * @return array Possibilidades de escolaridade
     */
    public static function getScholarityArray()
    {
        return [
            self::SCHOLARITY_NONE => self::SCHOLARITY_NONE,
            self::SCHOLARITY_ELEMENTARY_I => self::SCHOLARITY_ELEMENTARY_I,
            self::SCHOLARITY_ELEMENTARY_II => self::SCHOLARITY_ELEMENTARY_II,
            self::SCHOLARITY_ELEMENTARY_INCOMPLETE => self::SCHOLARITY_ELEMENTARY_INCOMPLETE,
            self::SCHOLARITY_ELEMENTARY_COMPLETE => self::SCHOLARITY_ELEMENTARY_COMPLETE,
            self::SCHOLARITY_HIGHSCHOOL_INCOMPLETE => self::SCHOLARITY_HIGHSCHOOL_INCOMPLETE,
            self::SCHOLARITY_HIGHSCHOOL_COMPLETE => self::SCHOLARITY_HIGHSCHOOL_COMPLETE,
            self::SCHOLARITY_UNDERGRADUATION_INCOMPLETE => self::SCHOLARITY_UNDERGRADUATION_INCOMPLETE,
            self::SCHOLARITY_UNDERGRADUATION_COMPLETE => self::SCHOLARITY_UNDERGRADUATION_COMPLETE,
        ];
    }
    
    public function setScholarity($scholarity)
    {
        $this->scholarity = $scholarity;
        return $this;
    }

    public function getWorkSituation()
    {
        return $this->workSituation;
    }

    public function setWorkSituation($workSituation)
    {
        $this->workSituation = $workSituation;
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
