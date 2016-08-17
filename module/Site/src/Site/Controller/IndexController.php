<?php

namespace Site\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Site\Form\ContactForm;
use Site\Form\ContactFilter;

class IndexController extends AbstractActionController
{

    /**
     * Página inicial do site
     * 
     * @return ViewModel
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        $form = new ContactForm('Formulario de Contato');
        $message = null;

        if ($request->isPost()) {

            $form->setInputFilter(new ContactFilter());
            $form->setData($request->getPost()->toArray());

            if ($form->isValid()) {
                $data = $form->getData();
                /*
                 *  Envia o email
                 */
            }
        }
        return new ViewModel(array(
            'message' => $message,
            'contact_form' => $form,
        ));
    }

}
