<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Description of PatrimonyController
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class PatrimonyController extends AbstractActionController
{
    public function indexAction() {
        
        return new ViewModel(array(
            'message' => null,
        ));
    }
}
