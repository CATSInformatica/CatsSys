<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Controller;

use Database\Service\EntityManagerService;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use SchoolManagement\Entity\WarningType;
use SchoolManagement\Form\StudentWarningFilter;
use SchoolManagement\Form\StudentWarningForm;
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
            $message = 'Nenhum tipo de advertência seleciondo.';
        }

        return new JsonModel(array(
            'message' => $message
        ));
    }

}
