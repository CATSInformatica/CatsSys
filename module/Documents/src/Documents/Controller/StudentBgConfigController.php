<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Documents\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Description of StudentBgConfigController
 * CRUD da configuração da carteirinha
 *
 * @author catsinformatica
 */
class StudentBgConfigController extends AbstractActionController
{
    use \Database\Service\EntityManagerService;
    
    public function indexAction()
    {
        try {
            $em = $this->getEntityManager();
            $configs = $em->getRepository('Documents\Entity\StudentBgConfig')->findAll();
            
        } catch (\Exception $ex) {
            $message = $ex->getMessage();
        }               
        return new ViewModel([
            'configs' => $configs, 
            'message' => $message
        ]); 
    }
    
    
    /**
     * grava no banco dados uma configuração de carteirinha
     * 
     * @return ViewModel
     */
    public function createAction()
    {
        
        
        return new ViewModel();
    }
    
}
