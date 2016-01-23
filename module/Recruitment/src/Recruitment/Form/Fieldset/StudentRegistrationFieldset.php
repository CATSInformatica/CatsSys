<?php

namespace Recruitment\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of StudentRegistrationFieldset
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
final class StudentRegistrationFieldset extends RegistrationFieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj, $options = null)
    {
        if (is_array($options) && !array_key_exists('pre_interview', $options)) {
            throw new InvalidArgumentException('`options` array must contain the key `pre_interview`');
        }

        parent::__construct($obj, $options);

        $this->add(array(
            'name' => 'recruitmentKnowAbout',
            'type' => 'DoctrineModule\Form\Element\ObjectMultiCheckbox',
            'options' => array(
                'label' => 'Por qual(is) meio(s) você soube do processo seletivo de alunos?*',
                'object_manager' => $obj,
                'target_class' => 'Recruitment\Entity\RecruitmentKnowAbout',
                'property' => 'recruitmentKnowAboutDescription',
            ),
        ));

        if ($options['pre_interview']) {
            $this->add(new PreInterviewFieldset($obj));
        }
    }

    public function getInputFilterSpecification()
    {

        return array(
            'recruitmentKnowAbout' => array(
                'required' => true,
            ),
        );
    }

}
