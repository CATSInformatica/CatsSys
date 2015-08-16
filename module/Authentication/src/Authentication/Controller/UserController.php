<?php

namespace Authentication\Controller;

use Database\Service\EntityManagerService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Authentication\Entity\User;
use Authentication\Form\UserForm;
use Authentication\Form\UserFilter;
use Authentication\Service\UserService;

class UserController extends AbstractActionController
{

    use EntityManagerService;

    // R -retrieve 	CRUD
    public function indexAction()
    {
        $this->layout('layout/dashboard-layout');
        $entityManager = $this->getEntityManager();
        $users = $entityManager->getRepository('Authentication\Entity\User')->findAll();


        return new ViewModel(array('users' => $users));
    }

//    // C -Create
    public function createAction()
    {
        $this->layout('layout/dashboard-layout');
        $userForm = new UserForm();
        $request = $this->getRequest();
        if ($request->isPost()) {

            $userForm->setInputFilter(new UserFilter());
            $userForm->setData($request->getPost());

            if ($userForm->isValid()) {

                $entityManager = $this->getEntityManager();
                $data = $userForm->getData();
                $user = new User();

                $pass = UserService::encryptPassword($data['user_password']);

                $user->setUserName($data['user_name'])
                        ->setUserPassword($pass['password'])
                        ->setUserPasswordSalt($pass['password_salt'])
                        ->setUserEmail($data['user_email'])
                        ->setUserActive(true);

                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirect()->toRoute('authentication/default', array(
                            'controller' => 'user',
                            'action' => 'index',
                ));
            }
        }

        return new ViewModel(array('form' => $userForm));
    }

    // Edit
    public function editAction()
    {
        $this->layout('layout/dashboard-layout');
        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('authentication/default', array(
                        'controller' => 'user',
                        'action' => 'index',
            ));
        }
        $entityManager = $this->getEntityManager();

        try {

            $user = $entityManager->getRepository('Authentication\Entity\User')
                    ->find($id);
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }

        $form = new UserForm();

        $form->get('user_name')->setValue($user->getUserName());
        $form->get('user_email')->setValue($user->getUserEmail());

        $request = $this->getRequest();

        if ($request->isPost()) {

            $formFilter = new UserFilter();
            $formFilter->get('user_name')->setRequired(false);

            $form->setInputFilter($formFilter);
            $data = $request->getPost();
            $form->setData($data);

            $form->get('user_name')->setValue($user->getUserName());

            if ($form->isValid()) {
                $pass = UserService::encryptPassword($data['user_password']);
                $user->setUserEmail($data['user_email'])
                        ->setUserPassword($pass['password'])
                        ->setUserPasswordSalt($pass['password_salt']);

                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirect()
                                ->toRoute('authentication/default', array(
                                    'controller' => 'user',
                                    'action' => 'index'
                ));
            }
        }
        return new ViewModel(array('form' => $form, 'id' => $id));
    }

//
//    // D -Delete
    public function deleteAction()
    {
        $this->layout('layout/dashboard-layout');
        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('authentication/default', array(
                        'controller' => 'user',
                        'action' => 'index',
            ));
        }

        $entityManager = $this->getEntityManager();

        try {
            $user = $entityManager->getRepository('Authentication\Entity\User')->find($id);
            $entityManager->remove($user);
            $entityManager->flush();
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            $this->redirect()->toRoute('authentication/default', array(
                'controller' => 'user',
                'action' => 'index',
            ));
        }

        return $this->redirect()->toRoute('authentication/default', array(
                    'controller' => 'user',
                    'action' => 'index',
        ));
    }

}
