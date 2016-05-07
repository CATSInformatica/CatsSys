<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Controller;

use Database\Controller\AbstractEntityActionController;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Common\Collections\Criteria;
use FinancialManagement\Form\AddCashFlowForm;
use FinancialManagement\Entity\CashFlow;
use FinancialManagement\Form\CashFlowTypeForm;
use FinancialManagement\Entity\CashFlowType;
use FinancialManagement\Form\OpenMonthBalanceForm;
use FinancialManagement\Form\CloseMonthBalanceForm;
use FinancialManagement\Entity\MonthlyBalance;
use Zend\Form\FormInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

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
     * Retorna um array 'monthBalances' com informações sobre os últimos 
     * $month(int) balanços mensais
     * 
     * @return JSonModel
     */
    public function getMonthBalancesAction()
    {
        $months = (int) $this->params('id', false);

        try {
            $em = $this->getEntityManager();

            $date = new \DateTime('now');
            $date->modify('-' . $months . ' months');
            $criteria = Criteria::create()
                    ->where(Criteria::expr()->gte("monthlyBalanceOpen", $date))
                    ->andWhere(Criteria::expr()->lt("monthlyBalanceOpen",
                                    new \DateTime('now')))
                    ->andWhere(Criteria::expr()->eq("monthlyBalanceIsOpen",
                                    false))
                    ->orderBy(array("monthlyBalanceOpen" => Criteria::ASC));
            $monthBalances = $em
                    ->getRepository('FinancialManagement\Entity\MonthlyBalance')
                    ->matching($criteria);

            $monthBalancesData = [];
            foreach ($monthBalances as $monthBalance) {
                $monthBalancesData['month'][] = (int) $monthBalance
                                ->getMonthlyBalanceOpen()->format('m');
                $monthBalancesData['revenue'][] = (int) $monthBalance
                                ->getMonthlyBalanceRevenue();
                $monthBalancesData['projectedRevenue'][] = (int) $monthBalance
                                ->getMonthlyBalanceProjectedRevenue();
                $monthBalancesData['expense'][] = (int) $monthBalance
                                ->getMonthlyBalanceExpense();
                $monthBalancesData['projectedExpense'][] = (int) $monthBalance
                                ->getMonthlyBalanceProjectedExpense();
            }
            return new JSonModel(array(
                'monthBalances' => $monthBalancesData
            ));
        } catch (\Exception $ex) {
            return new JSonModel(array(
                'monthBalances' => array(
                    'month' => -1,
                    'revenue' => array_fill(0, 12, -1),
                    'projectedRevenue' => array_fill(0, 12, -1),
                    'expense' => array_fill(0, 12, -1),
                    'projectedExpense' => array_fill(0, 12, -1),
                )
            ));
        }
    }

    /**
     * Exibe uma tabela com todos os fluxos de caixa
     * 
     * @return ViewModel
     */
    public function cashFlowsAction()
    {
        try {
            $em = $this->getEntityManager();
            $cashFlows = $em->getRepository('FinancialManagement\Entity\CashFlow')
                    ->findAll();
            return new ViewModel(array(
                'message' => null,
                'cashFlows' => $cashFlows,
            ));
        } catch (\Exception $e) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Entre com contato com o administrador '
                . 'do sistema.<br>' . 'Erro: ' . $e->getMessage(),
                'cashFlows' => null,
            ));
        }
    }

    /**
     * Exibe um formulário para adicionar uma despesa ao balanço mensal aberto, 
     * se o balanço existir
     * 
     * @return ViewModel
     */
    public function addCashFlowAction()
    {
        $message = null;

        try {
            $request = $this->getRequest();

            $em = $this->getEntityManager();
            $openMonth = $em
                    ->getRepository('FinancialManagement\Entity\MonthlyBalance')
                    ->findBy(array('monthlyBalanceIsOpen' => true));
            if (count($openMonth) === 0) {
                return new ViewModel(array(
                    'message' => 'Não é possível cadastrar fluxos de caixa pois '
                    . 'não há nenhum mês aberto.',
                    'form' => null,
                ));
            }

            $form = new AddCashFlowForm($em);
            $cashFlow = new CashFlow();
            $form->bind($cashFlow);
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $data = $form->getData(FormInterface::VALUES_AS_ARRAY)['add_cash_flow_fieldset'];
                    $cashFlowType = $em->find('FinancialManagement\Entity\CashFlowType',
                            $data['cashFlowType']);
                    $cashFlow->setCashFlowType($cashFlowType);
                    $department = $em->find('AdministrativeStructure\Entity\Department',
                            $data['department']);
                    $cashFlow->setDepartment($department);
                    $cashFlow->setMonthlyBalance($openMonth[0]);
                    if ($cashFlow->getCashFlowType()->getCashFlowTypeDirection() === CashFlowType::CASH_FLOW_DIRECTION_INFLOW) {
                        $openMonth[0]->setMonthlyBalanceRevenue(
                                $openMonth[0]->getMonthlyBalanceRevenue() +
                                $cashFlow->getCashFlowAmount());
                    } else {
                        $openMonth[0]->setMonthlyBalanceExpense(
                                $openMonth[0]->getMonthlyBalanceExpense() +
                                $cashFlow->getCashFlowAmount());
                    }

                    $em->persist($cashFlow);
                    $em->flush();
                    $this->redirect()->toRoute('financial-management/cash-flow',
                            array('action' => 'cash-flows'));
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

    /**
     * Exibe um formulário de edição para o fluxo de caixa, desde que o mês 
     * ao qual ele está associado esteja aberto
     * 
     * @return ViewModel
     */
    public function editCashFlowAction()
    {
        $id = $this->params('id', false);
        $request = $this->getRequest();

        if ($id) {
            try {
                $em = $this->getEntityManager();
                $cashFlow = $em
                        ->getReference('FinancialManagement\Entity\CashFlow',
                        $id);
                if (!$cashFlow->getMonthlyBalance()->getMonthlyBalanceIsOpen()) {
                    return new ViewModel(array(
                        'message' => 'O fluxo de caixa não pode ser editado '
                        . 'pois o mês ao qual ele está associado ('
                        . $cashFlow->getMonthlyBalance()->getMonthlyBalanceOpen()->format('m/y')
                        . ') já foi fechado.',
                        'form' => null,
                    ));
                }
                $form = new AddCashFlowForm($em);
                $form->get('submit')->setAttribute('value', 'Editar');
                $form->bind($cashFlow);
                $initialDirection = $cashFlow->getCashFlowType()->getCashFlowTypeDirection();
                $initialAmount = $cashFlow->getCashFlowAmount();
                if ($request->isPost()) {
                    $form->setData($request->getPost());
                    if ($form->isValid()) {
                        // Assumindo que o valor do fluxo mudou, retira-se o 
                        // valor anterior do balanço mensal
                        $monthBalance = $cashFlow->getMonthlyBalance();
                        if ($initialDirection === CashFlowType::CASH_FLOW_DIRECTION_INFLOW) {
                            $monthBalance->setMonthlyBalanceRevenue(
                                    $monthBalance->getMonthlyBalanceRevenue() -
                                    $initialAmount);
                        } else {
                            $monthBalance->setMonthlyBalanceExpense(
                                    $monthBalance->getMonthlyBalanceExpense() -
                                    $initialAmount);
                        }

                        $data = $form->getData(FormInterface::VALUES_AS_ARRAY)['add_cash_flow_fieldset'];
                        $cashFlowType = $em->find('FinancialManagement\Entity\CashFlowType',
                                $data['cashFlowType']);
                        $cashFlow->setCashFlowType($cashFlowType);
                        $department = $em->find('AdministrativeStructure\Entity\Department',
                                $data['department']);
                        $cashFlow->setDepartment($department);
                        if ($cashFlow->getCashFlowType()->getCashFlowTypeDirection() === CashFlowType::CASH_FLOW_DIRECTION_INFLOW) {
                            $monthBalance->setMonthlyBalanceRevenue(
                                    $monthBalance->getMonthlyBalanceRevenue() +
                                    $cashFlow->getCashFlowAmount());
                        } else {
                            $monthBalance->setMonthlyBalanceExpense(
                                    $monthBalance->getMonthlyBalanceExpense() +
                                    $cashFlow->getCashFlowAmount());
                        }

                        $em->merge($cashFlow);
                        $em->flush();
                        $this->redirect()->toRoute('financial-management/cash-flow',
                                array('action' => 'cash-flows'));
                    }
                } else {
                    $form
                            ->get('add_cash_flow_fieldset')
                            ->get('cashFlowType')
                            ->setValue($cashFlow->getCashFlowType()->getCashFlowTypeId());
                    $form
                            ->get('add_cash_flow_fieldset')
                            ->get('department')
                            ->setValue($cashFlow->getDepartment()->getDepartmentId());
                }
                return new ViewModel(array(
                    'message' => null,
                    'form' => $form,
                ));
            } catch (Exception $ex) {
                $message = 'Erro inesperado. Entre com contato com o administrador '
                        . 'do sistema. ' . 'Erro: ' . $ex->getMessage();
            }
        } else {
            $message = 'Nenhum fluxo de caixa foi selecionado.';
        }
        return new ViewModel(array(
            'message' => $message,
            'form' => null,
        ));
    }

    /**
     * Remove o fluxo de caixa selecionado, desde que o mês ao qual ele está 
     * associado esteja aberto
     * 
     * @return JsonModel
     */
    public function deleteCashFlowAction()
    {
        $id = $this->params('id', false);

        if ($id) {
            try {
                $em = $this->getEntityManager();

                $cashFlow = $em
                        ->getReference('FinancialManagement\Entity\CashFlow',
                        $id);
                $monthBalance = $cashFlow->getMonthlyBalance();
                if (!$monthBalance->getMonthlyBalanceIsOpen()) {
                    return new JsonModel(array(
                        'message' => 'O fluxo de caixa não pôde ser removido '
                        . 'pois o mês ao qual ele está associado ('
                        . $cashFlow->getMonthlyBalance()->getMonthlyBalanceOpen()->format('m/y')
                        . ') já foi fechado.'
                    ));
                }
                if ($cashFlow->getCashFlowType()->getCashFlowTypeDirection() === CashFlowType::CASH_FLOW_DIRECTION_INFLOW) {
                    $monthBalance->setMonthlyBalanceRevenue(
                            $monthBalance->getMonthlyBalanceRevenue() -
                            $cashFlow->getCashFlowAmount());
                } else {
                    $monthBalance->setMonthlyBalanceExpense(
                            $monthBalance->getMonthlyBalanceExpense() -
                            $cashFlow->getCashFlowAmount());
                }
                $em->remove($cashFlow);
                $em->flush();
                return new JsonModel(array(
                    'message' => 'Fluxo de caixa removido com sucesso.',
                    'callback' => array(
                        'cashFlowId' => $id,
                    ),
                ));
            } catch (Exception $ex) {
                $message = 'Erro inesperado. Entre com contato com o administrador '
                        . 'do sistema.<br>' . 'Erro: ' . $ex->getMessage();
            }
        } else {
            $message = 'Nenhum fluxo de caixa selecionado.';
        }
        return new JsonModel(array(
            'message' => $message
        ));
    }

    /**
     * Exibe uma tabela com os tipos de fluxo de caixa existentes
     * 
     * @return ViewModel
     */
    public function cashFlowTypesAction()
    {
        $message = null;
        $em = $this->getEntityManager();
        $cashFlowTypes = [];

        try {
            $cashFlowTypes = $em
                    ->getRepository('FinancialManagement\Entity\CashFlowType')
                    ->findAll();
        } catch (\Exception $e) {
            $message = 'Erro inesperado. Entre com contato com o administrador '
                    . 'do sistema.<br>' . 'Erro: ' . $e->getMessage();
        }

        return new ViewModel(array(
            'cashFlowTypes' => $cashFlowTypes,
            'message' => $message,
        ));
    }

    /**
     * Remove o tipo de fluxo de caixa selecionado
     * 
     * @return JsonModel
     */
    public function deleteCashFlowTypeAction()
    {
        $id = $this->params('id', false);

        if ($id) {
            try {
                $em = $this->getEntityManager();

                $cashFlowType = $em
                        ->getReference('FinancialManagement\Entity\CashFlowType',
                        $id);
                if (count($cashFlowType->getCashFlows()) !== 0) {
                    return new JsonModel(array(
                        'message' => 'Este tipo de fluxo de caixa não pôde ser removido '
                        . 'pois possui fluxos de caixa associados a ele',
                    ));
                }
                $em->remove($cashFlowType);
                $em->flush();
                return new JsonModel(array(
                    'message' => 'Tipo de fluxo de caixa removido com sucesso.',
                    'callback' => array(
                        'cashFlowTypeId' => $id,
                    ),
                ));
            } catch (Exception $ex) {
                $message = 'Erro inesperado. Entre com contato com o administrador '
                        . 'do sistema.<br>' . 'Erro: ' . $ex->getMessage();
            }
        } else {
            $message = 'Nenhum tipo de fluxo de caixa selecionado.';
        }
        return new JsonModel(array(
            'message' => $message
        ));
    }

    /**
     * Exibe um formulário para criar um tipo de fluxo de caixa
     * 
     * @return ViewModel
     */
    public function createCashFlowTypeAction()
    {
        $message = null;
        $request = $this->getRequest();

        $em = $this->getEntityManager();
        $form = new CashFlowTypeForm($em);

        try {
            $cashFlowType = new CashFlowType();
            $form->bind($cashFlowType);
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $em->persist($cashFlowType);
                    $em->flush();
                    $this->redirect()->toRoute('financial-management/cash-flow',
                            array('action' => 'cash-flow-types'));
                }
            }
        } catch (UniqueConstraintViolationException $ex) {
            $message = 'Esse tipo de fluxo de caixa já existe.';
        } catch (\Exception $e) {
            $message = 'Erro inesperado. Entre com contato com o administrador '
                    . 'do sistema.<br>' . 'Erro: ' . $e->getMessage();
        }

        return new ViewModel(array(
            'message' => $message,
            'form' => $form,
        ));
    }

    /**
     * Exibe um formulário de edição para o tipo de fluxo de caixa
     * 
     * @return ViewModel
     */
    public function editCashFlowTypeAction()
    {
        $id = $this->params('id', false);
        $request = $this->getRequest();

        if ($id) {
            try {
                $em = $this->getEntityManager();
                $cashFlowType = $em
                        ->getReference('FinancialManagement\Entity\CashFlowType',
                        $id);

                $form = new CashFlowTypeForm($em);
                $form->get('submit')->setAttribute('value', 'Editar');
                $form->bind($cashFlowType);

                if ($request->isPost()) {
                    $form->setData($request->getPost());
                    if ($form->isValid()) {
                        $em->merge($cashFlowType);
                        $em->flush();
                        $this->redirect()->toRoute('financial-management/cash-flow',
                                array('action' => 'cash-flow-types'));
                    }
                }
                return new ViewModel(array(
                    'message' => null,
                    'form' => $form,
                ));
            } catch (Exception $ex) {
                $message = 'Erro inesperado. Entre com contato com o administrador '
                        . 'do sistema. ' . 'Erro: ' . $ex->getMessage();
            }
        } else {
            $message = 'Nenhum tipo de fluxo de caixa foi selecionado.';
        }
        return new ViewModel(array(
            'message' => $message,
            'form' => null,
        ));
    }

    /**
     * Exibe uma tabela com os meses abertos
     * 
     * @return ViewModel
     */
    public function monthBalancesAction()
    {
        $message = null;
        $em = $this->getEntityManager();
        $monthBalances = [];

        try {
            $monthBalances = $em->getRepository('FinancialManagement\Entity\MonthlyBalance')
                    ->findAll();
        } catch (\Exception $e) {
            $message = 'Erro inesperado. Entre com contato com o administrador '
                    . 'do sistema.<br>' . 'Erro: ' . $e->getMessage();
        }

        return new ViewModel(array(
            'monthBalances' => $monthBalances,
            'message' => $message,
        ));
    }

    /**
     * Exibe um formulário para abrir o mês
     * 
     * @return ViewModel
     */
    public function openMonthBalanceAction()
    {
        $message = null;
        $request = $this->getRequest();

        try {
            $em = $this->getEntityManager();
            $openMonth = $em->getRepository('FinancialManagement\Entity\MonthlyBalance')
                    ->findBy(array('monthlyBalanceIsOpen' => true));
            if (count($openMonth) !== 0) { // count($openMonth) === 1 -> Já existe um mês aberto
                return new ViewModel(array(
                    'message' => 'Não é possível cadastrar um novo balanço mensal '
                    . 'pois já existe um mês aberto.',
                    'form' => null,
                ));
            }
            $form = new OpenMonthBalanceForm($em);
            $monthBalance = new MonthlyBalance();
            $form->bind($monthBalance);
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $month = $em->getRepository('FinancialManagement\Entity\MonthlyBalance')
                            ->findBy(array('monthlyBalanceOpen' =>
                        $monthBalance->getMonthlyBalanceOpen()));
                    if (count($month) !== 0) { // O mês não pode ser aberto duas vezes
                        return new ViewModel(array(
                            'message' => 'Esse mês não pode ser aberto, pois já foi cadastrado.',
                            'form' => null,
                        ));
                    }
                    $monthBalance->setMonthlyBalanceRevenue(0);
                    $monthBalance->setMonthlyBalanceExpense(0);
                    $monthBalance->setMonthlyBalanceIsOpen(true);
                    $em->persist($monthBalance);
                    $em->flush();
                    $this->redirect()->toRoute('financial-management/cash-flow',
                            array('action' => 'month-balances'));
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

    /**
     * Exibe um formulário para fechar o mês
     * 
     * @return ViewModel
     */
    public function closeMonthBalanceAction()
    {
        $message = null;
        $request = $this->getRequest();

        try {
            $em = $this->getEntityManager();
            $openMonth = $em->getRepository('FinancialManagement\Entity\MonthlyBalance')
                    ->findBy(array('monthlyBalanceIsOpen' => true));
            if (count($openMonth) === 0) {
                return new ViewModel(array(
                    'message' => 'Não há nenhum mês aberto.',
                    'openMonth' => null,
                    'form' => null,
                ));
            }
            $form = new CloseMonthBalanceForm($em);
            $form->bind($openMonth[0]);
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $openMonth[0]->setMonthlyBalanceClose(new \DateTime('now'));
                    $openMonth[0]->setMonthlyBalanceIsOpen(false);
                    $em->merge($openMonth[0]);
                    $em->flush();
                    $this->redirect()->toRoute('financial-management/cash-flow',
                            array('action' => 'month-balances'));
                }
            }
        } catch (\Exception $e) {
            $message = 'Erro inesperado. Entre com contato com o administrador '
                    . 'do sistema.<br>' . 'Erro: ' . $e->getMessage();
        }

        return new ViewModel(array(
            'message' => $message,
            'openMonth' => $openMonth[0],
            'form' => $form,
        ));
    }

    /**
     * Remove o balanço mensal selecionado desde que não haja fluxos de caixa relacionados ao mês
     * 
     * @return JsonModel
     */
    public function deleteMonthBalanceAction()
    {
        $id = $this->params('id', false);

        if ($id) {
            try {
                $em = $this->getEntityManager();

                $monthBalance = $em
                        ->getReference('FinancialManagement\Entity\MonthlyBalance',
                        $id);
                if (count($monthBalance->getCashFlows()) !== 0) {
                    return new JsonModel(array(
                        'message' => 'Este balanço do mês não pôde ser removido '
                        . 'pois possui fluxos de caixa associados a ele',
                    ));
                }
                $em->remove($monthBalance);
                $em->flush();
                return new JsonModel(array(
                    'message' => 'Balanço do mês removido com sucesso.',
                    'callback' => array(
                        'monthBalanceId' => $id,
                    ),
                ));
            } catch (Exception $ex) {
                $message = 'Erro inesperado. Entre com contato com o administrador '
                        . 'do sistema.<br>' . 'Erro: ' . $ex->getMessage();
            }
        } else {
            $message = 'Nenhum balanço do mês selecionado.';
        }
        return new JsonModel(array(
            'message' => $message
        ));
    }

}
