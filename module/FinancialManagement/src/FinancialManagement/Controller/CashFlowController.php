<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Controller;

use Database\Controller\AbstractEntityActionController;
use FinancialManagement\Form\AddExpenseForm;
//use FinancialManagement\Entity\Expense;
use Zend\View\Model\ViewModel;

/**
 * Description of CashFlowController
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class CashFlowController extends AbstractEntityActionController
{

    /**
     * Exibe informações sobre as despesas do cats
     * 
     * @return ViewModel
     */
    public function indexAction()
    {
        $message = null;

        return new ViewModel(array(
            'message' => $message,
        ));
    }

    /**
     * Exibe um formulário para adicionar uma despesa ao balanço financeiro
     * 
     * @return ViewModel
     */
    public function addExpenseAction()
    {
        $message = null;
        $request = $this->getRequest();

        try {
            $em = $this->getEntityManager();

            $form = new AddExpenseForm($em);
            //$expense = new Expense();
            //$form->bind($expense);
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    //$em->persist($expense);
                    //$em->flush();
                    $this->redirect()->toRoute('financial-management/cash-flow', 
                            array('action' => 'index'));
                }
            }
        } catch (\Exception $e) {
            $message = 'Erro inesperado. Entre com contato com o administrador '
                    . 'do sistema.<br>' . 'Erro: ' . $e->getMessage();
        }

        return new ViewModel(array(
            'message' => $message,
            'form' => $form,
        ));
    }

}
