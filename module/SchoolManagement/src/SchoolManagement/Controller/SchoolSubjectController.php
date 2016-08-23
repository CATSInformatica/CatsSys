<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Controller;

use Database\Controller\AbstractEntityActionController;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use SchoolManagement\Entity\Subject;
use SchoolManagement\Form\SubjectForm;
use Zend\Form\FormInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Description of SchoolSubjectController
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class SchoolSubjectController extends AbstractEntityActionController
{

    /**
     * Exibe em uma tabela todas as disciplinas cadastradas
     * 
     * @return ViewModel
     */
    public function indexAction()
    {
        $message = null;
        try {
            $em = $this->getEntityManager();
            $subjects = $em->getRepository('SchoolManagement\Entity\Subject')->findAll();
        } catch (Exception $ex) {
            $message = $ex->getMessage();
        }

        return new ViewModel(array(
            'subjects' => $subjects,
            'message' => $message,
        ));
    }

    /**
     * Grava no banco dados uma disciplina
     * 
     * @return ViewModel
     */
    public function createAction()
    {
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $message = null;

        $subject = new Subject();
        $form = new SubjectForm($em);
        $form->bind($subject);
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData(FormInterface::VALUES_AS_ARRAY)['subject'];
                $parent = null;
                if ($data['subjectParent'] > 0) {
                    $parent = $em->find('SchoolManagement\Entity\Subject', $data['subjectParent']);
                }
                try {
                    $subject->setParent($parent);
                    $em->persist($subject);
                    $em->flush();
                    
                    return new ViewModel(array(
                        'message' => "Disciplina cadastrada com sucesso!",
                        'form' => $form,
                    ));
                } catch (UniqueConstraintViolationException $ex) {
                    $message = 'Essa disciplina já existe.';
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
     * Remove do banco de dados a disciplina selecionada
     * 
     * @return JsonModel
     */
    public function deleteAction()
    {
        $id = $this->params('id', false);

        if ($id) {
            try {
                $em = $this->getEntityManager();
                $subject = $em->getReference('SchoolManagement\Entity\Subject', $id);
                if ($subject->hasChildren()) {
                    return new JsonModel(array(
                        'message' => 'Não é possível remover uma disciplina que contém outras',
                    ));
                }
                $em->remove($subject);
                $em->flush();
                $message = 'Disciplina removida com sucesso.';
                return new JsonModel(array(
                    'message' => $message,
                    'callback' => array(
                        'subjectId' => $id,
                    ),
                ));
            } catch (Exception $ex) {
                $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                    'Erro: ' . $ex->getMessage();
            }
        } else {
            $message = 'Nenhuma disciplina foi selecionada.';
        }
        return new JsonModel(array(
            'message' => $message
        ));
    }

    /**
     * Exibe um formulário de edição para a disciplina selecionada
     * 
     * @return ViewModel
     */
    public function editAction()
    {
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $message = null;
        $id = $this->params('id', false);
        if ($id) {
            try {
                $subject = $em->getReference('SchoolManagement\Entity\Subject', $id);

                $form = new SubjectForm($em);
                $form->get('submit')->setAttribute('value', 'Editar');
                $form->bind($subject);
                if ($request->isPost()) {
                    $form->setData($request->getPost());
                    if ($form->isValid()) {
                        $data = $form->getData(FormInterface::VALUES_AS_ARRAY)['subject'];

                        // existe um novo pai e ele é diferente do filho (evita ciclos triviais)
                        if ($data['subjectParent'] > 0 && $data['subjectParent'] !== $subject->getSubjectId()) {
                            $subject->setParent($em->getReference('SchoolManagement\Entity\Subject',
                                    $data['subjectParent']));
                        } else {
                            $subject->setParent(null);
                        }

                        $em->merge($subject);
                        $em->flush();
                        return $this->redirect()->toRoute('school-management/school-subject', array('action' => 'index'));
                    }
                }
                return new ViewModel(array(
                    'message' => $message,
                    'form' => $form,
                    'subject' => $subject,
                ));
            } catch (Exception $ex) {
                $message = 'Erro inesperado. Entre com contato com o administrador do sistema. ' .
                    'Erro: ' . $ex->getMessage();
            }
        } else {
            $message = 'Nenhuma disciplina foi selecionada.';
        }
        return new ViewModel(array(
            'message' => $message,
            'form' => null,
        ));
    }

}
