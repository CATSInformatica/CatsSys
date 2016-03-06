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
 * Description of EnrollmentController
 *
 * @author marcio
 */
class EnrollmentController extends AbstractEntityActionController
{

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
     * Faz a matrícula do aluno $sid na turma $cid
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

                $class = $em->getReference('SchoolManagement\Entity\StudentClass', $data['studentClass']);
                $registration = $em->getReference('Recruitment\Entity\Registration', $sid);
                $enrollment = new Enrollment();

                $enrollment
                    ->setClass($class)
                    ->setRegistration($registration);

                $class->addEnrollment($enrollment);

                $em->persist($class);
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

                $enrollment = $em->getRepository('SchoolManagement\Entity\Enrollment')->findOneBy(array(
                    'class' => $data['studentClass'],
                    'registration' => $sid
                ));

                if ($enrollment !== null) {
                    $em->remove($enrollment);
                    $em->flush();
                    $message = 'Matrícula desfeita com sucesso.';
                } else {
                    $message = 'O aluno não está matriculado na turma escolhida.'
                        . ' Por favor verifique se o aluno e a turma'
                        . ' foram escolhidos corretamente.';
                }
            } catch (Exception $ex) {
                $message = 'Erro: ' . $ex->getMessage();
            }
        } else {
            $message = 'Turma e/ou aluno não especificado(s).';
        }

        return new JsonModel(array(
            'message' => $message,
        ));
    }

}
