<?php

namespace Authentication\Controller;

use Authentication\Entity\User;
use Authentication\Form\UserFilter;
use Authentication\Form\UserForm;
use Authentication\Service\UserService;
use Database\Controller\AbstractEntityActionController;
use Exception;
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
                    ->setUserActive(true);

                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirect()->toRoute('authentication/user',
                        array(
                        'action' => 'index',
                ));
            }
        }

        return new ViewModel(array('form' => $userForm));
    }

    // Edit
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('authentication/user',
                    array(
                    'action' => 'index',
            ));
        }
        $entityManager = $this->getEntityManager();

        try {

            $user = $entityManager->getReference('Authentication\Entity\User', $id);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }

        $form = new UserForm();

        $form->get('user_name')->setValue($user->getUserName());

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
                $user->setUserPassword($pass['password'])
                    ->setUserPasswordSalt($pass['password_salt']);

                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirect()
                        ->toRoute('authentication/user',
                            array(
                            'action' => 'index'
                ));
            }
        }
        return new ViewModel(array('form' => $form, 'id' => $id));
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
