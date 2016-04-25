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

namespace Authentication\Controller;

/*
 * traits
 */

use Authentication\Form\LoginFilter;
use Authentication\Form\LoginForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\View\Model\ViewModel;

/**
 * Description of LoginController
 *
 * @author marcio
 */
class LoginController extends AbstractActionController
{

    protected $authService;

    public function __construct($authService)
    {
        $this->authService = $authService;
    }

    /**
     * Faz a autenticação de usuários
     * @return ViewModel
     */
    public function loginAction()
    {
        if ($this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('ums');
        }

        $this->layout('login/layout');
        $loginForm = new LoginForm();
        $message = null;
        $request = $this->getRequest();

        if ($request->isPost()) {
            $loginForm->setInputFilter(new LoginFilter());
            $loginForm->setData($request->getPost());

            if ($loginForm->isValid()) {
                $data = $loginForm->getData();
                if ($this->userAuthentication($data)) {
                    return $this->redirect()->toRoute('ums');
                } else {
                    $message = 'Credenciais inválidas.';
                }
            }
        }

        return new ViewModel(array(
            'form' => $loginForm,
            'message' => $message,
        ));
    }

    protected function userAuthentication($data)
    {
        $auth = $this->authService;
        $adapter = $auth->getAdapter();
        $adapter->setIdentityValue($data['username']);
        $adapter->setCredentialValue($data['password']);
        $authResult = $auth->authenticate();

        if ($authResult->isValid()) {
            $identity = $authResult->getIdentity();
            $auth->getStorage()->write($identity);

            $sessionManager = new SessionManager();
            if ($data['rememberme']) {
                $sessionManager->rememberMe();
            }

            // store user roles in a session container
            $userContainer = new Container('User');
            $userContainer->offsetSet('id', $identity->getUserId());

            $userRoles = $identity->getRole()->toArray();

            $roleNames = array();

            foreach ($userRoles as $userRole) {
                $roleNames[] = $userRole->getRoleName();
            }

            $userContainer->offsetSet('activeRole', $roleNames[0]);
            $userContainer->offsetSet('allRoles', $roleNames);

            $sessionManager->writeClose();

            return true;
        }
        return false;
    }

    public function logoutAction()
    {
        $auth = $this->authService;

        if ($auth->hasIdentity()) {
            $auth->clearIdentity();

            // remove user data
            $userContainer = new Container('User');
            $userContainer->getManager()->getStorage()->clear('User');

            // forget-me
            $sessionManager = new SessionManager();
            $sessionManager->forgetMe();
        }

        return $this->redirect()->toRoute('authentication/login');
    }

}
