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

use Database\Controller\AbstractDbalAndEntityActionController;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use SchoolManagement\Entity\AttendanceType;
use SchoolManagement\Entity\Repository\AttendanceRepository;
use SchoolManagement\Form\SchoolAttendanceForm;
use SchoolManagement\Model\AttendanceAnalysis;
use SchoolManagement\Model\AttendanceList;
use SchoolManagement\Model\PdfAttendanceList;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer as ViewRenderer;

/**
 * Controller para gestão da frenquência de alunos.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class SchoolAttendanceController extends AbstractDbalAndEntityActionController
{

    protected $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer;
    }

    /**
     * Gera a lista de presença, em formato csv, para uma turma nas datas selecionadas
     */
    public function generateListAction()
    {
        try {
            $em = $this->getEntityManager();
            $form = new SchoolAttendanceForm($em, [
                AttendanceType::TYPE_ATTENDANCE_BEGIN,
                AttendanceType::TYPE_ATTENDANCE_END
                ]
            );

            return new ViewModel(array(
                'form' => $form,
                'message' => null,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'form' => null,
                'message' => 'Erro inesperado: ' . $ex->getMessage(),
            ));
        }
    }

    /**
     * Gera a lista de presença, exibida na própria página, para uma turma nas datas selecionadas
     * @return ViewModel
     */
    public function generateListV2Action()
    {
        try {
            $em = $this->getEntityManager();
            $form = new SchoolAttendanceForm($em, [
                AttendanceType::TYPE_ATTENDANCE_BEGIN,
                AttendanceType::TYPE_ATTENDANCE_END
                ]
            );

            return new ViewModel(array(
                'form' => $form,
                'message' => null,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'form' => null,
                'message' => 'Erro inesperado: ' . $ex->getMessage(),
            ));
        }
    }

    /**
     * Busca as listas dos alunos nos dias 'dates' do tipo 'types' da turma 'classId'.
     * 
     * @return JsonModel
     */
    public function getListsAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost();

            if (is_numeric($data['classId']) && count($data['dates']) > 0 && count($data['types']) > 0) {

                try {

                    $conn = $this->getDbalConnection();

                    $lists = [];
                    foreach ($data['dates'] as $date) {
                        foreach ($data['types'] as $type) {
                            $lists[$date][$type] = AttendanceRepository::findStudentAttendances(
                                    $conn, $data['classId'], $date, $type
                            );
                        }
                    }

                    return new JsonModel([
                        'lists' => $lists,
                    ]);
                } catch (Exception $ex) {
                    return new JsonModel([
                        'message' => $ex->getMessage(),
                    ]);
                }
            }

            return new JsonModel([
                'message' => 'Dados inválidos.',
            ]);
        }

        return new JsonModel([
            'message' => 'Esta url só pode ser acessada via post',
        ]);
    }

    /**
     * Importa listas de presença criadas em
     * @see SchoolAttendanceController::generateListAction()
     * 
     * @return ViewModel
     */
    public function importListAction()
    {

        return new ViewModel([
        ]);
    }

    /**
     * Gera a lista de frequência de acordo com os valores inseridos no formulário em 
     * @see SchoolAttendanceController::generateListAction()
     * 
     * @return ViewModel
     */
    public function downloadListAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $em = $this->getEntityManager();
            $form = new SchoolAttendanceForm($em, [
                AttendanceType::TYPE_ATTENDANCE_BEGIN,
                AttendanceType::TYPE_ATTENDANCE_END
            ]);
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                /**
                 * get all enrollments
                 */
                $enrollments = $em->getRepository('SchoolManagement\Entity\Enrollment')
                    ->findAllCurrentStudents(array(
                    'class' => $data['schoolClasses']
                ));

                $data['className'] = $em->find('SchoolManagement\Entity\StudentClass', $data['schoolClasses'])
                    ->getClassName();

                $attList = new AttendanceList($data, $enrollments);
                $csv = $attList->getCsv();

                $view = new ViewModel();
                $view->setTemplate('download-csv/template')
                    ->setVariable('results', $csv)
                    ->setTerminal(true);

                $output = $this
                    ->viewRenderer
                    ->render($view);

                $response = $this->getResponse();
                $headers = $response->getHeaders();
                $headers->addHeaderLine('Content-Type', 'text/csv');
                $headers->addHeaderLine('Content-Disposition', "attachment; filename=\"attendanceList.csv\"");
                $headers->addHeaderLine('Accept-Ranges', 'bytes');
                $headers->addHeaderLine('Content-Length', strlen($output));

                $response->setContent($output);
                return $response;
            }

            $vm = new ViewModel(array(
                'form' => $form,
                'message' => null,
            ));

            $vm->setTemplate('school-management/school-attendance/generate-list.phtml');
            return $vm;
        }

        return $this->redirect()->toRoute('school-management/school-attendance', [
                'action' => 'generateList'
                ]
        );
    }

    /**
     * Salva a lista de presença (salva apenas faltas) na data $date, dos tipos 'types' dos alunos 'students'.
     * 
     * @return JsonModel
     */
    public function saveAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            try {

                $data = $request->getPost();
                
                if(empty($data['date'])) {
                    throw new \Exception('Nenhuma data foi escolhida.');
                }
                
                $date = new \DateTime($data['date']);
                $conn = $this->getDbalConnection();

                if (!empty($data['students'])) {
                    AttendanceRepository::insertNewList(
                        $conn, $date, $data['types'], $data['students']
                    );
                } else {
                    AttendanceRepository::insertNewList(
                        $conn, $date, $data['types']
                    );
                }

                $message = 'Lista de ' . $date->format('d/m/Y') . ' enviada com sucesso.';
            } catch (\Exception $ex) {
                $message = "Erro: " . $ex->getMessage();
            }

            return new JsonModel([
                'message' => $message,
            ]);
        }

        return new JsonModel([
            'message' => 'Esta url só pode ser acessada via post',
        ]);
    }
    /*
     * Gera a lista de chamada para a turma selecionada.
     * 
     */

    public function printListAction()
    {
        $em = $this->getEntityManager();

        $classId = $this->params('id', false);

        if ($classId) {
            try {

                $enrollments = $em->getRepository('SchoolManagement\Entity\Enrollment')
                    ->findAllCurrentStudents(array(
                    'class' => $classId
                ));

                $students = [];
                foreach ($enrollments as $enr) {
                    $students[] = array(
                        'id' => str_pad($enr['enrollmentId'], 4, '0', STR_PAD_LEFT),
                        'name' => $enr['personFirstName'] . ' ' . $enr['personLastName'],
                    );
                }

                $pdf = new PdfAttendanceList($students);

                return new ViewModel(array(
                    'message' => null,
                    'pdf' => $pdf,
                ));
            } catch (\Exception $e) {

                return new ViewModel([
                    'message' => $e->getMessage(),
                    'pdf' => null,
                ]);
            }
        }

        return new ViewModel([
            'message' => 'Nenhuma turma foi selecionada.',
            'pdf' => null,
        ]);
    }

    /**
     * Exibe os abonos de acordo com a seleção do mês
     * @return ViewModel
     */
    public function allowanceAction()
    {
        return new ViewModel();
    }

    /**
     * Exibe os abonos de acordo com a seleção do mês
     * @return ViewModel
     */
    public function getAllowanceAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();

            try {
                $em = $this->getEntityManager();

                $allowances = $em->getRepository('SchoolManagement\Entity\Attendance')
                    ->findAllowance(array(
                    'beginDate' => new \DateTime($data['start']),
                    'endDate' => new \DateTime($data['end']),
                ));

                return new JsonModel([
                    'allowance' => $allowances,
                ]);
            } catch (\Exception $ex) {
                return new JsonModel([
                    'message' => $ex->getMessage(),
                ]);
            }
        }

        return new JsonModel([
            'message' => 'Esta url só pode ser acessada via post',
        ]);
    }

    /**
     * Remove o abono cujo valor de attendanceId é $id
     * @return JsonModel
     */
    public function deleteAllowanceAction()
    {

        $id = $this->params('id', false);

        if ($id) {
            try {
                $em = $this->getEntityManager();
                $allowance = $em->getReference('SchoolManagement\Entity\Attendance', $id);
                $em->remove($allowance);
                $em->flush();
                return new JsonModel([
                    'message' => 'Abono removido com sucesso',
                    'callback' => array(
                        'id' => $id,
                    ),
                ]);
            } catch (\Exception $ex) {
                return new JsonModel([
                    'message' => $ex->getMessage(),
                ]);
            }
        }

        return new JsonModel([
            'message' => 'Nenhum abono foi selecionado.',
        ]);
    }

    public function addAllowanceAction()
    {

        try {

            $em = $this->getEntityManager();

            $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')
                ->findByEndDateGratherThan(new \DateTime('now'));

            $allowanceTypes = $em->getRepository('SchoolManagement\Entity\AttendanceType')
                ->findByAttendanceTypeIds([
                AttendanceType::TYPE_ATTENDANCE_ALLOWANCE_BEGIN,
                AttendanceType::TYPE_ATTENDANCE_ALLOWANCE_END,
                AttendanceType::TYPE_ATTENDANCE_ALLOWANCE_FULL,
            ]);

            return new ViewModel([
                'message' => null,
                'classes' => $classes,
                'allowanceTypes' => $allowanceTypes,
            ]);
        } catch (Exception $ex) {
            return new ViewModel([
                'message' => $ex->getMessage(),
                'classes' => null,
            ]);
        }
    }

    /**
     * @todo Criar os métodos setEnrollment, setDate e setAllowanceType
     * @return JsonModel
     */
    public function saveAllowanceAction()
    {
        $this->layout('empty/layout');
        $request = $this->getRequest();

        if ($request->isPost()) {

            $message = "";

            $data = $request->getPost();
            $dbal = $this->getDbalConnection();

            foreach ($data['allowances'] as $all) {
                $date = new \DateTime($all['date']);
                try {
                    AttendanceRepository::insertNewAttendance($dbal, $all['enrollment'], $all['allowanceType'], $date);

                    $message .= "<br>Aluno " . $all['enrollment'] . " recebeu o " .
                        AttendanceType::getAttendanceTypeName($all['allowanceType'])
                        . " na data " . $date->format('d/m/Y');
                } catch (\Exception $ex) {
                    if ($ex instanceof UniqueConstraintViolationException) {
                        $message .= "<br>Aluno " . $all['enrollment'] . " já possui o " .
                            AttendanceType::getAttendanceTypeName($all['allowanceType'])
                            . " na data " . $date->format('d/m/Y');
                        continue;
                    }
                    $message = $ex->getMessage();
                    break;
                }
            }

            return new JsonModel([
                'message' => $message,
            ]);
        }

        return new JsonModel([
            'message' => 'Esta url só pode ser acessada via post',
        ]);
    }

    /**
     * Exibe a porcentagem de presença, no mês, dos alunos da turma $id
     * 
     */
    public function analysisAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost();

            try {
                $em = $this->getEntityManager();
                $students = $em->getRepository('SchoolManagement\Entity\Enrollment')->findAllCurrentStudents([
                    'class' => $data['sclass'],
                ]);

                $attendances = $em->getRepository('SchoolManagement\Entity\Attendance')->findAttendance([
                    'class' => $data['sclass'],
                    'beginDate' => new \DateTime($data['beginDate']),
                    'endDate' => new \DateTime($data['endDate'])
                ]);

                $attAnalysis = new AttendanceAnalysis($students, $attendances);

                return new JsonModel([
                    'data' => $attAnalysis->getMonthlyAttendance(),
                    'message' => 'ok',
                ]);
            } catch (Exception $ex) {
                return new JsonModel([
                    'message' => $ex->getMessage(),
                ]);
            }
        }

        return new JsonModel([
            'message' => 'Esta url só pode ser acessada via post',
        ]);
    }

    public function analyzeAction()
    {
        $em = $this->getEntityManager();
        $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')
            ->findByEndDateGratherThan(new \DateTime('now'));

        return new ViewModel([
            'classes' => $classes,
        ]);
    }
}
