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

namespace SchoolManagement\Controller;

use Database\Controller\AbstractEntityActionController;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Exception;
use Recruitment\Entity\Recruitment;
use Recruitment\Entity\RecruitmentStatus;
use Recruitment\Form\SearchRegistrationsForm;
use RuntimeException;
use SchoolManagement\Entity\Enrollment;
use SchoolManagement\Form\SearchEnrollmentForm;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Manipula matrículas de alunos.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class EnrollmentController extends AbstractEntityActionController
{

    /**
     * Exibe a tabela de candidatos que podem ser matriculados em uma turma
     * @return ViewModel
     */
    public function indexAction()
    {
        try {
            $em = $this->getEntityManager();
            $form = new SearchRegistrationsForm($em, Recruitment::STUDENT_RECRUITMENT_TYPE);

            $form->get('recruitment')
                ->setAttribute('disabled', true);

            $form->get('registrationStatus')
                ->setValue(RecruitmentStatus::STATUSTYPE_INTERVIEW_APPROVED)
                ->setAttribute('disabled', true);
            $form->remove('submit');

            $sclassForm = new SearchEnrollmentForm($em);

            return new ViewModel(array(
                'message' => null,
                'form' => $form,
                'sclassForm' => $sclassForm,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema',
                'form' => null,
            ));
        }
    }

    /**
     * Exibe a tabela de alunos matriculados em turmas ativas.
     * @return ViewModel
     */
    public function manageAction()
    {
        try {
            $em = $this->getEntityManager();

            $sclassForm = new SearchEnrollmentForm($em);

            return new ViewModel(array(
                'message' => null,
                'sclassForm' => $sclassForm,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema',
                'sclassForm' => null,
            ));
        }
    }

    /**
     * Faz a matrícula do aluno $sid na turma $cid.
     * 
     * Se a matrícula já existe (tiver sido encerrada) ela é reabilidatada.
     * Caso contrário um novo $enrollment é criado
     */
    public function enrollAction()
    {
        $sid = $this->params('id', false);
        $request = $this->getRequest();

        if ($sid && $request->isPost()) {

            try {

                $data = $request->getPost();

                if (!is_numeric($data['studentClass'])) {
                    throw new RuntimeException('Turma não especificada');
                }

                $em = $this->getEntityManager();

                $enrollment = $em->getRepository('SchoolManagement\Entity\Enrollment')->findOneBy(array(
                    'class' => $data['studentClass'],
                    'registration' => $sid
                ));

                if ($enrollment === null) {
                    $class = $em->getReference('SchoolManagement\Entity\StudentClass', $data['studentClass']);
                    $registration = $em->getReference('Recruitment\Entity\Registration', $sid);
                    $enrollment = new Enrollment();

                    $enrollment
                        ->setClass($class)
                        ->setRegistration($registration);

                    $class->addEnrollment($enrollment);

                    $em->persist($class);
                } else {
                    $enrollment->setEnrollmentEndDate(null);
                    $em->merge($enrollment);
                }


                $em->flush();

                $message = 'Matrícula realizada com sucesso.';
            } catch (Exception $ex) {
                if ($ex instanceof ConstraintViolationException) {
                    $message = 'Aluno já está matriculado nesta turma.';
                } else {
                    $message = 'Erro: ' . $ex->getMessage();
                }
            }
        } else {
            $message = 'Turma e/ou aluno não especificado(s).';
        }

        return new JsonModel(array(
            'message' => $message,
        ));
    }

    /**
     * Remove a matrícula de um candidato $sid em uma turma $cid
     * 
     * @return JsonModel message
     */
    public function unenrollAction()
    {
        $sid = $this->params('id', false);
        $request = $this->getRequest();

        if ($sid && $request->isPost()) {
            try {
                $data = $request->getPost();

                if (!is_numeric($data['studentClass'])) {
                    throw new RuntimeException('Turma não especificada');
                }

                $em = $this->getEntityManager();

                $enrollment = $em->getReference('SchoolManagement\Entity\Enrollment', $sid);
                $em->remove($enrollment);
                $em->flush();
                $message = 'Matrícula desfeita com sucesso.';
            } catch (Exception $ex) {
                if ($ex instanceof ConstraintViolationException) {
                    $message = 'Não é possível remover este aluno. Ele possui listas de presença/abono ou alguma outra '
                        . 'atividade associada. Se deseja retirar o aluno da turma escolha a ação [Encerrar Matrícula].';
                } else {
                    $message = 'Erro: ' . $ex->getMessage();
                }
            }
        } else {
            $message = 'Turma e/ou aluno não especificado(s).';
        }

        return new JsonModel(array(
            'message' => $message,
            'callback' => [
                'id' => $sid,
            ]
        ));
    }

    /**
     * Encerra a matrícula de um aluno em uma turma. Define um valor para enrollmentEndDate em Enrollment.
     * 
     * @return JsonModel
     * @throws RuntimeException
     */
    public function closeEnrollAction()
    {
        $sid = $this->params('id', false);
        $request = $this->getRequest();

        if ($sid && $request->isPost()) {
            try {

                $data = $request->getPost();

                if (!is_numeric($data['studentClass'])) {
                    throw new RuntimeException('Turma não especificada');
                }

                $em = $this->getEntityManager();
                $enrollment = $em->getReference('SchoolManagement\Entity\Enrollment', $sid);

                $enrollment->setEnrollmentEndDate(new \DateTime());
                $em->merge($enrollment);
                $em->flush();
                $message = 'Matrícula encerrada com sucesso.';

                return new JsonModel([
                    'message' => $message,
                    'callback' => [
                        'id' => $sid,
                    ]
                ]);
            } catch (Exception $ex) {
                return new JsonModel([
                    'message' => $ex->getMessage(),
                ]);
            }
        }

        return new JsonModel([
            'message' => 'Nenhum aluno especificado',
        ]);
    }
}
