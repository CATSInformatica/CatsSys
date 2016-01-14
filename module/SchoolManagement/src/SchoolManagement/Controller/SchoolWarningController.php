<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Controller;

use Database\Service\EntityManagerService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use SchoolManagement\Entity\Warning;
use SchoolManagement\Entity\WarningType;
use SchoolManagement\Form\StudentWarningForm;
use SchoolManagement\Form\StudentWarningFilter;
use SchoolManagement\Form\GiveWarningForm;
use SchoolManagement\Form\GiveWarningFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Description of SchoolWarning
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class SchoolWarningController extends AbstractActionController
{

    use EntityManagerService;

    /**
     * Busca todos os tipos de advertência cadastrados
     * 
     * @return ViewModel (message, warningTypes)
     */
    public function indexAction()
    {
        try {
            $em = $this->getEntityManager();
            $warningTypes = $em->getRepository('SchoolManagement\Entity\WarningType')->findAll();
            $message = null;
        } catch (Exception $ex) {
            $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                    'Erro: ' . $ex->getMessage();
            $warningTypes = null;
        }

        return new ViewModel(array(
            'message' => $message,
            'warningTypes' => $warningTypes,
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

                    $this->redirect()->toRoute('school-management/school-warning', array('action' => 'index'));
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
            'message' => $message
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
        
        try {
            $em = $this->getEntityManager();
            $warnings_types = $em->getRepository('SchoolManagement\Entity\WarningType')
                    ->findAll();
            $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')
                    ->findAll();
        } catch (\Exception $ex) {
            $message = $ex->getMessage();
        }
        
        //  Obtém todas as turmas e seleciona seus nomes
        foreach ($classes as $class) {
            $class_names[$class->getClassId()] = $class->getClassName();
            
            //  Obtém todos os alunos e seleciona seus nomes
            $enrollments = $class->getEnrollments();
            foreach ($enrollments as $enrollment) {
                $person_by_id[$enrollment->getRegistration()->getPerson()
                        ->getPersonId()] = $enrollment->getRegistration()->getPerson();
                $names[$enrollment->getEnrollmentId()] = $enrollment->getRegistration()
                        ->getPerson()->getPersonName();
            }             
        }
        
        //  Obtém todos os tipos de advertência e seleciona seus nomes
        foreach ($warnings_types as $wt) {
            $wt_by_id[$wt->getWarningTypeId()] = $wt;
            $wt_names[$wt->getWarningTypeId()] = $wt->getWarningTypeName();
        }
        
        $options = array(
            'names' => $names,
            'warning_names' => $wt_names,
            'class_names' => $class_names
        );        
        $form = new GiveWarningForm('Give a Warning Form', $options);    
        
        if ($request->isPost()) {
            $form->setInputFilter(new GiveWarningFilter());
            $form->setData($request->getPost()->toArray());
            
            if ($form->isValid()) {
                $data = $form->getData();
                
                $pRegistrations = $person_by_id[$data['person_id']]->getRegistrations();
                $registration = null;
                foreach ($pRegistrations as $pr) {
                    if ($pr->getRecruitment()->getRecruitmentType() === 1) {
                        $registration = $pr;
                        break;
                    }
                }                
                $enrollment = $em->getRepository('SchoolManagement\Entity\Enrollment')
                        ->findOneByRegistration($registration);                
                        
                try {
                    //  Adiciona uma referência na tabela Warning
                    $warning = new Warning();
                    $warning->setEnrollment($enrollment)
                            ->setWarningType($wt_by_id[$data['warning_id']])
                            ->setWarningDate(new \DateTime($data['warning_date']))
                            ->setWarningComment($data['warning_comment']);
                    
                    //  Adiciona uma referência no array $warnings da tabela Enrollment
                    $enrollment->setWarnings($enrollment->getWarnings()->add($warning));
                    
                    $em->persist($warning);
                    $em->flush();

                    $this->redirect()->toRoute('school-management/school-warning', 
                            array('action' => 'given'));
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
                $enrollment_warnings = $warning->getEnrollment()->getWarnings()
                        ->toArray();
                $warnings = array();
                foreach ($enrollment_warnings as $ew) {
                    if ($ew !== $warning) {
                        $warnings[] = $ew;
                    }
                }
                $warning->getEnrollment()->setWarnings(new ArrayCollection($warnings));
                    
                $em->remove($warning);
                $em->flush();
                $message = 'Advertência removida com sucesso.';
            } catch (Exception $ex) {
                $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                        'Erro: ' . $ex->getMessage();
            }
        } else {
            $message = 'Nenhuma advertência selecionada.';
        }
        
        return new JsonModel(array(
            'message' => $message
        ));        
    }

}
