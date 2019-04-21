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

namespace FinancialManagement\Entity\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Exception;
use FinancialManagement\Entity\MonthlyPayment;

/**
 * Contém consultas específicas para manipulação da entidade MonthlyPayment via ORM ou Dbal.
 *
 * Os métodos utilzados pelo dbal são sempre definidos como `static`.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class MonthlyPaymentRepository extends EntityRepository
{

    const NO_PAYMENT_ID = -1;

    /**
     * Retorna todos os pagamentos da turma $sclass no mês $month.
     *
     * @param Connection $conn Database Abstraction Layer connection
     * @param int $sclass Id da turma escolhida
     * @param int $month Inteiro representando o mês (valor entre 1 e 12)
     * @return array Pagamentos dos alunos da turma $sclass no mês $month
     */
    public static function getClassPaymentsOfMonth(Connection $conn, $sclass, $month)
    {
        $query = $conn->createQueryBuilder();
        $sth = $query
            ->select('e.enrollment_id, '
                . 'mp.monthly_payment_id, '
                . 'mp.monthly_payment_value, '
                . 'mp.monthly_payment_date, '
                . 'mp.monthly_payment_month, '
                . 'mp.monthly_payment_observation, '
                . 'mp.monthly_payment_type')
            ->from('enrollment', 'e')
            ->leftJoin('e', 'monthly_payment', 'mp', 'e.enrollment_id = mp.enrollment_id')
            ->andWhere('e.class_id = ?')
            ->andWhere('mp.monthly_payment_month = ? OR mp.monthly_payment_month IS NULL')
            ->setParameters([$sclass, $month])
            ->execute();

        $payments = $sth->fetchAll();
        return $payments;
    }

    /**
     * Salva as mensalidades recebidas.
     *
     * @todo buscar o valor a ser atualizado para salvar como entrada negativa.
     *
     * @param Connection $conn Database Abstraction Layer connection.
     * @param array $payments lista de mensalidades a serem salvas/atualizadas.
     * @param int $openedMonth Identificador do mês aberto.
     * @throws Exception
     */
    public static function savePayments(Connection $conn, array &$payments, $openedMonth)
    {
        $conn->beginTransaction();
        try {

            foreach ($payments as &$payment) {

                if (!MonthlyPayment::isPaymentTypeValid($payment['type'])) {
                    throw new Exception('O tipo de pagamento informado não é válido');
                }
                if (!MonthlyPayment::isMonthValid($payment['month'])) {
                    throw new Exception('O mês informado não é válido');
                }

                if ($payment['type'] === MonthlyPayment::PAYMENT_TYPE_FREE) {
                    $payment['value'] = 0;
                }

                if (self::NO_PAYMENT_ID === (int) $payment['id']) {
                    // insert
                    $conn->insert('monthly_payment',
                        [
                        'enrollment_id' => $payment['enrollment'],
                        'monthly_payment_type' => $payment['type'],
                        'monthly_payment_month' => $payment['month'],
                        'monthly_payment_observation' => $payment['observation'] == "" ? null : $payment['observation'],
                        'monthly_payment_date' => $payment['date'],
                        'monthly_payment_value' => $payment['value'],
                        ]
                    );

                    $payment['id'] = $conn->lastInsertId();

                    // não é mensalidade do tipo isento
                    if ($payment['value'] > 0) {
                        CashFlowRepository::insertMonthlyPayment(
                            $conn, $openedMonth, $payment['value']
                        );
                    }
                } else {
                    // update
                    $paidBefore = self::getPaymentOf($conn, $payment['id']);

                    if ($paidBefore['enrollment_id'] != $payment['enrollment'] || $paidBefore['monthly_payment_month'] != $payment['month']) {
                        throw new Exception('Não é possível atualizar a mensalidade do aluno ' . $payment['enrollment']
                        . ' para o mês ' . $payment['month'] . ' as informações não correspondem com os valores salvos'
                        . ' no banco de dados.');
                    }

                    // se os valores de pagamento são distintos faz o ajuste de entradas
                    if ($payment['value'] != $paidBefore['monthly_payment_value']) {

                        // não é mensalidade do tipo isento
                        if ($paidBefore['monthly_payment_value'] > 0) {
                            CashFlowRepository::insertMonthlyPayment(
                                $conn, $openedMonth, -$paidBefore['monthly_payment_value']
                            );
                        }

                        // não é mensalidade do tipo isento
                        if ($payment['value'] > 0) {
                            CashFlowRepository::insertMonthlyPayment(
                                $conn, $openedMonth, $payment['value']
                            );
                        }
                    }

                    $conn->update('monthly_payment',
                        [
                        'enrollment_id' => $payment['enrollment'],
                        'monthly_payment_type' => $payment['type'],
                        'monthly_payment_observation' => $payment['observation'],
                        'monthly_payment_month' => $payment['month'],
                        'monthly_payment_date' => $payment['date'],
                        'monthly_payment_value' => $payment['value'],
                        ],
                        [
                        'monthly_payment_id' => (int) $payment['id']
                        ]
                    );
                }
            }
            $conn->commit();
        } catch (Exception $ex) {
            $conn->rollBack();
            throw $ex;
        }
    }

    /**
     * Remove as mensalidades recebidas.
     *
     * @todo buscar o valor a ser atualizado para salvar como entrada negativa.
     *
     * @param Connection $conn Database Abstraction Layer connection.
     * @param array $payments lista de mensalidades a serem removidas.
     * @param int $openedMonth Identificador do mês aberto.
     * @throws Exception
     */
    public static function deletePayments(Connection $conn, array &$payments, $openedMonth)
    {
        $conn->beginTransaction();
        try {

            foreach ($payments as &$payment) {

                if (self::NO_PAYMENT_ID === (int) $payment['id']) {
                    throw new Exception("Não foi possível remover um dos pagamentos. "
                    . "O aluno " . $payment['enrollment'] . " não possui pagamento no mês " . $payment['month']);
                }

                $paidBefore = self::getPaymentOf($conn, $payment['id']);

                if ($paidBefore['enrollment_id'] != $payment['enrollment'] || $paidBefore['monthly_payment_month'] != $payment['month']) {
                    throw new Exception('Não é possível remover a mensalidade do aluno ' . $payment['enrollment']
                    . ' para o mês ' . $payment['month'] . ' as informações não correspondem com os valores salvos'
                    . ' no banco de dados.');
                }

                // não é mensalidade do tipo isento
                if ($paidBefore['monthly_payment_value'] > 0) {
                    CashFlowRepository::insertMonthlyPayment(
                        $conn, $openedMonth, -$paidBefore['monthly_payment_value']
                    );
                }

                $conn->delete('monthly_payment',
                    [
                    'monthly_payment_id' => (int) $payment['id']
                    ]
                );

                $payment['id'] = self::NO_PAYMENT_ID;
            }

            $conn->commit();
        } catch (Exception $ex) {
            $conn->rollBack();
            throw $ex;
        }
    }

    /**
     * Busca as informações da mensalidade cujo identificador é $id.
     *
     * @param Connection $conn Database Abstraction Layer connection.
     * @param type $id Identificador da mensalidade.
     * @return boolean|array Dados do pagamentod a mensalidade cujo identificador é $id
     */
    public static function getPaymentOf(Connection $conn, $id)
    {
        $query = $conn->createQueryBuilder();
        $sth = $query
            ->select('mp.*')
            ->from('monthly_payment', 'mp')
            ->where('mp.monthly_payment_id = :id')
            ->setParameter('id', $id)
            ->execute();

        $payments = $sth->fetchAll();

        if (!empty($payments)) {
            return $payments[0];
        }

        return false;
    }

}
