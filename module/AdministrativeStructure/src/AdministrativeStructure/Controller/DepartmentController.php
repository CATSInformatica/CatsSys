<?php

namespace AdministrativeStructure\Controller;

use AdministrativeStructure\Entity\Department;
use AdministrativeStructure\Form\DepartmentForm;
use Database\Service\EntityManagerService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Description of DepartmentController
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class DepartmentController extends AbstractActionController
{

    use EntityManagerService;

    public function indexAction()
    {
        return new ViewModel([]);
    }

    public function addAction()
    {
        $request = $this->getRequest();

        $message = null;
        try {

            $em = $this->getEntityManager();
            $form = new DepartmentForm($em);

            $department = new Department();
            $form->bind($department);

            if ($request->isPost()) {

                $form->setData($request->getPost());

                if ($form->isValid()) {

                    $em->persist($department);
                    $em->flush();
                }
            }


            return new ViewModel([
                'form' => $form,
                'message' => null,
            ]);
        } catch (Exception $ex) {
            if ($ex instanceof UniqueConstraintViolationException) {
                $message = 'Não é possivel cadastrar mais de um departamento com o mesmo nome: ' . $ex->getMessage();
            } else {
                $message = 'Erro inesperado: ' . $ex->getMessage();
            }
        }

        return new ViewModel([
            'message' => $message,
            'form' => null,
        ]);
    }

    public function editAction()
    {
        
    }

    public function deleteAction()
    {
        
    }

}
