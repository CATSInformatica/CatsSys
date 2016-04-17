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
     * @var boolean 
     * @ORM\Column(name="is_correct", type="boolean", nullable=false)
     */
    private $isCorrect;
    
    /**
     *
     * @var string 
     * @ORM\Column(name="exam_answer_description", type="text", nullable=false)
     */
    private $examAnswerDescription;

    /**
     *
     * @var ExamQuestion
     * @ORM\ManyToOne(targetEntity="ExamQuestion", inversedBy="answerOptions", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="question_id", referencedColumnName="exam_question_id")
     */
    private $question;

    function __construct()
    {
        $this->isCorrect = false;
    }
    
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
     * @return boolean
     */
    function getIsCorrect()
    {
        return $this->isCorrect;
    }
    
    /**
     * 
     * @return ExamQuestion
     */
    function getQuestion()
    {
        return $this->question;
    }

    /**
     * 
     * @param boolean $isCorrect
     * @return ExamAnswer
     */
    function setIsCorrect($isCorrect)
    {
        $this->isCorrect = $isCorrect;
        return $this;
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
    
    /**
     * 
     * @param ExamQuestion $question
     * @return ExamAnswer
     */
    function setQuestion($question)
    {
        $this->question = $question;
        return $this;
    }

}
