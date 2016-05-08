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
            ->where('e.enrollment_enddate IS NULL')
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
     * @param Connection $conn  Database Abstraction Layer connection
     * @param array $payments lista de mensalidades a serem salvas/atualizadas
     * @throws Exception
     */
    public static function savePayments(Connection $conn, array $payments)
    {
        $conn->beginTransaction();
        try {

            foreach ($payments as $payment) {

                if (!MonthlyPayment::isPaymentTypeValid($payment['type'])) {
                    throw new Exception('O tipo de pagamento informado não é válido');
                }
                if (!MonthlyPayment::isMonthValid($payment['month'])) {
                    throw new Exception('O mês informado não é válido');
                }

                if ($payment['id'] === "") {
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
                } else {
                    // update
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
     * @param Connection $conn  Database Abstraction Layer connection
     * @param array $payments lista de mensalidades a serem removidas
     * @throws Exception
     */
    public static function deletePayments(Connection $conn, array $payments)
    {
        $conn->beginTransaction();
        try {

            foreach ($payments as $payment) {

                if ($payment['id'] === "") {
                    throw new Exception("Não foi possível remover um dos pagamentos. "
                    . "O aluno " . $payment['enrollment'] . " não possui pagamento no mês " . $payment['month']);
                }
                // delete
                $conn->delete('monthly_payment',
                    [
                    'monthly_payment_id' => (int) $payment['id']
                    ]
                );
            }

            $conn->commit();
        } catch (Exception $ex) {
            $conn->rollBack();
            throw $ex;
        }
    }

}
