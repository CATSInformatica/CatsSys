<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Controller;

use Database\Controller\AbstractEntityActionController;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use SchoolManagement\Entity\Warning;
use SchoolManagement\Entity\WarningType;
use SchoolManagement\Form\GiveWarningFilter;
use SchoolManagement\Form\GiveWarningForm;
use SchoolManagement\Form\StudentWarningFilter;
use SchoolManagement\Form\StudentWarningForm;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Description of SchoolWarning
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class SchoolWarningController extends AbstractEntityActionController
{

    /**
     * Busca todos os tipos de advertência cadastrados
     * 
     * @return ViewModel (message, warningTypes)
     */
    public function indexAction()
    {
        try {
            $em = $this->getEntityManager();
            $wTypes = $em->getRepository('SchoolManagement\Entity\WarningType')->findAll();
            $message = null;
        } catch (Exception $ex) {
            $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                    'Erro: ' . $ex->getMessage();
            $wTypes = null;
        }

        return new ViewModel(array(
            'message' => $message,
            'warningTypes' => $wTypes,
        ));
    }

    /**
     * 
     * Criar o formulário de cadastro de tipos de advertência
     * 
     * @return ViewModel (form, message)
     */
    public function createAction()
    {
        $request = $this->getRequest();

        $form = new StudentWarningForm();

        $message = null;

        if ($request->isPost()) {

            $data = $request->getPost();
            $form->setInputFilter(new StudentWarningFilter());
            $form->setData($data);

            if ($form->isValid()) {

                try {
                    $data = $form->getData();

                    $em = $this->getEntityManager();
                    $warningType = new WarningType();
                    $warningType
                            ->setWarningTypeName($data['warning_type_name'])
                            ->setWarningTypeDescription($data['warning_type_description']);

                    $em->persist($warningType);
                    $em->flush();

                    return $this->redirect()->toRoute('school-management/school-warning', array('action' => 'index'));
                } catch (Exception $ex) {
                    if ($ex instanceof UniqueConstraintViolationException) {
                        $message = 'Já existe uma advertência com este nome. Por favor escolha outro.';
                    } else {
                        $message = 'Erro inesperado. Por favor entre em contato com o'
                                . ' adminstrador do sistema. '
                                . 'Erro: ' . $ex->getMessage();
                    }
                }
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'message' => $message
        ));
    }

    /**
     * remove o tipo de advertência cujo id é $id e 
     * 
     * @return JsonModel ($message)
     */
    public function deleteAction()
    {

        $id = $this->params('sid', false);

        if ($id) {
            try {
                $em = $this->getEntityManager();
                $wType = $em->getReference('SchoolManagement\Entity\WarningType', $id);

                $em->remove($wType);
                $em->flush();
                $message = 'Advertência removida com sucesso.';
                return new JsonModel(array(
                    'message' => $message,
                    'callback' => array(
                        'warningId' => $id,
                    ),
                ));
            } catch (Exception $ex) {

                if ($ex instanceof ConstraintViolationException) {
                    $message = 'Não é possível remover este tipo de advertência. '
                            . 'Existem alunos advertidos com este tipo.';
                } else {
                    $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                            'Erro: ' . $ex->getMessage();
                }
            }
        } else {
            $message = 'Nenhum tipo de advertência selecionado.';
        }

        return new JsonModel(array(
            'message' => $message,
        ));
    }

    /**
     * Exibe todas as advertências dadas
     * 
     * @return ViewModel ($message)
     */
    public function givenAction()
    {
        try {
            $em = $this->getEntityManager();
            $warnings = $em->getRepository('SchoolManagement\Entity\Warning')->findAll();
            $message = null;
        } catch (Exception $ex) {
            $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                    'Erro: ' . $ex->getMessage();
            $warnings = null;
        }

        return new ViewModel(array(
            'message' => $message,
            'warnings' => $warnings,
        ));
    }

    /**
     * Dá uma advertência a um aluno
     * 
     * @return ViewModel ($form, $message)
     */
    public function giveAction()
    {
        $request = $this->getRequest();
        $message = null;
        $sNames = null;
        $wTypeNames = null;
        $classNames = null;

        try {
            $em = $this->getEntityManager();
            $wTypes = $em->getRepository('SchoolManagement\Entity\WarningType')
                    ->findAll();
            $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')
                    ->findAll();
        } catch (\Exception $ex) {
            $message = $ex->getMessage();
        }

        //  Obtém todas as turmas e seleciona seus nomes
        foreach ($classes as $class) {
            $classById[$class->getClassId()] = $class;
            $classNames[$class->getClassId()] = $class->getClassName();

            //  Obtém todos os alunos e seleciona seus nomes
            $enrollments = $class->getEnrollments()->toArray();
            foreach ($enrollments as $enrollment) {
                $personById[$enrollment->getRegistration()->getPerson()
                                ->getPersonId()] = $enrollment->getRegistration()->getPerson();
                $sNames[$enrollment->getEnrollmentId()] = $enrollment->getRegistration()
                                ->getPerson()->getPersonName();
            }
        }

        //  Obtém todos os tipos de advertência e seleciona seus nomes
        foreach ($wTypes as $wType) {
            $wTypeById[$wType->getWarningTypeId()] = $wType;
            $wTypeNames[$wType->getWarningTypeId()] = $wType->getWarningTypeName();
        }

        $options = array(
            'names' => $sNames,
            'warning_names' => $wTypeNames,
            'class_names' => $classNames
        );
        $form = new GiveWarningForm('Give a Warning Form', $options);

        if ($request->isPost()) {
            $form->setInputFilter(new GiveWarningFilter());
            $form->setData($request->getPost()->toArray());

            if ($form->isValid()) {
                $data = $form->getData();

                $enrollments = $classById[$data['class_id']]->getEnrollments()
                        ->toArray();
                $pRegistrations = $personById[$data['person_id']]->getRegistrations();
                $pEnrollment = null;
                foreach ($enrollments as $enrollment) {
                    foreach ($pRegistrations as $pr) {
                        if ($enrollment->getRegistration() === $pr) {
                            $pEnrollment = $enrollment;
                            break;
                        }
                    }
                    if ($pEnrollment !== null) {
                        break;
                    }
                }
                if ($pEnrollment === null) {
                    $message = 'Este aluno não está matriculado na turma indicada.';
                    return new ViewModel(array(
                        'message' => $message,
                        'form' => $form,
                    ));
                }

                try {
                    //  Adiciona uma referência na tabela Warning
                    $warning = new Warning();
                    $warning->setEnrollment($pEnrollment)
                            ->setWarningType($wTypeById[$data['warning_id']])
                            ->setWarningDate(new DateTime($data['warning_date']))
                            ->setWarningComment($data['warning_comment']);

                    //  Adiciona uma referência no array $warnings da tabela Enrollment
                    $enrollment->setWarnings($enrollment->getWarnings()->add($warning));

                    $em->persist($warning);
                    $em->flush();

                    return $this->redirect()->toRoute('school-management/school-warning', array('action' => 'given'));
                } catch (Exception $ex) {
                    $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                            'Erro: ' . $ex->getMessage();
                }
            }
        }

        return new ViewModel(array(
            'message' => $message,
            'form' => $form,
        ));
    }

    /**
     * Remove uma advertência dada cujo id é $id
     * 
     * @return JsonModel ($message)
     */
    public function deleteGivenAction()
    {
        $id = $this->params('sid', false);

        if ($id) {
            try {
                $em = $this->getEntityManager();

                //  Referencia da tabela Warning
                $warning = $em->getReference('SchoolManagement\Entity\Warning', $id);

                //  Deleta a referencia do array $warnings da tabela Enrollment
                $enrollmentWarnings = $warning->getEnrollment()->getWarnings()
                        ->toArray();
                $warnings = array();
                foreach ($enrollmentWarnings as $ew) {
                    if ($ew !== $warning) {
                        $warnings[] = $ew;
                    }
                }
                $warning->getEnrollment()->setWarnings(new ArrayCollection($warnings));

                $em->remove($warning);
                $em->flush();
                $message = 'Advertência removida com sucesso.';
                return new JsonModel(array(
                    'message' => $message,
                    'callback' => array(
                        'givenWarningId' => $id,
                    ),
                ));
            } catch (Exception $ex) {
                $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                        'Erro: ' . $ex->getMessage();
            }
        } else {
            $message = 'Nenhuma advertência selecionada.';
        }

        return new JsonModel(array(
            'message' => $message,
        ));
    }

}
