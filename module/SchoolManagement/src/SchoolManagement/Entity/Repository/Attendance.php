<?php

namespace SchoolManagement\Entity\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;

/**
 * Description of Attendance
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class Attendance extends EntityRepository
{

    /**
     * @param Connection $conn
     * @param array $students
     * @param \DateTime $date
     */
    public function insertNewList(Connection $conn, array $students, \DateTime $date)
    {
        $conn->beginTransaction();

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
                        \PDO::PARAM_INT,
                        \PDO::PARAM_INT,
                        'date'
                        ]
                    );
                }
                
            }
        }

        $conn->commit();
    }

}
