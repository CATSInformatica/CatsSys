<?php

namespace Site\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
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
    
    /**
    * Retorna um array com as informações dos vestibulinhos passados (número, ano, link)
    * 
    * @return JsonModel
    */
    public function getPastExamsAction()
    {   
        $psa = null;
        $message = null;
        $dir = './public/docs';
        if (file_exists($dir) == false) {
            $message = 'Diretório \'' . $dir . '\' não encontrado!';
        } else {
            $dir_contents = implode('-', scandir($dir));
            if (!preg_match_all('/(?P<source>(PSA_(?P<year>\d{4})_(?P<number>\d)(_(?P<part>\d))?)\.pdf)/', $dir_contents, $psa)) {
                $psa = null;
            }
        }
        return new JsonModel(array(
            'message' => $message,
            'psa_dir' => substr($dir, 8),
            'psa' => $psa,
        ));
    }

}
