<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of CashFlow
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 * @ORM\Table(name="cash_flow")
 * @ORM\Entity
 */
class CashFlow
{
    /**
     *
     * @var integer 
     * @ORM\Column(name="cash_flow_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $cashFlowId;

    /**
     *
     * @var \DateTime 
     * @ORM\Column(name="cash_flow_date", type="date", nullable=false)
     */
    private $cashFlowDate;

    /**
     *
     * @var float 
     * @ORM\Column(name="cash_flow_amount", type="decimal", nullable=false)
     */
    private $cashFlowAmount;

    /**
     *
     * @var string 
     * @ORM\Column(name="cash_flow_description", type="string", length=1000, nullable=false)
     */
    private $cashFlowDescription;

    /**
     *
     * @var string 
     * @ORM\Column(name="cash_flow_observation", type="string", length=1000)
     */
    private $cashFlowObservation;

    /**
     *
     * @var AdministrativeStructure\Entity\Department 
     * @ORM\ManyToOne(targetEntity="AdministrativeStructure\Entity\Department")
     * @ORM\JoinColumn(name="department_id", referencedColumnName="department_id")
     */
    private $department;

    /**
     *
     * @var CashFlowType 
     * @ORM\ManyToOne(targetEntity="CashFlowType", inversedBy="cashFlows")
     * @ORM\JoinColumn(name="cash_flow_type", referencedColumnName="cash_flow_type_id")
     */
    private $cashFlowType;

    /**
     *
     * @var MonthlyBalance 
     * @ORM\ManyToOne(targetEntity="MonthlyBalance", inversedBy="cashFlows")
     * @ORM\JoinColumn(name="monthly_balance_id", referencedColumnName="monthly_balance_id")
     */
    private $monthlyBalance;
    
    /**
     * 
     * @return integer
     */
    public function getCashFlowId()
    {
        return $this->cashFlowId;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getCashFlowDate()
    {
        return $this->cashFlowDate;
    }

    /**
     * 
     * @return float
     */
    public function getCashFlowAmount()
    {
        return $this->cashFlowAmount;
    }

    /**
     * 
     * @return string
     */
    public function getCashFlowDescription()
    {
        return $this->cashFlowDescription;
    }

    /**
     * 
     * @return string
     */
    public function getCashFlowObservation()
    {
        return $this->cashFlowObservation;
    }

    /**
     * 
     * @return AdministrativeStructure\Entity\Department 
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * 
     * @return CashFlowType
     */
    public function getCashFlowType()
    {
        return $this->cashFlowType;
    }
    
    /**
     * 
     * @return MonthlyBalance
     */
    public function getMonthlyBalance()
    {
        return $this->monthlyBalance;
    }
    
    /**
     * 
     * @param \DateTime $cashFlowDate
     * @return CashFlow
     */
    public function setCashFlowDate(\DateTime $cashFlowDate)
    {
        $this->cashFlowDate = $cashFlowDate;
        return $this;
    }

    /**
     * 
     * @param float $cashFlowAmount
     * @return CashFlow
     */
    public function setCashFlowAmount($cashFlowAmount)
    {
        $this->cashFlowAmount = $cashFlowAmount;
        return $this;
    }

    /**
     * 
     * @param string $cashFlowDescription
     * @return CashFlow
     */
    public function setCashFlowDescription($cashFlowDescription)
    {
        $this->cashFlowDescription = $cashFlowDescription;
        return $this;
    }

    /**
     * 
     * @param string $cashFlowObservation
     * @return CashFlow
     */
    public function setCashFlowObservation($cashFlowObservation)
    {
        $this->cashFlowObservation = $cashFlowObservation;
        return $this;
    }

    /**
     * 
     * @param AdministrativeStructure\Entity\Department $department
     * @return CashFlow
     */
    public function setDepartment(AdministrativeStructure\Entity\Department $department)
    {
        $this->department = $department;
        return $this;
    }

    /**
     * 
     * @param CashFlowType $cashFlowType
     * @return CashFlow
     */
    public function setCashFlowType(CashFlowType $cashFlowType)
    {
        $this->cashFlowType = $cashFlowType;
        return $this;
    }

    /**
     * 
     * @param MonthlyBalance $monthlyBalance
     * @return CashFlow
     */
    public function setMonthlyBalance(MonthlyBalance $monthlyBalance)
    {
        $this->monthlyBalance = $monthlyBalance;
        return $this;
    }

}
