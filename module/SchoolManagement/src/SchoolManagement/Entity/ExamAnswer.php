<?php

namespace SchoolManagement\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of ExamAnswer
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 * @ORM\Table(name="exam_answer")
 * @ORM\Entity
 */
class ExamAnswer
{
    /**
     *
     * @var integer 
     * @ORM\Column(name="exam_answer_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $examAnswerId;
    
    /**
     *
     * @var string 
     * @ORM\Column(name="exam_answer_description", type="string", nullable=false)
     */
    private $examAnswerDescription;
    
    /**
     * 
     * @return integer
     */
    function getExamAnswerId()
    {
        return $this->examAnswerId;
    }

    /**
     * 
     * @return string
     */
    function getExamAnswerDescription()
    {
        return $this->examAnswerDescription;
    }

    /**
     * 
     * @param string $examAnswerDescription
     * @return ExamAnswer
     */
    function setExamAnswerDescription($examAnswerDescription)
    {
        $this->examAnswerDescription = $examAnswerDescription;
        return $this;
    }


}
