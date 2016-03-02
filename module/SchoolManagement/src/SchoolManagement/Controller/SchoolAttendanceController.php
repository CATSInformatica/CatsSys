<?php

namespace SchoolManagement\Controller;

use Database\Service\DbalService;
use Database\Service\EntityManagerService;
use DateTime;
use Exception;
use SchoolManagement\Entity\AttendanceType;
use SchoolManagement\Entity\Repository\Attendance;
use SchoolManagement\Form\SchoolAttendanceForm;
use SchoolManagement\Model\AttendanceList;
use SchoolManagement\Model\PdfAttendanceList;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Description of SchoolAttendance
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class SchoolAttendanceController extends AbstractActionController
{

    use EntityManagerService,
        DbalService;

    /**
     * Gera a lista de presença para uma turma em uma data selecionada
     */
    public function generateListAction()
    {
        try {
            $em = $this->getEntityManager();
            $form = new SchoolAttendanceForm($em,
                [
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
            $form = new SchoolAttendanceForm($em);
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

                $output = $this->getServiceLocator()
                    ->get('viewrenderer')
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

        return $this->redirect()->toRoute('school-management/school-attendance',
                [
                'action' => 'generateList'
                ]
        );
    }

    public function saveAction()
    {

        $request = $this->getRequest();

        if ($request->isPost()) {

            $data = $request->getPost();
            $date = new DateTime($data['date']);
            $conn = $this->getDbalConnection();
            try {

                Attendance::insertNewList(
                    $conn, $data['students'], $date
                );

                $message = 'Lista de ' . $date->format('d/m/Y') . ' enviada com sucesso.';
            } catch (\Exception $ex) {
                $message = "Erro inesperado: " . $ex->getMessage();
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
     * gera a lista de chamada para a turma selecionada
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

}
