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
 * @ORM\Table(name="monthly_payment_type")
 * @ORM\Entity
 */
class MonthlyPaymentType
{
    
    /**
     *
     * @var integer 
     * @ORM\Column(name="monthly_payment_type_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $monthlyPaymentTypeId;

    /**
     *
     * @var string 
     * @ORM\Column(name="monthly_payment_type_name", type="string", length=90, nullable=false, unique=true)
     */
    private $cashFlowTypeName;

    /**
     *
     * @var string 
     * @ORM\Column(name="monthly_payment_type_description", type="string", length=200, nullable=false)
     */
    private $cashFlowTypeDescription;
    
    /**
     *
     * @var MonthlyPayment 
     * @ORM\OneToMany(targetEntity="MonthlyPayment", mappedBy="monthlyPaymentType")
     */
    private $monthlyPayments;
    
    public function __construct() {
        $this->monthlyPayments = new ArrayCollection();
    }

    /**
     * 
     * @return integer
     */
    public function getMonthlyPaymentTypeId()
    {
        return $this->monthlyPaymentTypeId;
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
     * @return Collection
     */
    public function getMonthlyPayments()
    {
        return $this->monthlyPayments;
    }

    /**
     * 
     * @param string $cashFlowTypeName
     * @return MonthlyPaymentType
     */
    public function setCashFlowTypeName($cashFlowTypeName)
    {
        $this->cashFlowTypeName = $cashFlowTypeName;
        return $this;
    }

    /**
     * 
     * @param string $cashFlowTypeDescription
     * @return MonthlyPaymentType
     */
    public function setCashFlowTypeDescription($cashFlowTypeDescription)
    {
        $this->cashFlowTypeDescription = $cashFlowTypeDescription;
        return $this;
    }

    /**
     * 
     * @param Collection $monthlyPayments
     * @return MonthlyPaymentType
     */
    public function setMonthlyPayments(Collection $monthlyPayments)
    {
        $this->monthlyPayments = $monthlyPayments;
        return $this;
    }
        
    /**
     * 
     * @param Collection $monthlyPayments
     * @return MonthlyPaymentType
     */
    public function addMonthlyPayment(Collection $monthlyPayments) 
    {
        foreach ($monthlyPayments as $monthlyPayment) {
            if (!$this->hasMonthlyPayment($monthlyPayment)) {
                $monthlyPayment->setMonthlyPaymentType($this);
                $this->monthlyPayments->add($monthlyPayment);
            }
        }
        return $this;
    }
    
    /**
     * 
     * @param Collection $monthlyPayments
     * @return MonthlyPaymentType
     */
    public function removeMonthlyPayment(Collection $monthlyPayments)
    {
        foreach ($monthlyPayments as $monthlyPayment) {
            $this->monthlyPayments->removeElement($monthlyPayment);
        }
        return $this;
    }
    
    /**
     * 
     * @param MonthlyPayment $monthlyPayment
     * @return bool
     */
    public function hasMonthlyPayment(MonthlyPayment $monthlyPayment)
    {
        return $this->monthlyPayments->contains($monthlyPayment);
    }

}
