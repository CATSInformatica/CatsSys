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
     * @ORM\Column(name="cash_flow_description", type="string", nullable=false)
     */
    private $cashFlowDescription;

    /**
     *
     * @var string 
     * @ORM\Column(name="cash_flow_observation", type="string")
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
     * @return integer
     */
    function getCashFlowId()
    {
        return $this->cashFlowId;
    }

    /**
     * 
     * @return \DateTime
     */
    function getCashFlowDate()
    {
        return $this->cashFlowDate;
    }

    /**
     * 
     * @return float
     */
    function getCashFlowAmount()
    {
        return $this->cashFlowAmount;
    }

    /**
     * 
     * @return string
     */
    function getCashFlowDescription()
    {
        return $this->cashFlowDescription;
    }

    /**
     * 
     * @return string
     */
    function getCashFlowObservation()
    {
        return $this->cashFlowObservation;
    }

    /**
     * 
     * @return AdministrativeStructure\Entity\Department 
     */
    function getDepartment()
    {
        return $this->department;
    }

    /**
     * 
     * @return CashFlowType
     */
    function getCashFlowType()
    {
        return $this->cashFlowType;
    }

    /**
     * 
     * @param \DateTime $cashFlowDate
     * @return CashFlow
     */
    function setCashFlowDate(\DateTime $cashFlowDate)
    {
        $this->cashFlowDate = $cashFlowDate;
    }

    /**
     * 
     * @param float $cashFlowAmount
     * @return CashFlow
     */
    function setCashFlowAmount($cashFlowAmount)
    {
        $this->cashFlowAmount = $cashFlowAmount;
    }

    /**
     * 
     * @param string $cashFlowDescription
     * @return CashFlow
     */
    function setCashFlowDescription($cashFlowDescription)
    {
        $this->cashFlowDescription = $cashFlowDescription;
    }

    /**
     * 
     * @param string $cashFlowObservation
     * @return CashFlow
     */
    function setCashFlowObservation($cashFlowObservation)
    {
        $this->cashFlowObservation = $cashFlowObservation;
    }

    /**
     * 
     * @param AdministrativeStructure\Entity\Department $department
     * @return CashFlow
     */
    function setDepartment(AdministrativeStructure\Entity\Department $department)
    {
        $this->department = $department;
    }

    /**
     * 
     * @param CashFlowType $cashFlowType
     * @return CashFlow
     */
    function setCashFlowType(CashFlowType $cashFlowType)
    {
        $this->cashFlowType = $cashFlowType;
    }


}
