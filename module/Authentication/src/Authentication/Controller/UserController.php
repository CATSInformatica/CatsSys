<?php

namespace Authentication\Controller;

use Authentication\Entity\User;
use Authentication\Form\UserForm;
use Database\Controller\AbstractEntityActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class UserController extends AbstractEntityActionController
{

    // R -retrieve 	CRUD
    public function indexAction()
    {
        $entityManager = $this->getEntityManager();
        $users = $entityManager->getRepository('Authentication\Entity\User')->findAll();
        return new ViewModel(array('users' => $users));
    }

//    // C -Create
    public function createAction()
    {

        try {

            $request = $this->getRequest();

            $em = $this->getEntityManager();
            $form = new UserForm($em);
            $user = new User();
            $form->bind($user);

            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {

                    $em->persist($user);
                    $em->flush();

                    return $this->redirect()->toRoute('authentication/user', [
                            'action' => 'index',
                    ]);
                }
            }

            return new ViewModel([
                'form' => $form
            ]);
        } catch (\Exception $ex) {

            return new ViewModel([
                'form' => null
            ]);
        }
    }

    // Edit
    public function editAction()
    {
        try {

            $id = $this->params('id', false);

            if (!$id) {
                return $this->redirect()->toRoute('authentication/user', [
                        'action' => 'index',
                ]);
            }

            $em = $this->getEntityManager();

            $user = $em->find('Authentication\Entity\User', $id);
            $form = new UserForm($em);
            $form->bind($user);

            $request = $this->getRequest();

            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {

                    $em->persist($user);
                    $em->flush();

                    return $this->redirect()->toRoute('authentication/user', [
                        'action' => 'index'
                    ]);
                }
            }
            return new ViewModel([
                'form' => $form
            ]);
        } catch (\Exception $ex) {
            return new ViewModel([
                'form' => null,
            ]);
        }
    }

    // D -Delete
    public function deleteAction()
    {
        $id = $this->params('id', false);
        if (!$id) {
            return new JsonModel([
                'message' => 'Nenhum usuário selecionado',
            ]);
        }

        try {
            $entityManager = $this->getEntityManager();
            $user = $entityManager->getReference('Authentication\Entity\User', $id);
            $entityManager->remove($user);
            $entityManager->flush();

            return new JsonModel([
                'message' => 'Usuário removido com sucesso',
            ]);
        } catch (\Exception $ex) {
            return new JsonModel([
                'message' => $ex->getMessage(),
            ]);
        }
    }
}
