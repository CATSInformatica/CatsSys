<?php

namespace SchoolManagement\Entity\Repository;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Exception;
use PDO;

/**
 * Description of Attendance
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class Attendance extends EntityRepository
{

    /**
     * Salva a lista de presença de uma data específica no banco de dados
     * 
     * @param Connection $conn
     * @param array $students
     * @param DateTime $date
     * @throws Exception
     */
    public static function insertNewList(Connection $conn, array $students, DateTime $date)
    {
        $conn->beginTransaction();
        try {

            $conn->delete('attendance', [
                'attendance_date' => $date
                ], [
                'date',
            ]);

            foreach ($students as $student) {

                foreach ($student['types'] as $stype) {

                    if ($stype['status']) {
                        $conn->insert('attendance',
                            [
                            'enrollment_id' => $student['id'],
                            'attendance_type_id' => $stype['id'],
                            'attendance_date' => $date,
                            ],
                            [
                            PDO::PARAM_INT,
                            PDO::PARAM_INT,
                            'date'
                            ]
                        );
                    }
                }
            }
            $conn->commit();
        } catch (Exception $ex) {
            $conn->rollBack();
            throw $ex;
        }
    }

}
