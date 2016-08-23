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
use DateTime;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use SchoolManagement\Entity\StudentClass;
use SchoolManagement\Form\StudentClassFilter;
use SchoolManagement\Form\StudentClassForm;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Permite manipular turmas
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class StudentClassController extends AbstractEntityActionController
{

    /**
     * Busca por todas as turmas cadastradas
     * 
     * @return ViewModel (classes, message)
     */
    public function indexAction()
    {

        try {
            $em = $this->getEntityManager();
            $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')->findAll();
            $message = null;
        } catch (Exception $ex) {
            $message = 'Erro inesperado. Entre com contato com o administrador do sistema. ' .
                'Erro: ' . $ex->getMessage();
            $classes = null;
        }

        return new ViewModel(array(
            'message' => $message,
            'classes' => $classes,
        ));
    }

    /**
     * Cria uma nova turma
     * @return ViewModel (form, message)
     */
    public function createAction()
    {
        $request = $this->getRequest();
        $message = null;
        $form = new StudentClassForm('Student Class');

        if ($request->isPost()) {

            $data = $request->getPost();
            $form->setData($data);
            $form->setInputFilter(new StudentClassFilter());

            if ($form->isValid()) {
                $data = $form->getData();

                try {
                    $em = $this->getEntityManager();

                    $class = new StudentClass();

                    $class->setClassName($data['class_name'])
                        ->setClassBeginDate(new DateTime($data['class_begin_date']))
                        ->setClassEndDate(new DateTime($data['class_end_date']));

                    $em->persist($class);
                    $em->flush();

                    return $this->redirect()->toRoute('school-management/student-class', array('action' => 'index'));
                } catch (Exception $ex) {
                    if ($ex instanceof UniqueConstraintViolationException) {
                        $message = 'já existe uma turma com este nome. Por favor utilize outro.';
                    } else {
                        $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                            'Erro: ' . $ex->getMessage();
                    }
                }
            }
        }

        return new ViewModel(array(
            'message' => $message,
            'form' => $form,
        ));
    }

    /**
     * Remove uma turma
     * 
     * Apenas turmas que ainda não iniciaram as aulas podem ser removidas
     * 
     * @return JsonModel message, mensagem de sucesso ou motivo da falha na remoção
     */
    public function deleteAction()
    {
        $id = $this->params('id', false);

        if ($id) {
            try {
                $em = $this->getEntityManager();

                $today = new DateTime('now');

                $class = $em->getReference('SchoolManagement\Entity\StudentClass', $id);

                if ($today < $class->getClassBeginDate()) {
                    $em->remove($class);
                    $em->flush();
                    $message = 'Turma removida com sucesso.';
                } else {
                    $message = 'Não é possivel remover esta turma. ' .
                        'A data de início das aulas é menor que a data atual.';
                }
            } catch (Exception $ex) {
                if ($ex instanceof ConstraintViolationException) {
                    $message = 'Não é possível remover turmas que possuam alunos associados.';
                } else {
                    $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                        'Erro: ' . $ex->getMessage();
                }
            }
        } else {
            $message = 'Nenhuma turma selecionada.';
        }

        return new JsonModel(array(
            'message' => $message
        ));
    }

    /**
     * Busca pela matrícula, nome e sobrenome de todos os alunos da turma $id.
     * @return JsonModel
     */
    public function getStudentsAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $data = $request->getPost();
            if (is_numeric($data['id'])) {
                try {
                    $em = $this->getEntityManager();

                    $students = $em->getRepository('SchoolManagement\Entity\Enrollment')->findAllCurrentStudents(array(
                        'class' => $data['id'],
                    ));

                    return new JsonModel([
                        'students' => $students,
                    ]);
                } catch (\Exception $ex) {
                    $message = $ex->getMessage();
                }
            } else {
                $message = 'Nenhuma turma selecionada';
            }
        } else {
            $message = 'Esta url só pode ser acessada via post';
        }

        return new JsonModel(array(
            'message' => $message
        ));
    }

    /**
     * Busca pelos dados todos os alunos da turma $id.
     * 
     * Esta função tem o mesmo papel de getStudentsAction, mas retorna mais informações.
     * 
     * @return ViewModel
     */
    public function getStudentsByClassAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $data = $request->getPost();
            if (is_numeric($data['id'])) {
                try {
                    $em = $this->getEntityManager();
                    $students = $em->getRepository('SchoolManagement\Entity\Enrollment')->findByClass($data['id']);
                    return new JsonModel([
                        'students' => $students,
                    ]);
                } catch (\Exception $ex) {
                    $message = $ex->getMessage();
                }
            } else {
                $message = 'Nenhuma turma selecionada';
            }
        } else {
            $message = 'Esta url só pode ser acessada via post';
        }

        return new JsonModel(array(
            'message' => $message
        ));
    }

    /**
     * Exibe informações importantes dos alunos matriculados na turma $id.
     * @return ViewModel
     */
    public function showStudentsByClassAction()
    {
        $id = $this->params('id', false);

        if ($id) {
            try {
                $em = $this->getEntityManager();

                $students = $em->getRepository('SchoolManagement\Entity\Enrollment')->findByClass($id);

                return new ViewModel([
                    'students' => $students,
                    'message' => null,
                ]);
            } catch (\Exception $ex) {
                return new ViewModel([
                    'message' => $ex->getMessage(),
                    'students' => null,
                ]);
            }
        }

        return new ViewModel([
            'message' => 'Nenhuma turma selecionada',
            'students' => null,
        ]);
    }

    /**
     * Exibe os alunos matriculados na turma selecionada em forma de um quadro com as fotos.
     * 
     * @return JsonModel|ViewModel
     */
    public function studentBoardAction()
    {
        $id = $this->params('id', false);

        if ($id) {
            try {

                $em = $this->getEntityManager();
                $students = $em->getRepository('SchoolManagement\Entity\Enrollment')->findByClass($id, true);

                return new ViewModel([
                    'message' => null,
                    'students' => $students,
                ]);
            } catch (\Exception $ex) {
                return new ViewModel([
                    'message' => $ex->getMessage(),
                    'students' => null,
                ]);
            }
        }

        return new ViewModel([
            'message' => 'Nenhuma turma selecionada',
            'students' => null,
        ]);
    }
}
