<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Controller;

use Database\Service\EntityManagerService;
use DateTime;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Exception;
use Recruitment\Entity\Recruitment;
use Recruitment\Form\SearchRegistrationsForm;
use SchoolManagement\Entity\Enrollment;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Recruitment\Entity\RecruitmentStatus;

/**
 * Description of EnrollmentController
 *
 * @author marcio
 */
class EnrollmentController extends AbstractActionController
{

    use EntityManagerService;

    public function indexAction()
    {
        try {
            $em = $this->getEntityManager();
            $form = new SearchRegistrationsForm($em, Recruitment::STUDENT_RECRUITMENT_TYPE);
            $form->get('registrationStatus')->setValue(RecruitmentStatus::STATUSTYPE_INTERVIEW_APPROVED);

            return new ViewModel(array(
                'message' => null,
                'form' => $form,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema',
                'form' => null,
            ));
        }
    }

    /**
     * Busca as informações de inscrição do candidato e as turmas disponíveis 
     * (turmas que ainda não finalizaram as aulas)
     * 
     * @return ViewModel (message, registration, classes)
     */
    public function studentProfileAction()
    {
        $id = $this->params('id1', false);

        if ($id) {

            try {
                $em = $this->getEntityManager();
                $registration = $em->getRepository('Recruitment\Entity\Registration')->findOneBy(array(
                    'registrationId' => $id
                ));

                $enrollments = $em->getRepository('SchoolManagement\Entity\Enrollment')->findBy(array(
                    'registration' => $id
                ));

                $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')
                    ->findByEndDateGratherThan(new DateTime('now'));

                return new ViewModel(array(
                    'message' => '',
                    'registration' => $registration,
                    'classes' => $classes,
                    'enrollments' => $enrollments,
                ));
            } catch (Exception $ex) {

                return new ViewModel(array(
                    'message' => 'Não foi possível encontrar o registro do candidato: ' . $ex->getMessage(),
                    'registration' => null,
                    'classes' => null,
                    'enrollments' => null,
                ));
            }
        }

        return new ViewModel(array(
            'message' => 'nenhum candidato foi especificado.',
            'registration' => null,
            'classes' => null,
            'enrollments' => null,
        ));
    }

    /**
     * Faz a matrícula do aluno $sid na turma $cid
     */
    public function enrollAction()
    {
        $cid = $this->params('id1', false);
        $sid = $this->params('id2', false);

        if ($sid && $cid) {

            try {
                $em = $this->getEntityManager();

                $class = $em->getReference('SchoolManagement\Entity\StudentClass', $cid);
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
                    $message = 'Não foi possível encontrar o registro do candidato: ' . $ex->getMessage();
                }
            }
        } else {
            $message = 'turma e/ou aluno não especificado(s).';
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
        $cid = $this->params('id1', false);
        $sid = $this->params('id2', false);

        if ($sid && $cid) {

            try {

                $em = $this->getEntityManager();

                $enrollment = $em->getRepository('SchoolManagement\Entity\Enrollment')->findOneBy(array(
                    'class' => $cid,
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
                $message = 'Não foi possível encontrar o registro do candidato: ' . $ex->getMessage();
            }
        } else {
            $message = 'turma e/ou aluno não especificado(s).';
        }

        return new JsonModel(array(
            'message' => $message,
        ));
    }

}
