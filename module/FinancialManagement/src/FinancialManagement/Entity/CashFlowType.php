<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of CashFlowType
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 * @ORM\Table(name="cash_flow_type")
 * @ORM\Entity
 */
class CashFlowType
{
    const CASH_FLOW_DIRECTION_OUTFLOW = 0;
    const CASH_FLOW_DIRECTION_INFLOW = 1;
    
    /**
     *
     * @var integer 
     * @ORM\Column(name="cash_flow_type_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $cashFlowTypeId;

    /**
     *
     * @var string 
     * @ORM\Column(name="cash_flow_type_name", type="string", length=90, nullable=false, unique=true)
     */
    private $cashFlowTypeName;

    /**
     *
     * @var string 
     * @ORM\Column(name="cash_flow_type_description", type="string", nullable=false)
     */
    private $cashFlowTypeDescription;

    /**
     *
     * @var integer 
     * @ORM\Column(name="cash_flow_type_direction", type="integer", nullable=false)
     */
    private $cashFlowTypeDirection;

    /**
     *
     * @var CashFlow 
     * @ORM\OneToMany(targetEntity="CashFlow", mappedBy="cashFlowType", fetch="EXTRA_LAZY")
     */
    private $cashFlows;
    
    public function __construct() {
        $this->cashFlows = new ArrayCollection();
    }
    
    /**
     * 
     * @return integer
     */
    function getCashFlowTypeId()
    {
        return $this->cashFlowTypeId;
    }

    /**
     * 
     * @return string
     */
    function getCashFlowTypeName()
    {
        return $this->cashFlowTypeName;
    }

    /**
     * 
     * @return string
     */
    function getCashFlowTypeDescription()
    {
        return $this->cashFlowTypeDescription;
    }

    /**
     * 
     * @return integer
     */
    function getCashFlowTypeDirection()
    {
        return $this->cashFlowTypeDirection;
    }

    /**
     * 
     * @return Collection
     */
    function getCashFlows()
    {
        return $this->cashFlows;
    }

    /**
     * 
     * @param string $cashFlowTypeName
     * @return CashFlowType
     */
    function setCashFlowTypeName($cashFlowTypeName)
    {
        $this->cashFlowTypeName = $cashFlowTypeName;
        return $this;
    }

    /**
     * 
     * @param string $cashFlowTypeDescription
     * @return CashFlowType
     */
    function setCashFlowTypeDescription($cashFlowTypeDescription)
    {
        $this->cashFlowTypeDescription = $cashFlowTypeDescription;
        return $this;
    }

    /**
     * 
     * @param integer $cashFlowTypeDirection
     * @return CashFlowType
     */
    function setCashFlowTypeDirection($cashFlowTypeDirection)
    {
        $this->cashFlowTypeDirection = $cashFlowTypeDirection;
        return $this;
    }

    /**
     * 
     * @param CashFlow $cashFlows
     * @return CashFlowType
     */
    function setCashFlows(CashFlow $cashFlows)
    {
        $this->cashFlows = $cashFlows;
        return $this;
    }


}
