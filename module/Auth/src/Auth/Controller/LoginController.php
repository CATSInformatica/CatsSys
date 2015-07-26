<?php

/**
 * TODO
 * O modulo Auth é responsável por autenticação e autorização
 * rotas deverão ser ajustadas para: 
 * auth --> página de login
 * auth/login --> página de login
 * auth/logout --> página de logout (retorna para página de login)
 * 
 */

namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
//use Auth\Entity\User;
use Auth\Form\LoginFilter;
use Auth\Form\LoginForm;
use Zend\Session\SessionManager;

/*
 * traits
 */
use Auth\Provider\ProvidesEntityManager;

/**
 * Description of LoginController
 *
 * @author marcio
 */
class LoginController extends AbstractActionController
{

    use ProvidesEntityManager;

    public function indexAction()
    {
        $em = $this->getEntityManager();
        $users = $em->getRepository('Auth\Entity\User')
                ->findAll();
        $message = $this->params()
                ->fromQuery('message', 'foo');

        return new ViewModel(array(
            'message' => $message,
            'users' => $users,
        ));
    }

    /**
     * Faz a autenticação de usuários
     * @return ViewModel
     */
    public function loginAction()
    {
        $loginForm = new LoginForm();

        $messages = null;

        $request = $this->getRequest();

        if ($request->isPost()) {
            $loginForm->setInputFilter(new LoginFilter(
                    $this->getServiceLocator())
            );
            $loginForm->setData($request->getPost());

            if ($loginForm->isValid()) {
                $data = $loginForm->getData();
                $messages = $this->authorize($data);
            }
        }
        return new ViewModel(array(
            'error' => 'Your authentication credentials are not valid!',
            'form' => $loginForm,
            'messages' => $messages,
        ));
    }

//    protected function createUser()
//    {
//
//        $em = $this->getEntityManager();
//
//        $user = new User();
//        $user->setUsrName("manuel@hotmail.com")
//                ->setUsrEmail("manuel@hotmail.com")
//                ->setUsrPasswordSalt(
//                        substr(str_shuffle("!@#$%*()_+{}:|0123456789ab"
//                                        . "cdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNO"
//                                        . "PQRSTUVWXYZ"), 0, 39))
//                ->setUsrPassword('123456789')
//                ->setUsrActive(true);
//
//        $em->persist($user);
//        $em->flush();
//
//        var_dump($user->getUsrId());
//    }

    protected function authorize($data)
    {

        $authService = $this->getServiceLocator()
                ->get('Zend\Authentication\AuthenticationService');

        $adapter = $authService->getAdapter();
        $adapter->setIdentityValue($data['username']);
        $adapter->setCredentialValue($data['password']);
        $authResult = $authService->authenticate();

        if ($authResult->isValid()) {
            $identity = $authResult->getIdentity();
            $authService->getStorage()->write($identity);
            $time = 86400; // Remember me by 1 day
            if ($data['rememberme']) {
                $sessionManager = new SessionManager();
                $sessionManager->rememberMe($time);
            }
        }
        return implode('\n', $authResult->getMessages());
    }

    public function logoutAction()
    {
        $auth = $this->getServiceLocator()
                ->get('Zend\Authentication\AuthenticationService');

        $auth->clearIdentity();
        $sessionManager = new SessionManager();
        $sessionManager->forgetMe();

        return $this->redirect()->toRoute('auth/default', array(
                    'controller' => 'login',
                    'action' => 'login',
                        )
        );
    }

}
