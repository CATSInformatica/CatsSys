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
use FinancialManagement\Entity\CashFlowType;

/**
 * Contém consultas específicas da entidade CashFlow via ORM ou Dbal.
 * 
 * Os métodos utilzados pelo dbal são sempre definidos como `static`.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class CashFlowRepository extends EntityRepository
{

    const MONTHLY_PAYMENT_DESCRIPTION_NEGATIVE = 'Uma mensalidade foi alterada ou removida. Este valor negativo indica o valor que teria sido recebido, mas não foi.';
    const MONTHLY_PAYMENT_DESCRIPTION_POSITIVE = 'Uma mensalidade foi salva. Este valor positivo indica o valor que foi recebido do aluno.';

    /**
     * Salva a entrada do tipo mensalidade.
     * 
     * Este método é utilzado em conjunto com os métodos que salvam ou removem as mesalidades
     *  - MonthlyPaymentRepository::savePayments
     *  - MonthlyPaymentRepository::deletePayments
     * 
     * @see MonthlyPaymentRepository
     * 
     * @param Connection $conn
     * @param int $monthBalance
     * @param float $value
     */
    public static function insertMonthlyPayment(Connection $conn, $monthBalance, $value)
    {
        $date = date('Y-m-d');

        $description = $value > 0 ? self::MONTHLY_PAYMENT_DESCRIPTION_POSITIVE : self::MONTHLY_PAYMENT_DESCRIPTION_NEGATIVE;

        $conn->insert('cash_flow',
            [
            'monthly_balance_id' => $monthBalance,
            'cash_flow_type' => CashFlowType::CASH_FLOW_TYPE_MONTHLY_PAYMENT,
            'cash_flow_date' => $date,
            'cash_flow_amount' => $value,
            'cash_flow_description' => $description,
            ]
        );

        MonthlyBalanceRepository::updateMonthlyBalanceRevenue($conn, $monthBalance, $value);
    }

}
