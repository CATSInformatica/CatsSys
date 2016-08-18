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
 * Representa o a tabela de despesas da família do candidato do psa.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="family_income_expense")
 * @ORM\Entity
 */
class FamilyIncomeExpense
{

    /**
     * Identificador.
     * 
     * @var int Identificador
     * @ORM\Column(name="family_income_expense_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $familyIncomeExpId;

    /**
     * Valor da despesa.
     * 
     * @var float Valor da despesa
     * @ORM\Column(name="family_income_expense_value", type="float", nullable=false)
     */
    private $familyIncomeExpValue;

    /**
     * Descrição breve da despesa.
     *
     * @var string Descrição breve da despesa
     * @ORM\Column(name="family_income_expense_description", type="string", length=500, nullable=false)
     */
    private $familyIncomeExpDescription;

    /**
     * Relacionamento N:1 com o PreInterview.
     * 
     * @var PreInterview Relacionamento N:1 com o PreInterview
     * @ORM\ManyToOne(targetEntity="PreInterview", inversedBy="familyExpenses")
     * @ORM\JoinColumn(name="pre_interview_id_exp", referencedColumnName="pre_interview_id", nullable=true)
     */
    private $preInterviewExpense;

    /**
     * Relacionamento N:1 com o PreInterview.
     * 
     * @var PreInterview Relacionamento N:1 com o PreInterview
     * @ORM\ManyToOne(targetEntity="PreInterview", inversedBy="familyIncome")
     * @ORM\JoinColumn(name="pre_interview_id_inc", referencedColumnName="pre_interview_id", nullable=true)
     */
    private $preInterviewIncome;

    public function getFamilyIncomeExpId()
    {
        return $this->familyIncomeExpId;
    }

    public function getFamilyIncomeExpValue()
    {
        return $this->familyIncomeExpValue;
    }

    public function setFamilyIncomeExpValue($familyIncomeExpValue)
    {
        $this->familyIncomeExpValue = $familyIncomeExpValue;
        return $this;
    }

    public function getFamilyIncomeExpDescription()
    {
        return $this->familyIncomeExpDescription;
    }

    public function setFamilyIncomeExpDescription($familyIncomeExpDescription)
    {
        $this->familyIncomeExpDescription = $familyIncomeExpDescription;
        return $this;
    }

    public function getPreInterviewExpense()
    {
        return $this->preInterviewExpense;
    }

    public function setPreInterviewExpense(
    PreInterview $preInterviewExpense = null)
    {
        $this->preInterviewExpense = $preInterviewExpense;
        return $this;
    }

    public function getPreInterviewIncome()
    {
        return $this->preInterviewIncome;
    }

    public function setPreInterviewIncome(
    PreInterview $preInterviewIncome = null)
    {
        $this->preInterviewIncome = $preInterviewIncome;
        return $this;
    }
}
