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

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Authentication\Form\LoginFilter;
use Authentication\Form\LoginForm;
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

    protected $auth;

    /**
     * Faz a autenticação de usuários
     * @return ViewModel
     */
    public function loginAction()
    {
        if ($this->hasIdentity()) {
            $this->redirect()->toRoute('dashboard/default');
        }

        $loginForm = new LoginForm();

        $message = null;

        $request = $this->getRequest();

        if ($request->isPost()) {
            $loginForm->setInputFilter(new LoginFilter(
                    $this->getServiceLocator()
            ));
            $loginForm->setData($request->getPost());

            if ($loginForm->isValid()) {
                $data = $loginForm->getData();
                $this->userAuthentication($data) ?
                                $this->redirect()->toRoute('dashboard/default') :
                                $message = 'Credenciais inválidas.';
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

        $adapter = $this->auth->getAdapter();
        $adapter->setIdentityValue($data['username']);
        $adapter->setCredentialValue($data['password']);
        $authResult = $this->auth->authenticate();

        if ($authResult->isValid()) {
            $identity = $authResult->getIdentity();
            $this->auth->getStorage()->write($identity);
            if ($data['rememberme']) {
                $sessionManager = new SessionManager();
                $sessionManager->rememberMe();
            }
            return true;
        }
        return false;

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
    }

    public function logoutAction()
    {
        if ($this->hasIdentity()) {
            $this->auth->clearIdentity();
            $sessionManager = new SessionManager();
            $sessionManager->forgetMe();
        }

        return $this->redirect()->toRoute('authentication/default');
    }

    protected function hasIdentity()
    {
        if (null == $this->auth) {
            $this->auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        }
        
        return $this->auth->hasIdentity();
    }

}
