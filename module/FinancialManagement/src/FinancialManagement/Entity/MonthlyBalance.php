<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of MonthlyBalance
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 * @ORM\Table(name="monthly_balance")
 * @ORM\Entity
 */
class MonthlyBalance
{
    /**
     *
     * @var integer 
     * @ORM\Column(name="monthly_balance_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $monthlyBalanceId;

    /**
     *
     * @var \DateTime 
     * @ORM\Column(name="monthly_balance_open", type="date", nullable=false)
     */
    private $monthlyBalanceOpen;

    /**
     *
     * @var \DateTime 
     * @ORM\Column(name="monthly_balance_close", type="date", nullable=false)
     */
    private $monthlyBalanceClose;

    /**
     *
     * @var float 
     * @ORM\Column(name="monthly_balance_projected_revenue", type="decimal", nullable=false)
     */
    private $monthlyBalanceProjectedRevenue;

    /**
     *
     * @var float 
     * @ORM\Column(name="monthly_balance_projected_expense", type="decimal", nullable=false)
     */
    private $monthlyBalanceProjectedExpense;

    /**
     *
     * @var float 
     * @ORM\Column(name="monthly_balance_revenue", type="decimal", nullable=false)
     */
    private $monthlyBalanceRevenue;

    /**
     *
     * @var float 
     * @ORM\Column(name="monthly_balance_expense", type="decimal", nullable=false)
     */
    private $monthlyBalanceExpense;

    /**
     *
     * @var boolean 
     * @ORM\Column(name="monthly_balance_is_open", type="boolean", nullable=false)
     */
    private $monthlyBalanceIsOpen;

    /**
     *
     * @var string 
     * @ORM\Column(name="monthly_balance_observation", type="string", length=1000)
     */
    private $monthlyBalanceObservation;

    /**
     *
     * @var Collection 
     * @ORM\OneToMany(targetEntity="CashFlow", mappedBy="monthlyBalance", fetch="EXTRA_LAZY")
     */
    private $cashFlows;
    
    public function __construct() {
        $this->cashFlows = new ArrayCollection();
    }
    
    /**
     * 
     * @return integer
     */
    public function getMonthlyBalanceId()
    {
        return $this->monthlyBalanceId;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getMonthlyBalanceOpen()
    {
        return $this->monthlyBalanceOpen;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getMonthlyBalanceClose()
    {
        return $this->monthlyBalanceClose;
    }

    /**
     * 
     * @return float
     */
    public function getMonthlyBalanceProjectedRevenue()
    {
        return $this->monthlyBalanceProjectedRevenue;
    }

    /**
     * 
     * @return float
     */
    public function getMonthlyBalanceProjectedExpense()
    {
        return $this->monthlyBalanceProjectedExpense;
    }

    /**
     * 
     * @return float
     */
    public function getMonthlyBalanceRevenue()
    {
        return $this->monthlyBalanceRevenue;
    }

    /**
     * 
     * @return float
     */
    public function getMonthlyBalanceExpense()
    {
        return $this->monthlyBalanceExpense;
    }

    /**
     * 
     * @return bool
     */
    public function getMonthlyBalanceIsOpen()
    {
        return $this->monthlyBalanceIsOpen;
    }

    /**
     * 
     * @return string
     */
    public function getMonthlyBalanceObservation()
    {
        return $this->monthlyBalanceObservation;
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
     * @param \DateTime $monthlyBalanceOpen
     * @return MonthlyBalance
     */
    public function setMonthlyBalanceOpen(\DateTime $monthlyBalanceOpen)
    {
        $this->monthlyBalanceOpen = $monthlyBalanceOpen;
        return $this;
    }

    /**
     * 
     * @param \DateTime $monthlyBalanceClose
     * @return MonthlyBalance
     */
    public function setMonthlyBalanceClose(\DateTime $monthlyBalanceClose)
    {
        $this->monthlyBalanceClose = $monthlyBalanceClose;
        return $this;
    }

    /**
     * 
     * @param float $monthlyBalanceProjectedRevenue
     * @return MonthlyBalance
     */
    public function setMonthlyBalanceProjectedRevenue($monthlyBalanceProjectedRevenue)
    {
        $this->monthlyBalanceProjectedRevenue = $monthlyBalanceProjectedRevenue;
        return $this;
    }

    /**
     * 
     * @param float $monthlyBalanceProjectedExpense
     * @return MonthlyBalance
     */
    public function setMonthlyBalanceProjectedExpense($monthlyBalanceProjectedExpense)
    {
        $this->monthlyBalanceProjectedExpense = $monthlyBalanceProjectedExpense;
        return $this;
    }

    /**
     * 
     * @param float $monthlyBalanceRevenue
     * @return MonthlyBalance
     */
    public function setMonthlyBalanceRevenue($monthlyBalanceRevenue)
    {
        $this->monthlyBalanceRevenue = $monthlyBalanceRevenue;
        return $this;
    }

    /**
     * 
     * @param float $monthlyBalanceExpense
     * @return MonthlyBalance
     */
    public function setMonthlyBalanceExpense($monthlyBalanceExpense)
    {
        $this->monthlyBalanceExpense = $monthlyBalanceExpense;
        return $this;
    }

    /**
     * 
     * @param bool $monthlyBalanceIsOpen
     * @return MonthlyBalance
     */
    public function setMonthlyBalanceIsOpen($monthlyBalanceIsOpen)
    {
        $this->monthlyBalanceIsOpen = $monthlyBalanceIsOpen;
        return $this;
    }

    /**
     * 
     * @param string $monthlyBalanceObservation
     * @return MonthlyBalance
     */
    public function setMonthlyBalanceObservation($monthlyBalanceObservation)
    {
        $this->monthlyBalanceObservation = $monthlyBalanceObservation;
        return $this;
    }

    /**
     * 
     * @param Collection $cashFlows
     * @return MonthlyBalance
     */
    public function setCashFlows(Collection $cashFlows)
    {
        $this->cashFlows = $cashFlows;
        return $this;
    }

    /**
     * 
     * @param Collection $cashFlows
     * @return MonthlyBalance
     */
    public function addCashFlows(Collection $cashFlows) 
    {
        foreach ($cashFlows as $cashFlow) {
            if (!$this->hasCashFlow($cashFlow)) {
                $cashFlow->setMonthlyBalance($this);
                $this->cashFlows->add($cashFlow);
            }
        }
        return $this;
    }
    
    /**
     * 
     * @param Collection $cashFlows
     * @return MonthlyBalance
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
