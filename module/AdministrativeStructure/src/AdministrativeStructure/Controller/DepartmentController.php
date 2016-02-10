<?php

namespace AdministrativeStructure\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Description of DepartmentController
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class DepartmentController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel(array(
            'message' => 'hello',
        ));
    }
}
