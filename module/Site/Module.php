<?php

namespace Site;

use Zend\Mvc\I18n\Translator;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;

class Module
{

    public function onBootstrap(MvcEvent $e)
    {
        $application = $e->getApplication();
        $sm = $application->getServiceManager();
        $eventManager = $application->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $translator = $sm->get('translator');
        $translator->addTranslationFile('phpArray', __DIR__ . '/language_php/Captcha.php', 'default', 'pt_BR');
        $translator->addTranslationFile('phpArray', __DIR__ . '/language_php/Validate.php', 'default', 'pt_BR');
        AbstractValidator::setDefaultTranslator(new Translator($translator));
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

}
