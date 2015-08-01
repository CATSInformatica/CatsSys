<?php

namespace Dashboard\Controller;

use Database\Provider\ProvidesEntityManager;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Database\Entity\User;
use Dashboard\Form\UserForm;
use Dashboard\Form\UserFilter;
use Zend\Crypt\Password\Bcrypt;

// Doctrine Entity manager
//use Doctrine\ORM\Tools\Setup;
//use Doctrine\ORM\EntityManager;
// Doctrine Annotations
//use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
//use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity;
//use DoctrineORMModule\Form\Annotation\AnnotationBuilder as DoctrineAnnotationBuilder;
// for the form
//use Zend\Form\Element;


class UserController extends AbstractActionController
{

    use ProvidesEntityManager;

    public function __construct()
    {
        
    }

    // R -retrieve 	CRUD
    public function indexAction()
    {
        $auth = new AuthenticationService();

        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('auth/default');
        }
//        echo __CLASS__;
        $entityManager = $this->getEntityManager();
        $users = $entityManager->getRepository('Database\Entity\User')->findAll();


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

                $bcrypt = new Bcrypt();
                $passwordSalt = $bcrypt->create($data['user_password']);
                $bcrypt->setSalt($passwordSalt);
                $password = $bcrypt->create($data['user_password']);

                $user->setUserName($data['user_name'])
                        ->setUserPassword($password)
                        ->setUserPasswordSalt($passwordSalt)
                        ->setUserEmail($data['user_email'])
                        ->setUserActive(true);

                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirect()->toRoute('dashboard/default', array(
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
        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('dashboard/default');
        }
        $entityManager = $this->getEntityManager();

        try {

            $user = $entityManager->getRepository('Database\Entity\User')
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

                $bcrypt = new Bcrypt();
                $bcrypt->setSalt($user->getUserPasswordSalt());
                $pass = $bcrypt->create($data['user_password']);

                $user->setUserEmail($data['user_email']);
                $user->setUserPassword($pass);

                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirect()
                                ->toRoute('dashboard/default', array(
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
        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('dashboard/default', array(
                        'controller' => 'user',
                        'action' => 'index',
            ));
        }

        $entityManager = $this->getEntityManager();

        try {
            $user = $entityManager->getRepository('Database\Entity\User')->find($id);
            $entityManager->remove($user);
            $entityManager->flush();
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            $this->redirect()->toRoute('dashboard/default', array(
                'controller' => 'user',
                'action' => 'index',
            ));
        }

        return $this->redirect()->toRoute('dashboard/default', array(
                    'controller' => 'user',
                    'action' => 'index',
        ));
    }

}
