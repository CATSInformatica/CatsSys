<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace UMS;

use Zend\Mvc\I18n\Translator;
use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;

/**
 * Description of Module
 *
 * @author marcio
 */
class Module
{

    public function onBootstrap(MvcEvent $e)
    {
        $application = $e->getApplication();
        $sm = $application->getServiceManager();
        $translator = $sm->get('translator');
        $translator->addTranslationFile('phpArray', __DIR__ . '/language_php/Captcha.php', 'default', 'pt_BR');
        $translator->addTranslationFile('phpArray', __DIR__ . '/language_php/Validate.php', 'default', 'pt_BR');
        AbstractValidator::setDefaultTranslator(new Translator($translator));
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

}
