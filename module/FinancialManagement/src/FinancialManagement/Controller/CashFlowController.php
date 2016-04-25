<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\Common\Collections\Criteria;
use FinancialManagement\Form\AddExpenseForm;
use FinancialManagement\Entity\Expense;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Description of CashFlowController
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class CashFlowController extends AbstractActionController
{

    use \Database\Service\EntityManagerService;

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
     * Retorna um array com as despesas do ano atual
     * 
     * @return JSonModel
     */
    public function getMonthsCashFlowAction()
    {
        $message = null;
        try {
            $em = $this->getEntityManager();
            $yearExpenses = $em->getRepository('FinancialManagement\Entity\Expense')
                    ->findBy(array('expenseYear' => date('Y')));
            $yearRevenues = $em->getRepository('FinancialManagement\Entity\Revenue')
                    ->findBy(array('revenueYear' => date('Y')));
            $monthExpense = array_fill(0, 12, 0);
            $monthRevenue = array_fill(0, 12, 0);
            $monthlyExpense = array(
                'description' => array(),
                'total' => 0,
            );
            $monthlyRevenue = array(
                'description' => array(),
                'total' => 0,
            );
            foreach ($yearExpenses as $ye) {
                $monthExpense[$ye->getExpenseMonth()] = (double) $ye->getExpenseAmount();
                if ($ye->getExpenseIsFixed()) {
                    $monthlyExpense['description'][] = $ye->getExpenseDescription();
                    $monthlyExpense['total'] += (double) $ye->getExpenseAmount();
                }
            }
            foreach ($yearRevenues as $yr) {
                $monthRevenue[$yr->getRevenueMonth()] = (double) $yr->getRevenueAmount();
                if ($yr->getRevenueIsFixed()) {
                    $monthlyRevenue['description'][] += $yr->getRevenueDescription();
                    $monthlyRevenue['total'] += (double) $yr->getRevenueAmount();
                }
            }

            return new JSonModel(array(
                'monthExpense' => $monthExpense,
                'monthRevenue' => $monthRevenue,
                'monthlyExpense' => $monthlyExpense,
                'monthlyRevenue' => $monthlyRevenue,
                'message' => $message,
            ));
        } catch (\Exception $ex) {
            $message = $ex->getMessage();
        }

        return new JSonModel(array(
            'message' => $message,
        ));
    }

    /**
     * Retorna um array com as despesas dos últimos $range anos anteriores
     * 
     * @return JSonModel
     */
    public function getYearsCashFlowAction()
    {
        $message = null;

        $range = $this->params('id', false);

        if ($range >= 0) {
            try {
                $currentYear = date('Y');

                $em = $this->getEntityManager();
                if ($range == 0) { // Todos os anos
                    $expenseCriteria = Criteria::create()
                            ->where(Criteria::expr()->lt('expenseYear', $currentYear));
                    $revenueCriteria = Criteria::create()
                            ->where(Criteria::expr()->lt('revenueYear', $currentYear));

                    $range = $currentYear - 2003; // 2003 - Ano que o CATS começou
                } else {
                    $expenseCriteria = Criteria::create()
                            ->where(Criteria::expr()->gte('expenseYear', $currentYear - $range))
                            ->andWhere(Criteria::expr()->lt('expenseYear', $currentYear));
                    $revenueCriteria = Criteria::create()
                            ->where(Criteria::expr()->gte('revenueYear', $currentYear - $range))
                            ->andWhere(Criteria::expr()->lt('revenueYear', $currentYear));
                }
                $lastYearsExpense = $em->getRepository('FinancialManagement\Entity\Expense')->matching($expenseCriteria);
                $lastYearsRevenue = $em->getRepository('FinancialManagement\Entity\Revenue')->matching($revenueCriteria);

                for ($i = 1; $i <= $range; ++$i) {
                    $yearExpense[$currentYear - $i] = 0.0;
                    $yearRevenue[$currentYear - $i] = 0.0;
                }
                foreach ($lastYearsExpense as $lye) {
                    $yearExpense[$lye->getExpenseYear()] += $lye->getExpenseAmount();
                }
                foreach ($lastYearsRevenue as $lyr) {
                    $yearRevenue[$lyr->getRevenueYear()] += $lyr->getRevenueAmount();
                }

                return new JSonModel(array(
                    'yearExpense' => $yearExpense,
                    'yearRevenue' => $yearRevenue,
                    'message' => $message,
                ));
            } catch (\Exception $ex) {
                $message = $ex->getMessage();
            }
        } else {
            $message = 'Intervalo não selecionado';
        }

        return new JSonModel(array(
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
            $expense = new Expense();
            $form->bind($expense);
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $em->persist($expense);
                    $em->flush();
                    $this->redirect()->toRoute('financial-management/cash-flow', array('action' => 'index'));
                }
                $message = 'Houve um erro com o formulário';
            }
        } catch (\Exception $e) {
            $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                    'Erro: ' . $ex->getMessage();
        }

        return new ViewModel(array(
            'message' => $message,
            'form' => $form,
        ));
    }

}
