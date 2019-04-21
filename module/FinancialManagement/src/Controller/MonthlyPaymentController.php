<?php

/*
 * Copyright (C) 2016 Márcio Dias <marciojr91@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace FinancialManagement\Controller;

use Database\Controller\AbstractDbalAndEntityActionController;
use DateTime;
use Exception;
use FinancialManagement\Entity\Repository\MonthlyBalanceRepository;
use FinancialManagement\Entity\Repository\MonthlyPaymentRepository;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

/**
 * Permite manipular a mensalidade dos alunos de uma turma.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class MonthlyPaymentController extends AbstractDbalAndEntityActionController
{

    /**
     * Lista as turmas para escolha.
     *
     * @return ViewModel
     */
    public function paymentAction()
    {

        try {

            $em = $this->getEntityManager();

            $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')
                ->findByEndDateGratherThan(new DateTime('now'));

            return new ViewModel([
                'message' => null,
                'classes' => $classes,
            ]);
        } catch (Exception $ex) {
            return new ViewModel([
                'message' => $ex->getMessage(),
                'classes' => null,
            ]);
        }
    }

    /**
     * Lista busca a situação de pagamento de todos os alunos da turma escolhida para o mês escolhido.
     */
    public function getPaymentsAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost();

            $conn = $this->getDbalConnection();
            try {

                $payments = MonthlyPaymentRepository::getClassPaymentsOfMonth($conn, $data['sclass'], $data['month']);

                return new JsonModel([
                    'payments' => $payments,
                ]);
            } catch (Exception $ex) {
                return new JsonModel([
                    'message' => $ex->getMessage(),
                ]);
            }
        }

        return new JsonModel([
            'message' => 'Esta url só pode ser acessada via post',
        ]);
    }

    /**
     * Salva as mensalidades recebidas.
     *
     * @return JsonModel
     */
    public function savePaymentsAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {

            $payments = (array) $request->getPost()['payments'];

            if (empty($payments)) {
                return new JsonModel([
                    'message' => 'Nenhum pagamento foi selecionado',
                ]);
            }

            try {
                $conn = $this->getDbalConnection();

                //busca o mês aberto

                $openedMonth = MonthlyBalanceRepository::getOpenedMonth($conn);

                if ($openedMonth === false) {
                    throw new Exception('Mensalidades são tratadas como receitas. '
                    . 'Para cadastrar receitas é necessário abrir um mês contábil.');
                }

                MonthlyPaymentRepository::savePayments($conn, $payments, $openedMonth);

                return new JsonModel([
                    'message' => 'Pagamentos salvos com sucesso',
                    'callback' => $payments
                ]);
            } catch (Exception $ex) {

                if($ex instanceof UniqueConstraintViolationException) {
                    return new JsonModel([
                        'message' => 'Mensalidade já cadastrada. Para alterar o valor, remova a mensalidade cadastrada.',
                    ]);
                }

                return new JsonModel([
                    'message' => 'Erro ao salvar as mesalidades. Erro: ' . $ex->getMessage(),
                ]);
            }
        }

        return new JsonModel([
            'message' => 'Esta url só pode ser acessada via post'
        ]);
    }

    /**
     * Remove as mesalidades recebidas.
     *
     * @return JsonModel
     */
    public function deletePaymentsAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {

            $payments = (array) $request->getPost()['payments'];

            if (empty($payments)) {
                return new JsonModel([
                    'message' => 'Nenhum pagamento foi selecionado',
                ]);
            }

            try {
                $conn = $this->getDbalConnection();

                //busca o mês aberto
                $openedMonth = MonthlyBalanceRepository::getOpenedMonth($conn);

                if ($openedMonth === false) {
                    throw new Exception('Mensalidades são tratadas como receitas. '
                    . 'Para cadastrar receitas é necessário abrir um mês contábil.');
                }

                MonthlyPaymentRepository::deletePayments($conn, $payments, $openedMonth);

                return new JsonModel([
                    'message' => 'Pagamentos removidos com sucesso',
                    'callback' => $payments
                ]);
            } catch (Exception $ex) {
                return new JsonModel([
                    'message' => 'Erro ao remover as mesalidades. Erro: ' . $ex->getMessage(),
                ]);
            }
        }

        return new JsonModel([
            'message' => 'Esta url só pode ser acessada via post'
        ]);
    }

}
