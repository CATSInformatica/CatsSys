<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Validator;

use Zend\Validator\AbstractValidator;
use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * Description of DateCompare
 *
 * @author marcio
 */
class DateGratherThan extends AbstractValidator
{

    const NOT_GREATER = 'notGreaterThan';
    const NOT_GREATER_INCLUSIVE = 'notGreaterThanInclusive';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_GREATER => "The input is not greater than '%minimumName%'",
        self::NOT_GREATER_INCLUSIVE => "The input is not greater or equal than '%min%'"
    ];

    /**
     * @var array
     */
    protected $messageVariables = [
        'minimumName' => 'minimumName'
    ];

    /**
     * \DateTime string format
     * @var string
     */
    protected $format;

    /**
     * \DateTime string format
     * @var string
     */
    protected $compareWithFormat;

    /**
     * Minimum date field name
     *
     * @var string
     */
    protected $minimumName;

    /**
     * Minimum date format
     *
     * @var string
     */
    protected $minimumFormat;

    /**
     * Whether to do inclusive comparisons, allowing equivalence to max
     *
     * If false, then strict comparisons are done, and the value may equal
     * the minimum option
     *
     * @var bool
     */
    protected $inclusive;

    /**
     * Sets validator options
     *
     * @param  array|Traversable $options
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (!array_key_exists('format', $options)) {
            throw new Exception\InvalidArgumentException("Missing option 'format'");
        }

        if (array_key_exists('compareWith', $options)) {
            if (!array_key_exists('name', $options['compareWith'])) {
                $minimumName = null;
            } else {
                $minimumName = $options['compareWith']['name'];
            }

            if (!array_key_exists('format', $options['compareWith'])) {
                $minimumFormat = $options['format'];
            } else {
                $minimumFormat = $options['compareWith']['format'];
            }
        } else {
            $minimumName = null;
            $minimumFormat = null;
        }

        if (!array_key_exists('inclusive', $options)) {
            $options['inclusive'] = false;
        }

        $this->setFormat($options['format'])
                ->setMinimumName($minimumName)
                ->setMinimumFormat($minimumFormat)
                ->setInclusive($options['inclusive']);

        parent::__construct($options);
    }

    /**
     * Returns the \DateTime format
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Sets the \DateTime format
     *
     * @return DateGreaterThan Provides a fluent interface
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Returns the min option name
     *
     * @return string
     */
    public function getMinimumName()
    {
        return $this->minimumName;
    }

    /**
     * Sets the min option
     *
     * @param  mixed $min
     * @return DateGreaterThan Provides a fluent interface
     */
    public function setMinimumName($min)
    {
        $this->minimumName = $min;
        return $this;
    }

    /**
     * Returns the min option format
     *
     * @return string
     */
    public function getMinimumFormat()
    {
        return $this->minimumFormat;
    }

    /**
     * Sets the min option format
     *
     * @param  string $format
     * @return DateGreaterThan Provides a fluent interface
     */
    public function setMinimumFormat($format)
    {
        $this->minimumFormat = $format;
        return $this;
    }

    /**
     * Returns the inclusive option
     *
     * @return bool
     */
    public function getInclusive()
    {
        return $this->inclusive;
    }

    /**
     * Sets the inclusive option
     *
     * @param  bool $inclusive
     * @return DateGreaterThan Provides a fluent interface
     */
    public function setInclusive($inclusive)
    {
        $this->inclusive = $inclusive;
        return $this;
    }

    /**
     * Returns true if and only if date $value is greater than min date option
     * or the current date, if date option was not specified
     *
     * @param  mixed $value
     * @return bool
     */

    /**
     * Returns true if and only if date $value is greater than $context[$this->min] date option
     * or the current date, if no date option was specified
     * @param string $value
     * @param array $context field to be compared with
     * @return boolean
     */
    public function isValid($value, $context = null)
    {

        $this->setValue($value);

        if ($this->minimumName !== null) {
            $dateMin = \DateTime::createFromFormat(
                            $this->minimumFormat, $context[$this->minimumName]
            );
        } else {
            $dateMin = new \DateTime('now');
        }
        $dateValue = \DateTime::createFromFormat($this->format, $value);

        if ($this->inclusive) {
            if ($dateMin >= $dateValue) {
                $this->error(self::NOT_GREATER_INCLUSIVE);
                return false;
            }
        } else {
            if ($dateMin > $dateValue) {
                $this->error(self::NOT_GREATER);
                return false;
            }
        }

        return true;
    }

}
