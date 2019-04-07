<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    const CASH_FLOW_TYPE_MONTHLY_PAYMENT = 0;
    const CASH_FLOW_DIRECTION_OUTFLOW = 0;
    const CASH_FLOW_DIRECTION_INFLOW = 1;
    const CASH_FLOW_DIRECTION_OUTFLOW_DESCRIPTION = "DESPESA";
    const CASH_FLOW_DIRECTION_INFLOW_DESCRIPTION = "RECEITA";

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
     * @ORM\Column(name="cash_flow_type_description", type="string", length=1000, nullable=false)
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
    public function getCashFlowTypeId()
    {
        return $this->cashFlowTypeId;
    }

    /**
     *
     * @return string
     */
    public function getCashFlowTypeName()
    {
        return $this->cashFlowTypeName;
    }

    /**
     *
     * @return string
     */
    public function getCashFlowTypeDescription()
    {
        return $this->cashFlowTypeDescription;
    }

    /**
     *
     * @return integer
     */
    public function getCashFlowTypeDirection()
    {
        return $this->cashFlowTypeDirection;
    }

    /**
     *
     * @return Collection
     */
    public function getCashFlows()
    {
        return $this->cashFlows;
    }

    /**
     *
     * @param int $cashFlowTypeDirection
     * @return string
     */
    public static function getCashFlowTypeDirectionDescription($cashFlowTypeDirection)
    {
        if ($cashFlowTypeDirection === self::CASH_FLOW_DIRECTION_OUTFLOW) {
            return self::CASH_FLOW_DIRECTION_OUTFLOW_DESCRIPTION;
        } else {
            return self::CASH_FLOW_DIRECTION_INFLOW_DESCRIPTION;
        }
    }

    /**
     *
     * @param string $cashFlowTypeName
     * @return CashFlowType
     */
    public function setCashFlowTypeName($cashFlowTypeName)
    {
        $this->cashFlowTypeName = $cashFlowTypeName;
        return $this;
    }

    /**
     *
     * @param string $cashFlowTypeDescription
     * @return CashFlowType
     */
    public function setCashFlowTypeDescription($cashFlowTypeDescription)
    {
        $this->cashFlowTypeDescription = $cashFlowTypeDescription;
        return $this;
    }

    /**
     *
     * @param integer $cashFlowTypeDirection
     * @return CashFlowType
     */
    public function setCashFlowTypeDirection($cashFlowTypeDirection)
    {
        $this->cashFlowTypeDirection = $cashFlowTypeDirection;
        return $this;
    }

    /**
     *
     * @param Collection $cashFlows
     * @return CashFlowType
     */
    public function setCashFlows(Collection $cashFlows)
    {
        $this->cashFlows = $cashFlows;
        return $this;
    }

    /**
     *
     * @param Collection $cashFlows
     * @return CashFlowType
     */
    public function addCashFlows(Collection $cashFlows)
    {
        foreach ($cashFlows as $cashFlow) {
            if (!$this->hasCashFlow($cashFlow)) {
                $cashFlow->setCashFlowType($this);
                $this->cashFlows->add($cashFlow);
            }
        }
        return $this;
    }

    /**
     *
     * @param Collection $cashFlows
     * @return CashFlowType
     */
    public function removeCashFlows(Collection $cashFlows)
    {
        foreach ($cashFlows as $cashFlow) {
            $this->cashFlows->removeElement($cashFlow);
        }
        return $this;
    }

    /**
     *
     * @param CashFlow $cashFlow
     * @return bool
     */
    public function hasCashFlow(CashFlow $cashFlow)
    {
        return $this->cashFlows->contains($cashFlow);
    }

}
