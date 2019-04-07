<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Model;

use Zend\Captcha\Image;

/**
 * Description of Captcha
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class CaptchaImage extends Image
{

    const DEFAULT_DIR = './data/captcha/';
    const DEFAULT_URL = '/recruitment/captcha/generate/';

    /**
     * Directory for generated images
     *
     * @var string
     */
    protected $imgDir = self::DEFAULT_DIR;

    /**
     * URL for accessing images
     *
     * @var string
     */
    protected $imgUrl = self::DEFAULT_URL;

    /**
     * Image font file
     *
     * @var string
     */
    protected $font = './data/fonts/Arial.ttf';

    public function __construct($options = array(
        'width' => '350',
        'height' => '100',
        'dotNoiseLevel' => '60',
        'lineNoiseLevel' => 3,
        'expiration' => '360',
    ))
    {
        parent::__construct($options);
    }

    /**
     * !!! Override this function to point to the new helper.
     * Get helper name used to render captcha
     *
     * @return string
     */
    public function getHelperName()
    {
        return 'CaptchaImageViewHelper';
    }

}
