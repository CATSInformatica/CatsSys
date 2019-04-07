<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization\Controller;

use Authorization\Entity\Privilege;
use Authorization\Form\PrivilegeForm;
use Database\Controller\AbstractEntityActionController;
use Exception;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Description of Privilege
 *
 * @author marcio
 */
class PrivilegeController extends AbstractEntityActionController
{

    public function indexAction()
    {
        try {
            $em = $this->getEntityManager();
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

    public function createAction()
    {

        $request = $this->getRequest();

        $em = $this->getEntityManager();
        $roles = $em->getRepository('\Authorization\Entity\Role')->findAll();
        $resources = $em->getRepository('\Authorization\Entity\Resource')->findAll();

        $options = [];
        foreach ($roles as $role) {
            $options['roles'][$role->getRoleId()] = $role->getRoleName();
        }

        foreach ($resources as $resource) {
            $options['resources'][$resource->getResourceId()] = $resource->getResourceName();
        }

        $form = new PrivilegeForm('privilege', $options);

        if ($request->isPost()) {

            $data = $request->getPost();
            $em = $this->getEntityManager();

            $privilege = new Privilege();

            $privilege->setPrivilegeName($data['privilege_name'] !== "" ? $data['privilege_name'] : null)
                ->setPrivilegePermissionAllow($data['privilege_permission_allow'])
                ->setResource($em->getReference('Authorization\Entity\Resource', $data['resource_id']))
                ->setRole($em->getReference('Authorization\Entity\Role', $data['role_id']));

            try {
                $em->persist($privilege);
                $em->flush();

                return $this->redirect()->toRoute('authorization/privilege',
                        array(
                        'action' => 'index'
                ));
            } catch (Exception $ex) {
                return new ViewModel(array(
                    'message' => $ex->getMessage(),
                ));
            }
        }

        return new ViewModel(array(
            'form' => $form,
        ));
    }

    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');

        if ($id) {
            try {

                $em = $this->getEntityManager();
                $privilege = $em->getReference('Authorization\Entity\Privilege', $id);
                $em->remove($privilege);
                $em->flush();

                return new JsonModel(array(
                    'message' => 'Privilégio removido com sucesso.',
                ));
            } catch (Exception $ex) {
                return new JsonModel(array(
                    'message' => $ex->getMessage(),
                ));
            }
        }

        return new JsonModel(array(
            'message' => 'Nenhum privilégio foi selecionado',
        ));
    }

}
