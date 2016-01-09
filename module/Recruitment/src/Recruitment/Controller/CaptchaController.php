<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Controller;

use Recruitment\Form\StudentRegistrationForm;
use Recruitment\Model\CaptchaImage;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

/**
 * Description of CaptchaController
 *
 * @author marcio
 */
class CaptchaController extends AbstractActionController
{

    public function generateAction()
    {
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', "image/png");
        $id = $this->params('id', false);

        if ($id) {

            $image = CaptchaImage::DEFAULT_DIR . $id;

            if (file_exists($image) !== false) {
                $imageGetContent = file_get_contents($image);

                $response->setStatusCode(200);
                $response->setContent($imageGetContent);

                if (file_exists($image) == true) {
                    unlink($image);
                }
            }
        }

        return $response;
    }

    public function refreshAction()
    {
        $form = new StudentRegistrationForm('Inscrição');
        $captcha = $form->get('registration_captcha')->getCaptcha();
        $data = array();
        $data['id'] = $captcha->generate();
        $data['src'] = $captcha->getImgUrl() . $captcha->getId() . $captcha->getSuffix();
        return new JsonModel($data);
    }

}
