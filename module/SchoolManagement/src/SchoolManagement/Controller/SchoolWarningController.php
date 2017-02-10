<?php

/*
 * Copyright (C) 2016 Gabriel Pereira <rickardch@gmail.com>
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
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use SchoolManagement\Entity\Warning;
use SchoolManagement\Entity\WarningType;
use SchoolManagement\Form\GiveWarningForm;
use SchoolManagement\Form\StudentWarningFilter;
use SchoolManagement\Form\StudentWarningForm;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Exception;

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
        $em = $this->getEntityManager();
        
        try {
            $warnings = $em->getRepository('SchoolManagement\Entity\Warning')->findAll();
            
            return new ViewModel(array(
                'message' => null,
                'warnings' => $warnings,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                        'Erro: ' . $ex->getMessage(),
                'warnings' => [],
            ));
        }

        
    }

    /**
     * Dá uma advertência a um aluno
     * 
     * @return ViewModel ($form, $message)
     */
    public function giveAction()
    {
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $warning = new Warning();
        $wTypeNames = null;
        $classNames = null;
        $message = null;

        try {
            //  Obtém todos os tipos de advertência e seleciona seus nomes
            $wTypes = $em->getRepository('SchoolManagement\Entity\WarningType')
                    ->findAll();
            foreach ($wTypes as $wType) {
                $wTypeNames[$wType->getWarningTypeId()] = $wType->getWarningTypeName();
            }
            
            //  Obtém todas as turmas e seleciona seus nomes
            $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')
                    ->findAll();
            foreach ($classes as $class) {
                $classNames[$class->getClassId()] = $class->getClassName();
            }

            $options = array(
                'warning_type_names' => $wTypeNames,
                'class_names' => $classNames
            );
            $form = new GiveWarningForm($em, $options);
            $form->bind($warning);
            
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $em->persist($warning);
                    $em->flush();

                    return $this->redirect()->toRoute('school-management/school-warning', array('action' => 'given'));
                }
                $message = 'O formulário não foi preenchido corretamente.';
            }
            return new ViewModel(array(
                'message' => null,
                'form' => $form,
            ));
        } catch (\Throwable $ex) {
            $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                    'Erro: ' . $ex->getMessage();
        }

        return new ViewModel(array(
            'message' => $message,
            'form' => null,
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
        $em = $this->getEntityManager();

        if ($id) {
            try {
                //  Referencia da tabela Warning
                $warning = $em->getReference('SchoolManagement\Entity\Warning', $id);

                //  Deleta a referencia do array $warnings da tabela Enrollment
                $warning->getEnrollment()->removeWarning($warning);

                $em->remove($warning);
                $em->flush();
                
                return new JsonModel(array(
                    'message' => 'Advertência removida com sucesso.',
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
