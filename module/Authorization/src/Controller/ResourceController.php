<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization\Controller;

use Authorization\Entity\Resource;
use Authorization\Form\ResourceFilter;
use Authorization\Form\ResourceForm;
use Database\Controller\AbstractEntityActionController;
use Exception;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Description of ResourceController
 *
 * @author marcio
 */
class ResourceController extends AbstractEntityActionController
{

    public function indexAction()
    {
        $em = $this->getEntityManager();

        $resources = $em->getRepository('\Authorization\Entity\Resource')->findAll();

        return new ViewModel(array(
            'resources' => $resources,
        ));
    }

    public function createAction()
    {

        $request = $this->getRequest();

        $form = new ResourceForm();

        if ($request->isPost()) {

            $data = $request->getPost();
            $form->setInputFilter(new ResourceFilter());
            $form->setData($data);

            if ($form->isValid()) {
                $data = $form->getData();
                $em = $this->getEntityManager();

                $resource = new Resource();
                $resource->setResourceName($data['resource_name']);

                try {
                    $em->persist($resource);
                    $em->flush();

                    return $this->redirect()->toRoute('authorization/resource',
                            array(
                            'action' => 'index'
                    ));
                } catch (Exception $ex) {
                    return new ViewModel(array(
                        'message' => $ex->getCode() . ': ' . $ex->getMessage(),
                    ));
                }
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
            $em = $this->getEntityManager();
            try {
                $resource = $em->getReference('Authorization\Entity\Resource', array('resourceId' => $id));
                $em->remove($resource);
                $em->flush();
                return new JsonModel(array(
                    'message' => 'Recurso removido com sucesso',
                ));
            } catch (Exception $ex) {
                return new JsonModel(array(
                    'message' => $ex->getMessage(),
                ));
            }
        }

        return new JsonModel(array(
            'message' => 'Nenhum recurso foi selecionado',
        ));
    }

}
