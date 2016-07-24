<?php

namespace SchoolManagement\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of Enrollment
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class EnrollmentRepository extends EntityRepository
{

    public function findAllCurrentStudents($params)
    {
        return $this->_em
                ->createQuery('SELECT e.enrollmentId, p.personFirstName, p.personLastName '
                    . 'FROM SchoolManagement\Entity\Enrollment e '
                    . 'JOIN e.registration r '
                    . 'JOIN r.person p '
                    . 'WHERE e.class = :class AND e.enrollmentEndDate IS NULL '
                    . 'ORDER BY p.personFirstName ASC'
                )
                ->setParameters($params)
                ->getResult();
    }

    /**
     * Busca todos os alunos atualmente matriculados na turma.
     * 
     * @param int $classId
     * @param bool $sortByName Se true faz a ordenação por nome, se false ordena por número de matrícula.
     * @return array
     */
    public function findByClass($classId, $sortByName = false)
    {

        $orderBy = !$sortByName ? 'e.enrollmentId' : 'p.personFirstName asc, p.personLastName asc';

        return $this->_em
                ->createQuery('SELECT p.personId, e.enrollmentId, '
                    . 'CONCAT(CONCAT(p.personFirstName, \' \'), p.personLastName) as '
                    . 'personFullName, p.personGender, p.personCpf, p.personEmail, p.personPhone, p.personSocialMedia, '
                    . 'p.personBirthday, p.personRg '
                    . 'FROM SchoolManagement\Entity\Enrollment e '
                    . 'JOIN e.registration r '
                    . 'JOIN r.person p '
                    . 'WHERE e.class = :class AND e.enrollmentEndDate IS NULL '
                    . 'ORDER BY ' . $orderBy
                )
                ->setParameter('class', $classId)
                ->getResult();
    }
}
