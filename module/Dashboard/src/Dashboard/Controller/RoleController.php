<?php

namespace Dashboard\Controller;

use Database\Entity\Role as RoleEntity;
use Database\Provider\ProvidesEntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


use Authorization\Acl\AclDb;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RoleController
 *
 * @author marcio
 */
class RoleController extends AbstractActionController
{

    use ProvidesEntityManager;

    public function indexAction()
    {
        $entityManager = $this->getEntityManager();
//        $aclDb = new AclDb($entityManager);
        

//        $role1 = new RoleEntity();
//        
//        $role1->setRoleName('guest');
//        $entityManager->persist($role1);
//
//        $role2 = new RoleEntity();
//        $role2->setRoleName('member');
//        $entityManager->persist($role2);
//
//        $role3 = new RoleEntity();
//        $role3->setRoleName('admin');
//        $entityManager->persist($role3);
//        $entityManager->flush();

        try {
            $roles = $entityManager->getRepository('Database\Entity\Role')
                    ->findAll();

            return new ViewModel(array(
                'roles' => $roles,
            ));
        } catch (\Exception $dbSelectException) {
            return new ViewModel(array(
                'error' => $dbSelectException->getMessage(),
            ));
        }
    }

    public function createAction()
    {
//        $entityManager = $this->getEntityManager();
//
//        $role1 = new RoleEntity();
//        
//        $role1->setRoleName('guest');
//        $entityManager->persist($role1);
//        $entityManager->flush();
    }

    public function editAction()
    {
        
    }

    public function deleteAction()
    {
        
    }

}
