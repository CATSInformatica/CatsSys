<?php

namespace SchoolManagement\Controller;

use SchoolManagement\Form\SchoolAttendanceForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Database\Service\EntityManagerService;

/**
 * Description of SchoolAttendance
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class SchoolAttendanceController extends AbstractActionController
{

    use EntityManagerService;

    /**
     * Gera a lista de presença para uma turma em uma data selecionada
     */
    public function generateListAction()
    {

        try {

            $em = $this->getEntityManager();
            $form = new SchoolAttendanceForm($em);

            return new ViewModel(array(
                'form' => $form,
                'message' => null,
            ));
        } catch (\Exception $ex) {
            return new ViewModel(array(
                'form' => null,
                'message' => 'Erro inesperado: ' . $ex->getMessage(),
            ));
        }
    }

}
