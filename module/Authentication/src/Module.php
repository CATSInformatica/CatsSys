<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Module
 *
 * @author marcio
 */

namespace Authentication;

class Module {

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    // public function getServiceConfig() {
    //     return array(
    //         'factories' => array(
    //             'Zend\Authentication\AuthenticationService' => function($serviceManager) {
    //                 return $serviceManager->get('doctrine.authenticationservice.orm_default');
    //             },
    //         ),
    //     );
    // }

}
