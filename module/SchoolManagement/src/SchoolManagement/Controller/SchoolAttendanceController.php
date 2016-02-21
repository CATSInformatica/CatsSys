<?php

namespace SchoolManagement\Controller;

use Database\Service\EntityManagerService;
use Database\Service\DbalService;
use Exception;
use SchoolManagement\Entity\AttendanceType;
use SchoolManagement\Form\SchoolAttendanceForm;
use SchoolManagement\Model\AttendanceList;
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
            $date = new \DateTime($data['date']);
            $em = $this->getEntityManager();
            
//            echo '<pre>';
//            print_r($data);
//            exit;

            try {
                $em->getRepository('SchoolManagement\Entity\Attendance')->insertNewList(
                    $this->getDbalConnection(), $data['students'], $date
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

}
