<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Revenue
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 * @ORM\Table(name="revenue")
 * @ORM\Entity
 */
class Revenue
{
    /**
     *
     * @var integer 
     * @ORM\Column(name="revenue_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $revenueId;

    /**
     *
     * @var integer 
     * @ORM\Column(name="revenue_year", type="integer", nullable=false)
     */
    private $revenueYear;

    /**
     *
     * @var integer 
     * @ORM\Column(name="revenue_month", type="integer", nullable=false)
     */
    private $revenueMonth;

    /**
     *
     * @var decimal 
     * @ORM\Column(name="revenue_amount", type="decimal", nullable=false)
     */
    private $revenueAmount;

    /**
     *
     * @var string 
     * @ORM\Column(name="revenue_description", type="string", nullable=false)
     */
    private $revenueDescription;

    /**
     *
     * @var boolean 
     * @ORM\Column(name="revenue_is_fixed", type="boolean", nullable=false)
     */
    private $revenueIsFixed;

    /**
     * 
     * @return integer
     */
    function getRevenueId()
    {
        return $this->revenueId;
    }

    /**
     * 
     * @return integer
     */
    function getRevenueYear()
    {
        return $this->revenueYear;
    }

    /**
     * 
     * @return integer
     */
    function getRevenueMonth()
    {
        return $this->revenueMonth;
    }

    /**
     * 
     * @return double
     */
    function getRevenueAmount()
    {
        return $this->revenueAmount;
    }

    /**
     * 
     * @return string
     */
    function getRevenueDescription()
    {
        return $this->revenueDescription;
    }

    /**
     * 
     * @return boolean
     */
    function getRevenueIsFixed()
    {
        return $this->revenueIsFixed;
    }

    /**
     * 
     * @param integer $revenueId
     * @return \FinancialManagement\Entity\Revenue
     */
    function setRevenueId($revenueId)
    {
        $this->revenueId = $revenueId;
        return $this;
    }

    /**
     * 
     * @param integer $revenueYear
     * @return \FinancialManagement\Entity\Revenue
     */
    function setRevenueYear($revenueYear)
    {
        $this->revenueYear = $revenueYear;
        return $this;
    }

    /**
     * 
     * @param integer $revenueMonth
     * @return \FinancialManagement\Entity\Revenue
     */
    function setRevenueMonth($revenueMonth)
    {
        $this->revenueMonth = $revenueMonth;
        return $this;
    }

    /**
     * 
     * @param double $revenueAmount
     * @return \FinancialManagement\Entity\Revenue
     */
    function setRevenueAmount($revenueAmount)
    {
        $this->revenueAmount = number_format($revenueAmount, 8, '.');;
        return $this;
    }

    /**
     * 
     * @param string $revenueDescription
     * @return \FinancialManagement\Entity\Revenue
     */
    function setRevenueDescription($revenueDescription)
    {
        $this->revenueDescription = $revenueDescription;
        return $this;
    }

    /**
     * 
     * @param boolean $revenueIsFixed
     * @return \FinancialManagement\Entity\Revenue
     */
    function setRevenueIsFixed($revenueIsFixed)
    {
        $this->revenueIsFixed = $revenueIsFixed;
        return $this;
    }


}
