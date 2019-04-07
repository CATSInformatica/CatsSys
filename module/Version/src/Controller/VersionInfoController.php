<?php

namespace Version\Controller;

use Version\Model\CatsSysVersion;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class VersionInfoController extends AbstractActionController
{

    public function indexAction()
    {

        $version = new CatsSysVersion();

        return new ViewModel(array(
            'version' => $version,
        ));
    }

}
