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
     * Exibe as disciplinas e permite sua adição, edição e remoção.
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        try {
            $em = $this->getEntityManager();

            $form = new SubjectForm($em);
            $baseSubjects = $em->getRepository('SchoolManagement\Entity\Subject')
                    ->findBy(['parent' => null]);

            return new ViewModel(array(
                'baseSubjects' => $baseSubjects,
                'form' => $form,
                'message' => null,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'baseSubjects' => null,
                'form' => null,
                'message' => $ex->getMessage(),
            ));
        }
    }

    /**
     * Grava no banco dados uma disciplina
     *
     * @return JsonModel
     */
    public function createAction()
    {
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $message = null;

        try {
            if ($request->isPost()) {
                $form = new SubjectForm($em);
                $subject = new Subject();
                $form->bind($subject);
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $data = $form->getData(FormInterface::VALUES_AS_ARRAY)['subject-fieldset'];

                    $parent = null;
                    if ($data['subjectParent']) {
                        $parent = $em->find('SchoolManagement\Entity\Subject', $data['subjectParent']);
                    }

                    $subject->setParent($parent);
                    $em->persist($subject);
                    $em->flush();

                    return new JsonModel(array(
                        'message' => "Disciplina cadastrada com sucesso!",
                        'error' => false,
                        'subjectId' => $subject->getSubjectId(),
                        'subjectName' => $subject->getSubjectName(),
                        'subjectDescription' => $subject->getSubjectDescription(),
                        'formErrors' => [],
                    ));
                } else {
                    return new JsonModel(array(
                        'message' => 'Ocorreu um erro. A disciplina não foi criada!',
                        'error' => true,
                        'subjectId' => null,
                        'subjectName' => null,
                        'subjectDescription' => null,
                        'formErrors' => $form->getMessages()['subject-fieldset'],
                    ));
                }
            }
        } catch (UniqueConstraintViolationException $ex) {
            $message = 'Essa disciplina já existe.';
        } catch (Exception $ex) {
            $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                'Erro: ' . $ex->getMessage();
        }
        return new JsonModel(array(
            'message' => $message,
            'error' => true,
            'subjectId' => null,
            'subjectName' => null,
            'subjectDescription' => null,
            'formErrors' => [],
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
        $message = null;

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
                return new JsonModel(array(
                    'message' => 'Disciplina removida com sucesso.',
                    'error' => false,
                ));
            } catch (Exception $ex) {
                $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                    'Erro: ' . $ex->getMessage();
            }
        } else {
            $message = 'Nenhuma disciplina foi selecionada.';
        }

        return new JsonModel(array(
            'message' => $message,
            'error' => true,
        ));
    }

    /**
     * Persiste as mudanças feitas a uma disciplina selecionada
     *
     * @return JsonModel
     */
    public function editAction()
    {
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $message = null;

        $id = $this->params('id', false);
        try {
            if ($request->isPost()) {
                $subject = $em->getReference('SchoolManagement\Entity\Subject', $id);

                $form = new SubjectForm($em);
                $form->bind($subject);
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $data = $form->getData(FormInterface::VALUES_AS_ARRAY)['subject-fieldset'];

                    $parentSubject = null;
                    // existe um novo pai e ele é diferente do filho (evita ciclos triviais)
                    if ($data['subjectParent'] > 0 && $data['subjectParent'] !== $subject->getSubjectId()) {
                        $parentSubject = $em->getReference('SchoolManagement\Entity\Subject',
                                $data['subjectParent']);
                    }
                    $subject->setParent($parentSubject);

                    $em->merge($subject);
                    $em->flush();

                    return new JsonModel(array(
                        'message' => "Disciplina modificada com sucesso!",
                        'error' => false,
                        'subjectName' => $subject->getSubjectName(),
                        'subjectDescription' => $subject->getSubjectDescription(),
                        'subjectParentId' => ($parentSubject === null) ? null : $parentSubject->getSubjectId(),
                        'formErrors' => [],
                    ));
                } else {
                    return new JsonModel(array(
                        'message' => 'Ocorreu um erro. A disciplina não foi modificada!',
                        'error' => true,
                        'subjectName' => null,
                        'subjectDescription' => null,
                        'subjectParentId' => null,
                        'formErrors' => $form->getMessages()['subject-fieldset'],
                    ));
                }
            }
        } catch (UniqueConstraintViolationException $ex) {
            $message = 'Essa disciplina já existe.';
        } catch (Exception $ex) {
            $message = 'Erro inesperado. Entre com contato com o administrador do sistema. ' .
                'Erro: ' . $ex->getMessage();
        }
        return new JsonModel(array(
            'message' => $message,
            'error' => true,
            'subjectName' => null,
            'subjectDescription' => null,
            'subjectParentId' => null,
            'formErrors' => [],
        ));
    }

}
