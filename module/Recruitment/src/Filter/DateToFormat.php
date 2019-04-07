<?php

namespace Recruitment\Filter;

use Zend\Filter\AbstractFilter;
use Zend\Stdlib\ArrayUtils;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DateConvertFormat
 *
 * @author marcio
 */
class DateToFormat extends AbstractFilter
{

    /**
     *
     * @var string
     */
    protected $inputFormat = 'd/m/Y';

    /**
     *
     * @var string
     */
    protected $outputFormat = 'Y-m-d';

    public function __construct($dataFormatOptions = null)
    {
        if ($dataFormatOptions instanceof Traversable) {
            $dataFormatOptions = ArrayUtils::iteratorToArray($dataFormatOptions);
        }

        if (!array_key_exists('inputFormat', $dataFormatOptions)) {
            $dataFormatOptions['inputFormat'] = $this->getInputFormat();
        }

        if (!array_key_exists('outputFormat', $dataFormatOptions)) {
            $dataFormatOptions['outputFormat'] = $this->getOutputFormat();
        }

        $this->setInputFormat($dataFormatOptions['inputFormat'])
                ->setOutputFormat($dataFormatOptions['outputFormat']);
    }

    /**
     *
     * @return string
     */
    function getInputFormat()
    {
        return $this->inputFormat;
    }

    /**
     *
     * @return string
     */
    function getOutputFormat()
    {
        return $this->outputFormat;
    }

    /**
     * Sets input date format
     * @param string $inputFormat
     * @return \DateConvertFormat
     */
    function setInputFormat($inputFormat)
    {
        $this->inputFormat = $inputFormat;
        return $this;
    }

    /**
     * Sets output date format
     * @param string $outputFormat
     * @return \DateConvertFormat
     */
    function setOutputFormat($outputFormat)
    {
        $this->outputFormat = $outputFormat;
        return $this;
    }

    /**
     * Defined by Zend\Filter\FilterInterface
     *
     * Returns the string $value, converting characters to lowercase as necessary
     *
     * If the value provided is non-scalar, the value will remain unfiltered
     *
     * @param  string $value
     * @return string|mixed
     */

    /**
     * Defined by Zend\Filter\FilterInterface
     *
     * Converts the date format from $this->inputFormat to $this->outputFormat
     * @param string $value
     * @return string|mixed
     */
    public function filter($value)
    {
        if (!is_string($value) || empty($value)) {
            return $value;
        }

        $dateValue = \DateTime::createFromFormat($this->inputFormat, $value);

        return $dateValue->format($this->outputFormat);
    }

}
