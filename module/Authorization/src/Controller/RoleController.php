<?php

namespace Authorization\Controller;

use Authorization\Entity\Role as EntityRole;
use Authorization\Form\RoleFilter;
use Authorization\Form\RoleForm;
use Authorization\Form\UserRoleForm;
use Database\Controller\AbstractEntityActionController;
use Doctrine\Common\Collections\ArrayCollection;
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

                $parents = new ArrayCollection();
                foreach ($data['role_parent'] as $parentRoleId) {
                    $parents->add($entityManager->getReference('Authorization\Entity\Role', $parentRoleId));
                }

                $role->addParents($parents);

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
        $roleId = $this->params('id', false);

        if (!$roleId) {
            return new JsonModel([
                'message' => 'Nenhum papel escolhido',
            ]);
        }

        try {
            $em = $this->getEntityManager();
            $role = $em->getReference('Authorization\Entity\Role', $roleId);
            $em->remove($role);
            $em->flush();
            return new JsonModel([
                'message' => 'Papel removido com sucesso',
            ]);
        } catch (\Exception $ex) {
            return new JsonModel([
                'message' => $ex->getMessage(),
            ]);
        }
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
                $em->persist($user);
                $em->flush();

                return $this->redirect()->toRoute('authorization/role',
                        [
                        'action' => 'users-x-roles',
                        ]
                );
            } catch (Exception $ex) {
                return new ViewModel(array(
                    'message' => $ex->getCode() . ': ' . $ex->getMessage(),
                    'form' => null,
                ));
            }
        }

        return new ViewModel([
            'form' => $form,
            'message' => null,
        ]);
    }

    public function removeUserRoleAction()
    {
        $request = $this->getRequest();
        $em = $this->getEntityManager();

        $roles = $em->getRepository('Authorization\Entity\Role')->findAll();
        $users = $em->getRepository('Authentication\Entity\User')->findAll();

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

                $role->removeUser($user);
                $em->merge($user);
                $em->flush();

                return $this->redirect()->toRoute('authorization/role',
                        [
                        'action' => 'users-x-roles',
                        ]
                );
            } catch (\Exception $ex) {
                return new ViewModel(array(
                    'message' => $ex->getMessage(),
                ));
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    public function usersXRolesAction()
    {

        $entityManager = $this->getEntityManager();
        $users = $entityManager->getRepository('Authentication\Entity\User')->findBy([
            'userActive' => true,
        ]);

        return new ViewModel([
            'users' => $users
        ]);
    }

}
