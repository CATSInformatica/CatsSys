<?php

namespace AdministrativeStructure\Controller;

use AdministrativeStructure\Entity\Department;
use AdministrativeStructure\Form\DepartmentForm;
use Database\Controller\AbstractEntityActionController;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Exception;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Description of DepartmentController
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class DepartmentController extends AbstractEntityActionController
{

    public function indexAction()
    {
        return new ViewModel([]);
    }

    public function addAction()
    {
        $request = $this->getRequest();

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

                    return $this->redirect()->toRoute('administrative-structure/department');
                }
            }
        } catch (Exception $ex) {
            if ($ex instanceof UniqueConstraintViolationException) {
                $message = 'Não é possivel cadastrar mais de um departamento com o mesmo nome.';
            } else {
                $message = 'Erro inesperado: ' . $ex->getMessage();
            }
        }

        $view = new ViewModel([
            'form' => isset($form) ? $form : null,
            'message' => isset($message) ? $message : null,
        ]);

        $view->setTemplate('administrative-structure/department/department-form.phtml');
        return $view;
    }

    public function getDepartmentsAction()
    {
        $results = [];
        $departmentId = $this->params('id', false);

        try {
            $UserContainer = new Container('User');
            $em = $this->getEntityManager();

            if ($departmentId === false) {
                $restrictedBy['parent'] = null;
                if (empty($UserContainer->id)) {
                    $restrictedBy['isActive'] = true;
                }
                $departments = $em->getRepository('AdministrativeStructure\Entity\Department')->findBy($restrictedBy);
            } else {
                if (empty($UserContainer->id)) {
                    $departments = $em->getReference('AdministrativeStructure\Entity\Department', $departmentId)
                            ->getActiveChildren()->toArray();
                } else {
                    $departments = $em->getReference('AdministrativeStructure\Entity\Department', $departmentId)
                            ->getChildren()->toArray();
                }
            }


            if (count($departments) > 0) {
                $hydrator = new DoctrineHydrator($em);
                foreach ($departments as $dep) {
                    $extractedArray = $hydrator->extract($dep);
                    $extractedArray['numberOfChildren'] = $dep->getNumberOfChildren();
                    $results[] = $extractedArray;
                }
            }
            return new JsonModel([
                'message' => null,
                'results' => $results,
            ]);
        } catch (Exception $ex) {
            return new JsonModel([
                'message' => $ex->getMessage(),
                'results' => [],
            ]);
        }
    }

    /**
     * Edita um departamento
     * @return ViewModel
     */
    public function editAction()
    {
        $id = $this->params('id', false);

        if (!$id) {
            return $this->redirect()->toRoute('administrative-structure/department');
        }

        $request = $this->getRequest();
        try {

            $em = $this->getEntityManager();
            $form = new DepartmentForm($em);

            $department = $em->find('AdministrativeStructure\Entity\Department', $id);
            $form->bind($department);

            // remove te previous parent
            $department->setParent();

            if ($request->isPost()) {

                $form->setData($request->getPost());

                if ($form->isValid()) {

                    $em->merge($department);
                    $em->flush();

                    return $this->redirect()->toRoute('administrative-structure/department');
                }
            }
        } catch (Exception $ex) {
            if ($ex instanceof UniqueConstraintViolationException) {
                $message = 'Não é possivel cadastrar mais de um departamento com o mesmo nome.';
            } else {
                $message = 'Erro inesperado: ' . $ex->getMessage();
            }
        }

        $view = new ViewModel([
            'form' => isset($form) ? $form : null,
            'message' => isset($message) ? $message : null,
        ]);

        $view->setTemplate('administrative-structure/department/department-form.phtml');
        return $view;
    }

    /**
     * 
     * Remove um departamento ($id) que não possua nenhum departamento filho associado
     * 
     * @return JsonModel
     */
    public function deleteAction()
    {
        $id = $this->params('id', false);

        if ($id) {

            try {

                $em = $this->getEntityManager();
                $department = $em->getReference('AdministrativeStructure\Entity\Department', $id);

                $em->remove($department);
                $em->flush();

                return new JsonModel([
                    'response' => true,
                    'message' => 'Departmento removido com sucesso',
                    'callback' => [
                        'departmentId' => $id,
                    ],
                ]);
            } catch (Exception $ex) {
                if ($ex instanceof ConstraintViolationException) {
                    $message = 'Este departamento possui departamentos filhos ou cargos ou depesas ou receitas associadas.';
                } else {
                    $message = 'Erro inesperado: ' . $ex->getMessage();
                }

                return new JsonModel([
                    'response' => false,
                    'message' => $message,
                ]);
            }
        }
    }
}
