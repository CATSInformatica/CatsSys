<?php

namespace SchoolManagement\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of ExamQuestion
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 * @ORM\Table(name="exam_question")
 * @ORM\Entity
 */
class ExamQuestion
{

    const QUESTION_TYPE_CLOSED = 1;
    const QUESTION_TYPE_OPEN = 2;
    const QUESTION_TYPE_CLOSED_DESC = "Questão Fechada";
    const QUESTION_TYPE_OPEN_DESC = "Questão Aberta";

    /**
     *
     * @var integer 
     * @ORM\Column(name="exam_question_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $examQuestionId;

    /**
     *
     * @var string 
     * @ORM\Column(name="exam_question_enunciation", type="text", nullable=false)
     */
    private $examQuestionEnunciation;

    /**
     *
     * @var integer 
     * @ORM\Column(name="exam_question_type", type="integer", nullable=false)
     */
    private $examQuestionType;

    /**
     *
     * @var Subject
     * @ORM\ManyToOne(targetEntity="Subject")
     * @ORM\JoinColumn(name="subject_id", referencedColumnName="subject_id", nullable=false)
     */
    private $subject;

    /**
     *
     * @var ExamAnswer
     * @ORM\OneToMany(targetEntity="ExamAnswer", mappedBy="question", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $answerOptions;

    public function __construct()
    {
        $this->answerOptions = new ArrayCollection();
    }

    /**
     * 
     * @return integer
     */
    public function getExamQuestionId()
    {
        return $this->examQuestionId;
    }

    /**
     * 
     * @return string
     */
    public function getExamQuestionEnunciation()
    {
        return $this->examQuestionEnunciation;
    }

    /**
     * 
     * @return integer
     */
    public function getExamQuestionType()
    {
        return $this->examQuestionType;
    }

    /**
     * 
     * @return Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * 
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getAnswerOptions()
    {
        return $this->answerOptions;
    }

    /**
     * 
     * @param string $examQuestionEnunciation
     * @return ExamQuestion
     */
    public function setExamQuestionEnunciation($examQuestionEnunciation)
    {
        $this->examQuestionEnunciation = $examQuestionEnunciation;
        return $this;
    }

    /**
     * 
     * @param integer $examQuestionType
     * @return ExamQuestion
     */
    public function setExamQuestionType($examQuestionType)
    {
        $this->examQuestionType = $examQuestionType;
        return $this;
    }

    /**
     * 
     * @param Subject $subject
     * @return ExamQuestion
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     *
     * @param Collection $answers
     * @return ExamQuestion
     */
    public function addAnswerOptions(Collection $answers)
    {
        foreach ($answers as $ans) {
            if (!$this->hasAnswer($ans)) {
                $ans->setQuestion($this);
                $this->answerOptions->add($ans);
            }
        }
        return $this;
    }

    /**
     * 
     * @param Collection $answers
     * @return ExamQuestion
     */
    public function removeAnswerOptions(Collection $answers)
    {
        foreach ($answers as $ans) {
            $this->answerOptions->removeElement($ans);
        }
        return $this;
    }

    /**
     * 
     * @param ExamAnswer $answer
     * @return boolean
     */
    public function hasAnswer($answer)
    {
        return $this->answerOptions->contains($answer);
    }

    /**
     * Retorna a resposta correta ou null caso seja uma questao aberta
     * 
     * @return null|ExamAnswer
     */
    public function getCorrectAnswerOption()
    {

        if ($this->examQuestionType === self::QUESTION_TYPE_CLOSED) {
            $criteria = Criteria::create()
                ->where(Criteria::expr()->eq("isCorrect", true))
                ->setMaxResults(1);

            $result = $this->answerOptions->matching($criteria);

            return $result->toArray()[0];
        }
        
        return null;
    }
    
    /**
     * Converte a resposta correta em uma letra. Caso nao exista uma resposta
     * correta retorna null.
     * 
     * @return string|null
     */
    public function getConvertedCorrectAnswer()
    {
        $ascii = ord('A');
        
        foreach($this->answerOptions as $answer) {
            if($answer->getIsCorrect()) {
                return chr($ascii);
            }
            $ascii++;
        }
        
        return null;
    }
}
