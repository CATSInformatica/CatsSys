<?php

namespace SchoolManagement\Form;

use Doctrine\Common\Persistence\ObjectManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of SearchEnrollmentForm
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class SearchEnrollmentForm extends Form implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj, $name = null, $options = array())
    {
        parent::__construct($name, $options);

        $sClasses = $obj->getRepository('SchoolManagement\Entity\StudentClass')
            ->findByEndDateGratherThan(new \DateTime('now'));

        $this
            ->add(array(
                'name' => 'studentClasses',
                'type' => 'select',
                'options' => array(
                    'label' => 'Turmas',
                    'value_options' => $this->getStudentClasses($sClasses),
                ),
                'attributes' => [
                    'id' => 'class-search-field',
                ]
            ))
            ->add(array(
                'name' => 'submit',
                'type' => 'button',
                'attributes' => array(
                    'value' => 'Buscar',
                    'class' => 'btn-primary btn-flat btn-block',
                    'id' => 'class-search-button',
                ),
                'options' => array(
                    'label' => 'Buscar',
                    'glyphicon' => 'search',
                ),
            ))
        ;
    }

    /**
     * Formata as turmas para que sejam utilizadas como opções em um elemento html select
     * 
     * @param array $sClasses
     * @return array
     */
    protected function getStudentClasses($sClasses)
    {
        $scArr = [];
        foreach ($sClasses as $sc) {
            $scArr[$sc->getClassId()] = $sc->getClassName();
        }

        return $scArr;
    }

    public function getInputFilterSpecification()
    {
        return array(
            'studentClasses' => array(
                'required' => true,
            ),
        );
    }
//put your code here
}
