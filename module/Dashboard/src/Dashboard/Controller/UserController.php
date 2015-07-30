<?php

namespace Dashboard\Controller;
// Doctrine Annotations


// for the form


use Database\Provider\ProvidesEntityManager;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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
        
        if(!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('auth/default');
        }
//        echo __CLASS__;
        
        $entityManager = $this->getEntityManager();
        $users = $entityManager->getRepository('Database\Entity\User')->findAll();
        
        
        return new ViewModel(array('users' => $users));
    }

//    // C -Create
//    public function createAction()
//    {
//        $entityManager = $this->getEntityManager();
//        $user = new User;
//        $builder = new DoctrineAnnotationBuilder($entityManager);
//        $form = $builder->createForm($user);
//        $form->setHydrator(new DoctrineHydrator($entityManager, 'Database\Entity\User'));
//        $send = new Element('send');
//        $send->setValue('Create'); // submit
//        $send->setAttributes(array(
//            'type' => 'submit'
//        ));
//        $form->add($send);
//        $form->bind($user);
//
//        $request = $this->getRequest();
//        if ($request->isPost()) {
////			$form->setInputFilter(new UserFilter());
//            $form->setData($request->getPost());
//            if ($form->isValid()) {
//
//                // ToDo replace this code with code that uses the $user object. The user has to save himself or use datamapper 
////				$data = $form->getData();
////				$hydrator = new ReflectionHydrator();
////				$data  = $hydrator->extract($data); // turn the object to array
////				unset($data['submit']); // Cannot use object of type CsnUser\Entity\UserEntity as array
////				if (empty($data['usr_registration_date'])) $data['usr_registration_date'] = '2013-07-19 12:00:00';				
////				$this->getUsersTable()->insert($data);
//
//                $entityManager->persist($user);
//                $entityManager->flush();
//
//                return $this->redirect()->toRoute('dashboard/default');
//            }
//        }
//
//        return new ViewModel(array('form' => $form));
//    }
//
//    // U -Update
//    public function updateAction()
//    {
//        $id = $this->params()->fromRoute('id');
//        if (!$id) {
//            return $this->redirect()->toRoute('dashboard/default');
//        }
//        $entityManager = $this->getEntityManager();
//
//        try {
//            $repository = $entityManager->getRepository('Database\Entity\User');
//            $user = $repository->find($id);
//        } catch (\Exception $ex) {
//            echo $ex->getMessage(); // this never will be seen fi you don't comment the redirect
//            return $this->redirect()->toRoute('dashboard/default');
//        }
//        $builder = new DoctrineAnnotationBuilder($entityManager);
//        $form = $builder->createForm($user);
//        $form->setHydrator(new DoctrineHydrator($entityManager, 'Database\Entity\User'));
//        $send = new Element('send');
//        $send->setValue('Edit'); // submit
//        $send->setAttributes(array(
//            'type' => 'submit'
//        ));
//        $form->add($send);
//
//        $form->bind($user);
//
//        $request = $this->getRequest();
//        if ($request->isPost()) {
//            // $form->setInputFilter(new UserFilter());
//            $form->setData($request->getPost());
//            if ($form->isValid()) {
//
//                // ToDo raplace the code with something that uses user object
////				$data = $form->getData();
////				$hydrator = new ReflectionHydrator();
////				$data  = $hydrator->extract($data); // turn the object to array
////				unset($data['submit']);
////				if (empty($data['usr_registration_date'])) $data['usr_registration_date'] = '2013-07-19 12:00:00';
////				$this->getUsersTable()->update($data, array('usr_id' => $id));
//                $entityManager->persist($user);
//                $entityManager->flush();
//
//                return $this->redirect()->toRoute('csn_user/default', array('controller' => 'user-doctrine', 'action' => 'index'));
//            }
//        }
//        return new ViewModel(array('form' => $form, 'id' => $id));
//    }
//
//    // D -Delete
//    public function deleteAction()
//    {
//        $id = $this->params()->fromRoute('id');
//        if (!$id) {
//            return $this->redirect()->toRoute('dashboard/default');
//        }
//
//        $entityManager = $this->getEntityManager();
//
//        try {
//            $repository = $entityManager->getRepository('Database\Entity\User');
//            $user = $repository->find($id);
//            $entityManager->remove($user);
//            $entityManager->flush();
//        } catch (\Exception $ex) {
//            echo $ex->getMessage();
//            $this->redirect()->toRoute('dashboard/default');
//        }
//
//        return $this->redirect()->toRoute('dashboard/default');
//    }

}
