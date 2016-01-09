<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\View\Helper;

use Recruitment\Model\CaptchaImage as CaptchaAdapter;
use Zend\Form\ElementInterface;
use Zend\Form\Exception\DomainException;
use Zend\Form\View\Helper\Captcha\AbstractWord;

/**
 * Description of CaptchaViewHeper
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class CaptchaImage extends AbstractWord
{

    /**
     * Override
     * 
     * Render the captcha
     *
     * @param  ElementInterface $element
     * @throws Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element)
    {
        //we could also set the separator here to break between the inputs and the image.
        //$this->setSeparator('')
        $captcha = $element->getCaptcha();

        if ($captcha === null || !$captcha instanceof CaptchaAdapter) {
            throw new DomainException(sprintf(
                '%s requires that the element has a "captcha" attribute of type Recruitment\Model\CaptchaImage;
        none found', __METHOD__
            ));
        }

        $captcha->generate();

        $imgAttributes = array(
            'width' => $captcha->getWidth(),
            'height' => $captcha->getHeight(),
            'alt' => $captcha->getImgAlt(),
            'src' => $captcha->getImgUrl() . $captcha->getId() . $captcha->getSuffix(),
            'class' => 'thumbnail center-block',
        );
        if ($element->hasAttribute('id')) {
            $imgAttributes['id'] = $element->getAttribute('id') . '-image';
        }

        $closingBracket = $this->getInlineClosingBracket();
        $img = sprintf(
            '<img  %s%s ', $this->createAttributesString($imgAttributes), $closingBracket
        );

        $position = $this->getCaptchaPosition();
        $separator = $this->getSeparator();
        $captchaInput = $this->renderCaptchaInputs($element);


        $pattern = '%s'
            . '<div class="input-group">'
            . '%s%s'
            . '<span class="input-group-btn">'
            . '<button id="' . $element->getAttribute('id') . '-refresh' . '" class="btn btn-default" type="button">'
            . '<i class="ion-loop"></i>'
            . '</button>'
            . '</span>'
            . '</div>';

        if ($position == self::CAPTCHA_PREPEND) {
            return sprintf($pattern, $captchaInput, $separator, $img);
        }

        return sprintf($pattern, $img, $separator, $captchaInput);
    }

}
