<?php

namespace Authorization\Controller;

use Authorization\Entity\Role as EntityRole;
use Authorization\Form\RoleFilter;
use Authorization\Form\RoleForm;
use Authorization\Form\UserRoleForm;
use Database\Controller\AbstractEntityActionController;
use Exception;
use Zend\Json\Json;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

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
class RoleController extends AbstractEntityActionController
{

    public function indexAction()
    {
        $entityManager = $this->getEntityManager();
        try {
            $roles = $entityManager->getRepository('Authorization\Entity\Role')
                ->findBy([], ['roleId' => 'asc']);
            return new ViewModel(array(
                'roles' => $roles,
            ));
        } catch (Exception $dbSelectException) {
            return new ViewModel(array(
                'error' => $dbSelectException->getMessage(),
            ));
        }
    }

    /**
     * 
     * Done
     * @return ViewModel
     */
    public function createAction()
    {
        $request = $this->getRequest();

        $entityManager = $this->getEntityManager();
        $roles = $entityManager->getRepository('Authorization\Entity\Role')
            ->findAll();

        $formRoles = [];
        foreach ($roles as $role) {
            $formRoles[$role->getRoleId()] = $role->getRoleName();
        }
        $roleForm = new RoleForm($formRoles);


        if ($request->isPost()) {
            $data = $request->getPost();
            $roleForm->setInputFilter(new RoleFilter());
            $roleForm->setData($data);

            if ($roleForm->isValid()) {

                $data = $roleForm->getData();

                $role = new EntityRole();
                $role->setRoleName($data['role_name']);

                foreach ($data['role_parent'] as $parentRoleId) {
                    $role->addParent(
                        $entityManager->getReference('Authorization\Entity\Role', $parentRoleId)
                    );
                }

                try {
                    $entityManager->persist($role);
                    $entityManager->flush();

                    $this->redirect()->toRoute('authorization/role',
                        array(
                        'action' => 'index',
                    ));
                } catch (Exception $ex) {
                    return new ViewModel(array(
                        'message' => $ex->getCode() . ': ' . $ex->getMessage(),
                    ));
                }
            }
        }

        return new ViewModel(array(
            'roleForm' => $roleForm,
        ));
    }

    public function deleteAction()
    {
        $roleId = $this->params()->fromRoute('id');

        if ($roleId) {
            $em = $this->getEntityManager();
            try {
                $role = $em->getReference('Authorization\Entity\Role', array('roleId' => $roleId));
                $em->remove($role);
                $em->flush();
                return new ViewModel(array(
                    'message' => 'Role deleted successfully',
                ));
            } catch (Exception $ex) {
                return new ViewModel(array(
                    'message' => $ex->getCode() . ': ' . $ex->getMessage(),
                ));
            }
        }

        return new ViewModel(array(
            'message' => 'Param id can\'t be empty',
        ));
    }

    public function changeActiveUserRoleAction()
    {

        $request = $this->getRequest();

        // If it's ajax call
        if ($request->isXmlHttpRequest()) {
            $data = Json::decode($this->getRequest()->getContent());

            $userContainer = new Container('User');
            if ($userContainer->id && in_array($data->role, $userContainer->allRoles)) {
                $userContainer->activeRole = $data->role;

                return new JsonModel(array(
                    'success' => true,
                    'message' => 'Role changed successfully.',
                ));
            }
            return new JsonModel(array(
                'error' => true,
                'message' => 'You don\'t have the specified role.',
            ));
        }
        return new JsonModel(array(
            'error' => true,
            'message' => 'Request without data.',
        ));
    }

    public function addRoleToUserAction()
    {
        $request = $this->getRequest();
        $em = $this->getEntityManager();

        $roles = $em->getRepository('\Authorization\Entity\Role')->findAll();
        $users = $em->getRepository('\Authentication\Entity\User')->findAll();

        $options = [];
        foreach ($roles as $role) {
            $options['roles'][$role->getRoleId()] = $role->getRoleName();
        }

        foreach ($users as $user) {
            $options['users'][$user->getUserId()] = $user->getUserName();
        }

        $form = new UserRoleForm('user-role', $options);

        if ($request->isPost()) {
            $data = $request->getPost();

            try {
                $user = $em->getReference('Authentication\Entity\User', $data['user_id']);
                $role = $em->getReference('Authorization\Entity\Role', $data['role_id']);

                $user->addRole($role);
                $role->addUser($user);
                $em->persist($user);
                $em->persist($role);
                $em->flush();

                return $this->redirect()->toRoute('authentication/user',
                        array(
                        'action' => 'index'
                ));
            } catch (Exception $ex) {
                return new ViewModel(array(
                    'message' => $ex->getCode() . ': ' . $ex->getMessage(),
                ));
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

}
