<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Expense
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 * @ORM\Table(name="expense")
 * @ORM\Entity
 */
class Expense
{

    /**
     *
     * @var integer 
     * @ORM\Column(name="expense_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $expenseId;

    /**
     *
     * @var integer 
     * @ORM\Column(name="expense_year", type="integer", nullable=false)
     */
    private $expenseYear;

    /**
     *
     * @var integer 
     * @ORM\Column(name="expense_month", type="integer", nullable=false)
     */
    private $expenseMonth;

    /**
     *
     * @var decimal 
     * @ORM\Column(name="expense_amount", type="decimal", nullable=false)
     */
    private $expenseAmount;

    /**
     *
     * @var string 
     * @ORM\Column(name="expense_description", type="string", nullable=false)
     */
    private $expenseDescription;

    /**
     *
     * @var boolean 
     * @ORM\Column(name="expense_is_fixed", type="boolean", nullable=false)
     */
    private $expenseIsFixed;

    /**
     * 
     * @return integer
     */
    function getExpenseId()
    {
        return $this->expenseId;
    }

    /**
     * 
     * @return integer
     */
    function getExpenseYear()
    {
        return $this->expenseYear;
    }

    /**
     * 
     * @return integer
     */
    function getExpenseMonth()
    {
        return $this->expenseMonth;
    }

    /**
     * 
     * @return double
     */
    function getExpenseAmount()
    {
        return $this->expenseAmount;
    }

    /**
     * 
     * @return string
     */
    function getExpenseDescription()
    {
        return $this->expenseDescription;
    }

    /**
     * 
     * @return string
     */
    function getExpenseIsFixed()
    {
        return $this->expenseDescription;
    }

    /**
     * 
     * @param integer $expenseYear
     * @return \FinancialManagement\Entity\Expense
     */
    function setExpenseYear($expenseYear)
    {
        $this->expenseYear = $expenseYear;
        return $this;
    }

    /**
     * 
     * @param integer $expenseMonth
     * @return \FinancialManagement\Entity\Expense
     */
    function setExpenseMonth($expenseMonth)
    {
        $this->expenseMonth = $expenseMonth;
        return $this;
    }

    /**
     * 
     * @param double $expenseAmount
     * @return \FinancialManagement\Entity\Expense
     */
    function setExpenseAmount($expenseAmount)
    {
        $this->expenseAmount = number_format($expenseAmount, 8, '.');
        return $this;
    }

    /**
     * 
     * @param string $expenseDescription
     * @return \FinancialManagement\Entity\Expense
     */
    function setExpenseDescription($expenseDescription)
    {
        $this->expenseDescription = $expenseDescription;
        return $this;
    }

    /**
     * 
     * @param boolean $expenseIsFixed
     * @return \FinancialManagement\Entity\Expense
     */
    function setExpenseIsFixed($expenseIsFixed)
    {
        $this->expenseDescription = $expenseIsFixed;
        return $this;
    }

}
