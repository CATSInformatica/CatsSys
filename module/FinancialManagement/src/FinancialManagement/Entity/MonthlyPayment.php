<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of MonthlyPayment
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 * @ORM\Table(name="monthly_payment")
 * @ORM\Entity
 */
class MonthlyPayment
{
    /**
     *
     * @var integer 
     * @ORM\Column(name="monthly_payment_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $monthlyPaymentId;

    /**
     *
     * @var \DateTime 
     * @ORM\Column(name="monthly_payment_date", type="date", nullable=false)
     */
    private $monthlyPaymentDate;

    /**
     *
     * @var string 
     * @ORM\Column(name="monthly_payment_observation", type="string", length=1000)
     */
    private $monthlyPaymentObservation;

    /**
     *
     * @var MonthlyPaymentType 
     * @ORM\ManyToOne(targetEntity="MonthlyPaymentType", inversedBy="monthlyPayments")
     * @ORM\JoinColumn(name="monthly_payment_type", referencedColumnName="monthly_payment_type_id")
     */
    private $monthlyPaymentType;
    
    /**
     * 
     * @return integer
     */
    public function getMonthlyPaymentId()
    {
        return $this->monthlyPaymentId;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getMonthlyPaymentDate()
    {
        return $this->monthlyPaymentDate;
    }

    /**
     * 
     * @return string
     */
    public function getMonthlyPaymentObservation()
    {
        return $this->monthlyPaymentObservation;
    }

    /**
     * 
     * @return MonthlyPaymentType
     */
    public function getMonthlyPaymentType()
    {
        return $this->monthlyPaymentType;
    }

    /**
     * 
     * @param \DateTime $monthlyPaymentDate
     * @return MonthlyPayment
     */
    public function setMonthlyPaymentDate(\DateTime $monthlyPaymentDate)
    {
        $this->monthlyPaymentDate = $monthlyPaymentDate;
        return $this;
    }

    /**
     * 
     * @param string $monthlyPaymentObservation
     * @return MonthlyPayment
     */
    public function setMonthlyPaymentObservation($monthlyPaymentObservation)
    {
        $this->monthlyPaymentObservation = $monthlyPaymentObservation;
        return $this;
    }

    /**
     * 
     * @param MonthlyPaymentType $monthlyPaymentType
     * @return MonthlyPayment
     */
    public function setMonthlyPaymentType(MonthlyPaymentType $monthlyPaymentType)
    {
        $this->monthlyPaymentType = $monthlyPaymentType;
        return $this;
    }


   
}
