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

/**
 * Contém consultas específicas da entidade MonthlyBalance via ORM ou Dbal.
 *
 * Os métodos utilzados pelo dbal são sempre definidos como `static`.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class MonthlyBalanceRepository extends EntityRepository
{

    /**
     * Busca o mês contábil aberto. Caso nao exita retorna falso.
     *
     * @param Connection $conn
     * @return int|boolean Identificador do mês aberto ou falso caso não exista
     */
    public static function getOpenedMonth(Connection $conn)
    {
        $query = $conn->createQueryBuilder();
        $sth = $query
            ->select('mb.monthly_balance_id')
            ->from('monthly_balance', 'mb')
            ->where('mb.monthly_balance_is_open = true')
            ->setMaxResults(1)
            ->execute();

        $openedMonth = $sth->fetchAll();

        if (!empty($openedMonth)) {
            return (int) $openedMonth[0]['monthly_balance_id'];
        }

        return false;
    }

    /**
     * Atualiza a receita real do mês para entradas do tipo mensalidade (futuramente outros tipos?).
     *
     * Este método é utilzado em conjunto com o método que insere entradas via Dbal
     *  - CashFlowRepository::insertMonthlyPaymentCashFlowType
     *
     * @param Connection $conn  Database Abstraction Layer connection
     * @param int $monthBalance Identificador do mês aberto
     * @param float $value Valor a ser adicionado à receita real do mês
     */
    public static function updateMonthlyBalanceRevenue(Connection $conn, $monthBalance, $value)
    {
        $conn->executeUpdate("UPDATE monthly_balance SET monthly_balance_revenue = monthly_balance_revenue + ?"
            . " WHERE monthly_balance_id = ?", [
            $value,
            $monthBalance
        ]);
    }

}
