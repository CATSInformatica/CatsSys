<?php

namespace SchoolManagement\Entity\Repository;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Exception;
use PDO;
use SchoolManagement\Entity\AttendanceType;

/**
 * Description of Attendance
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class AttendanceRepository extends EntityRepository
{

    public function findAttendance($params)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('e.enrollmentId, t.date, at.attendanceTypeId, at.attendanceType')
            ->from('SchoolManagement\Entity\Attendance', 't')
            ->join('t.attendanceType', 'at')
            ->join('t.enrollment', 'e')
            ->join('e.class', 'c')
            ->where('c.classId = :class') // assiduidade da turma :class
            ->andWhere('e.enrollmentEndDate IS NULL') // apenas alunos atuais
            ->andWhere('at.attendanceTypeId IN (:atts)') // restringe os tipos de abonos e presenças
            ->andWhere($qb->expr()->between('t.date', ':beginDate', ':endDate'))
            ->setParameters([
                'atts' => [
                    AttendanceType::TYPE_ATTENDANCE_BEGIN,
                    AttendanceType::TYPE_ATTENDANCE_END,
                    AttendanceType::TYPE_ATTENDANCE_ALLOWANCE_FULL,
                    AttendanceType::TYPE_ATTENDANCE_ALLOWANCE_BEGIN,
                    AttendanceType::TYPE_ATTENDANCE_ALLOWANCE_END,
                ],
                'beginDate' => $params['beginDate'],
                'endDate' => $params['endDate'],
                'class' => $params['class'],
            ])
            ->orderBy('e.enrollmentId, t.date');

        return $qb->getQuery()->getResult();
    }

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

            $conn->delete('attendance',
                [
                'attendance_date' => $date,
                'attendance_type_id' => AttendanceType::TYPE_ATTENDANCE_BEGIN,
                ], [
                'date',
                PDO::PARAM_INT,
            ]);

            $conn->delete('attendance',
                [
                'attendance_date' => $date,
                'attendance_type_id' => AttendanceType::TYPE_ATTENDANCE_END,
                ], [
                'date',
                PDO::PARAM_INT,
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

    public static function insertNewAttendance(Connection $conn, $enrollment, $attendanceType, \DateTime $date)
    {
        $conn->insert('attendance',
            [
            'enrollment_id' => $enrollment,
            'attendance_type_id' => $attendanceType,
            'attendance_date' => $date,
            ], [
            PDO::PARAM_INT,
            PDO::PARAM_INT,
            'date'
            ]
        );
    }

    public function findAllowance($params = [])
    {

        $qb = $this->_em->createQueryBuilder();

        $qb->select("a.attendanceId, e.enrollmentId, CONCAT(CONCAT(p.personFirstName, ' '), p.personLastName) as name,"
                . " p.personId, at.attendanceTypeId, at.attendanceType, a.date, c.className")
            ->from('SchoolManagement\Entity\Attendance', 'a')
            ->join('a.attendanceType', 'at')
            ->join('a.enrollment', 'e')
            ->join('e.class', 'c')
            ->join('e.registration', 'r')
            ->join('r.person', 'p')
            ->where('(a.attendanceType = :id1 OR a.attendanceType = :id2 OR  a.attendanceType = :id3 )')
            ->andWhere('a.date >= :beginDate')
            ->andWhere('a.date <= :endDate')
            ->setParameters(array(
                'id1' => AttendanceType::TYPE_ATTENDANCE_ALLOWANCE_FULL,
                'id2' => AttendanceType::TYPE_ATTENDANCE_ALLOWANCE_BEGIN,
                'id3' => AttendanceType::TYPE_ATTENDANCE_ALLOWANCE_END,
                'beginDate' => $params['beginDate'],
                'endDate' => $params['endDate'],
            ))
            ->orderBy('a.date')
            ->addOrderBy('p.personFirstName');

        return $qb->getQuery()->getResult();
    }

}
