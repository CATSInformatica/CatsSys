<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Entity;

use Doctrine\ORM\Mapping as ORM;
use SchoolManagement\Entity\Enrollment;

/**
 * Permite a manipulação de mensalidades de alunos no banco de dados.
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="monthly_payment")
 * @ORM\Entity
 */
class MonthlyPayment
{

    /**
     * Meses do ano
     */
    const MONTH_JANUARY = 1;
    const MONTH_FEBRUARY = 2;
    const MONTH_MARCH = 3;
    const MONTH_APRIL = 4;
    const MONTH_MAY = 5;
    const MONTH_JUNE = 6;
    const MONTH_JULY = 7;
    const MONTH_AUGUST = 8;
    const MONTH_SEPTEMBER = 9;
    const MONTH_OCTOBER = 10;
    const MONTH_NOVEMBER = 11;
    const MONTH_DECEMBER = 12;

    /**
     * Tipos de pagamento
     */
    const PAYMENT_TYPE_PARTIAL = 'TOTAL';
    const PAYMENT_TYPE_TOTAL = 'PARCIAL';
    const PAYMENT_TYPE_FREE = 'ISENTO';

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
     * @var Enrollment
     * @ORM\ManyToOne(targetEntity="SchoolManagement\Entity\Enrollment")
     * @ORM\JoinColumn(name="enrollment_id", referencedColumnName="enrollment_id")
     */
    private $enrollment;

    /**
     *
     * @var int
     * @ORM\Column(name="monthly_payment_month", type="smallint", nullable=false)
     */
    private $monthlyPaymentMonth;

    /**
     * Tipo de pagamento.
     * 
     * @var string
     * @ORM\Column(name="monthly_payment_type", type="string", length=10)
     */
    private $monthlyPaymentType;

    /**
     *
     * @var string 
     * @ORM\Column(name="monthly_payment_observation", type="string", length=1000, nullable=true)
     */
    private $monthlyPaymentObservation;

    /**
     *
     * @var float
     * @ORM\Column(name="monthly_payment_value", type="float", nullable=false)
     */
    private $monthlyPaymentValue;

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
     * Retorna o tipo de pagamento da mesalidade.
     * 
     * @return int
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
     * Define o tipo de pagamento.
     * 
     * @param int $monthlyPaymentType
     * @return MonthlyPayment
     * @throws \InvalidArgumentException
     */
    public function setMonthlyPaymentType($monthlyPaymentType)
    {
        if (!in_array($monthlyPaymentType,
                [
                self::PAYMENT_TYPE_TOTAL,
                self::PAYMENT_TYPE_PARTIAL,
                self::PAYMENT_TYPE_FREE,
            ])) {
            throw new \InvalidArgumentException('O tipo de pagamento informado não é válido.');
        }
        $this->monthlyPaymentType = $monthlyPaymentType;
        return $this;
    }

    /**
     * 
     * @return Enrollment
     */
    public function getEnrollment()
    {
        return $this->enrollment;
    }

    /**
     * 
     * @param Enrollment $enrollment
     * @return Self
     */
    public function setEnrollment(Enrollment $enrollment)
    {
        $this->enrollment = $enrollment;
        return $this;
    }

    /**
     * Retorna o mês ao qual o pagamento corresponde.
     * 
     * @return int
     */
    public function getMonthlyPaymentMonth()
    {
        return $this->monthlyPaymentMonth;
    }

    /**
     * Define o mês ao qual o pagamento é referente.
     * 
     * @param int $monthlyPaymentMonth
     * @return \FinancialManagement\Entity\MonthlyPayment
     */
    public function setMonthlyPaymentMonth($monthlyPaymentMonth)
    {
        if (!in_array($monthlyPaymentMonth,
                [
                self::MONTH_JANUARY,
                self::MONTH_FEBRUARY,
                self::MONTH_MARCH,
                self::MONTH_APRIL,
                self::MONTH_MAY,
                self::MONTH_JUNE,
                self::MONTH_JULY,
                self::MONTH_AUGUST,
                self::MONTH_SEPTEMBER,
                self::MONTH_OCTOBER,
                self::MONTH_NOVEMBER,
                self::MONTH_DECEMBER
            ])) {
            throw new \InvalidArgumentException('A mês de pagamento deve ser um inteiro entre 1 e 12.');
        }
        $this->monthlyPaymentMonth = $monthlyPaymentMonth;

        return $this;
    }

    /**
     * Retorna o valor pago pelo aluno na mensalidade.
     * 
     * @return float
     */
    public function getMonthlyPaymentValue()
    {
        return $this->monthlyPaymentValue;
    }

    /**
     * Define o valor do pagamento.
     * 
     * @param float $monthlyPaymentValue
     * @return \FinancialManagement\Entity\MonthlyPayment
     */
    public function setMonthlyPaymentValue($monthlyPaymentValue)
    {
        $this->monthlyPaymentValue = $monthlyPaymentValue;
        return $this;
    }

}
