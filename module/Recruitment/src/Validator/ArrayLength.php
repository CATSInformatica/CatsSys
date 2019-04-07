<?php

namespace Recruitment\Validator;

use Zend\Validator\AbstractValidator;

/**
 * Description of ArrayLength
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class ArrayLength extends AbstractValidator
{

    const NOT_AT_LEAST = "atLeast";
    const NOT_EXACTLY = "exactly";

    /**
     * Validation failure message template definitions
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_AT_LEAST => "choose at least '%atLeast%'",
        self::NOT_EXACTLY => "choose exactly '%exactly%'",
    );

    /**
     * @var array
     */
    protected $messageVariables = [
        'atLeast' => 'atLeast',
        'exactly' => 'exactly',
    ];

    /**
     * @var integer
     */
    protected $atLeast;

    /**
     * @var integer
     */
    protected $exactly;

    public function __construct($options = null)
    {
        parent::__construct($options);

        if (is_array($options)) {
            if (array_key_exists('exactly', $options)) {
                $this->setExactly($options['exactly']);
            } else if (array_key_exists('atLeast', $options)) {
                $this->setAtLeast($options['atLeast']);
            } else {
                throw new \InvalidArgumentException('the `options` array doesn\'t contain '
                . 'either `exactly` or `atLeast` keys.');
            }
        } else {
            throw new \InvalidArgumentException('`options` argument must be an array with '
            . 'keys `exactly` or `atLeast`');
        }
    }

    /**
     *
     * @return integer
     */
    public function getExactly()
    {
        return $this->exactly;
    }

    /**
     * @param integer $exactly
     * @return \Recruitment\Validator\ArrayLength
     */
    public function setExactly($exactly)
    {
        $this->exactly = $exactly;
        return $this;
    }

    /**
     * @return integer
     */
    public function getAtLeast()
    {
        return $this->atLeast;
    }

    /**
     * @param type $atLeast
     * @return Recruitment\Validator\ArrayLength
     */
    public function setAtLeast($atLeast)
    {
        $this->atLeast = $atLeast;
        return $this;
    }

    public function isValid($value)
    {
        if (is_array($value)) {
            $size = count($value);

            if (isset($this->exactly) && ($size != $this->exactly)) {
                $this->error(self::NOT_EXACTLY);
                return false;
            } else if (isset($this->atLeast) && ($size < $this->atLeast)) {
                $this->error(self::NOT_AT_LEAST);
                return false;
            }
        }

        return true;
    }

//put your code here
}
