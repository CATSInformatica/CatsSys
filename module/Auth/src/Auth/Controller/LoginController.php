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
use Auth\Form\LoginFilter;
use Auth\Form\LoginForm;
use Zend\Session\SessionManager;

/*
 * traits
 */
use Database\Provider\ProvidesEntityManager;

/**
 * Description of LoginController
 *
 * @author marcio
 */
class LoginController extends AbstractActionController
{

    use ProvidesEntityManager;

    /**
     * Faz a autenticação de usuários
     * @return ViewModel
     */
    public function loginAction()
    {
        $loginForm = new LoginForm();

        $message = null;

        $request = $this->getRequest();

        if ($request->isPost()) {
            $loginForm->setInputFilter(new LoginFilter(
                    $this->getServiceLocator())
            );
            $loginForm->setData($request->getPost());

            if ($loginForm->isValid()) {
                $data = $loginForm->getData();
                $message = $this->userAuthentication($data);
            }
        }

        return new ViewModel(array(
            'error' => 'Your authentication credentials are not valid!',
            'form' => $loginForm,
            'message' => $message,
        ));
    }

    protected function userAuthentication($data)
    {
        $message = null;
        $authService = $this->getServiceLocator()
                ->get('Zend\Authentication\AuthenticationService');

        $adapter = $authService->getAdapter();
        $adapter->setIdentityValue($data['username']);
        $adapter->setCredentialValue($data['password']);
        $authResult = $authService->authenticate();

        if ($authResult->isValid()) {
            $identity = $authResult->getIdentity();
//            $authService->clearIdentity();
            $authService->getStorage()->write($identity);
            if ($data['rememberme']) {
                $sessionManager = new SessionManager();
                $sessionManager->rememberMe(); //check module.config.php
            }
            $message = 'Usuário autenticado com sucesso.';
            $this->redirect()->toRoute('dashboard/default');
        } else {
            $message = 'Crendenciais inválidas.';
        }

//        if ($result->isValid()) {
//            $session_user->getManager()->getStorage()->clear('user');
//
//            $session = new Container('User');
//            $session->offsetSet('email', $data['email']);
//
//            $this->flashMessenger()->addMessage(array('success' => 'Login Success.'));
//            // Redirect to page after successful login
//        } else {
//            
//            // Redirect to page after login failure
//        }
        return $message;
    }

    public function logoutAction()
    {
        $auth = $this->getServiceLocator()
                ->get('Zend\Authentication\AuthenticationService');

        $auth->clearIdentity();
        $sessionManager = new SessionManager();
        $sessionManager->forgetMe();

        return $this->redirect()->toRoute('auth/default');
    }

}
