<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization\Controller;

use Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Description of Privilege
 *
 * @author marcio
 */
class PrivilegeController extends AbstractActionController
{

    use \Database\Service\EntityManagerService;

    public function indexAction()
    {
        $em = $this->getEntityManager();

        try {

            $privileges = $em->getRepository('\Authorization\Entity\Privilege')
                    ->findAll();
            return new ViewModel(array(
                'privileges' => $privileges,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => $ex->getCode() . ': ' . $ex->getMessage(),
            ));
        }
    }

}
