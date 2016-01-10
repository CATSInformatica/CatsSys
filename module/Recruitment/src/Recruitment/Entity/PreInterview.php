<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Entity;

use Doctrine\ORM\Mapping as ORM;
use Recruitment\Entity\Registration;

/**
 * Description of PreInterview
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="pre_interview")
 * @ORM\Entity
 */
class PreInterview
{

    /**
     * Somente em escola pública.
     * Maior parte em escola pública.
     * Somente em escola particular.
     * Escola particular com bolsa.
     * Maior parte em escola particular.
     * Não frequentei escola regular.
     */
    const SCHOOL_TYPE_ONLY_PUBLIC = 1;
    const SCHOOL_TYPE_MOST_PUBLIC = 2;
    const SCHOOL_TYPE_ONLY_PRIVATE = 3;
    const SCHOOL_TYPE_PRIVATE_SCHOLARSHIP = 4;
    const SCHOOL_TYPE_MOST_PRIVATE = 5;
    const SCHOOL_TYPE_NOT_ATTENDED_REGULAR_SCHOOL = 6;

    /**
     * Curso assistencial.
     * Curso particular.
     * Curso particular com bolsa.
     * Não frequentei curso pré-vestibular.
     */
    const PREP_SCHOOL_ASSISTANCE = 1;
    const PREP_SCHOOL_PRIVATE = 2;
    const PREP_SCHOOL_PRIVATE_SCHOLARSHIP = 3;
    const PREP_SCHOOL_NOTHING = 4;

    /**
     * Inglês
     * Espanhol
     * Inglês e espanhol
     * Outro(s)
     * Não fiz curso de idioma
     */
    const LANGUAGE_COURSE_ENGLISH = 1;
    const LANGUAGE_COURSE_SPANISH = 2;
    const LANGUAGE_COURSE_ENGLISH_AND_SPANISH = 3;
    const LANGUAGE_COURSE_OTHER = 4;
    const LANGUAGE_COURSE_NOTHING = 5;

    /**
     * Sim, faço Ensino Médio. 
     * Sim, faço outro curso pré-vestibular.
     * Sim, faço curso técnico/profissionalizante.
     * Sim, faço curso superior.
     * Não estudo atualmente.
     */
    const CURRENT_STUDY_HIGH_SCHOOL = 1;
    const CURRENT_STUDY_PREP_SCHOOL = 2;
    const CURRENT_STUDY_CERTIFICATE_PROGRAM = 3;
    const CURRENT_STUDY_HIGHER_EDUCATION = 4;
    const CURRENT_STUDY_NOTHING = 5;

    /**
     * Quantas pessoas moram em sua casa (incluindo você)?* 
     * Uma pessoa.
     * Duas pessoas.
     * Três pessoas.
     * Quatro pessoas.
     * Cinco pessoas.
     * Seis pessoas.
     * Mais de seis pessoas.
     */
    const ONE = 1;
    const TWO = 2;
    const THREE = 3;
    const FOUR = 4;
    const FIVE = 5;
    const SIX = 6;
    const MORE_THAN_SIX = 7;

    /**
     * Quem mora com você?
     */
    const LIVE_WITH_YOU_ALONE = 'Moro sozinho.';
    const LIVE_WITH_YOU_CHILDREN = 'Filhos.';
    const LIVE_WITH_YOU_PARENTS = 'Moro com pai e/ou mãe.';
    const LIVE_WITH_YOU_SIBLINGS = 'Irmãos.';
    const LIVE_WITH_YOU_LIFE_PARTNER = 'Esposa, marido, companheiro(a).';
    const LIVE_WITH_YOU_OTHER = 'Outro.';

    /**
     * Bicicleta, carona. 
     * A pé, carona.
     * Transporte escolar (gratuito).
     * Transporte coletivo (particular).
     * Transporte próprio (carro/moto).
     * Outro
     */
    const MEANS_OF_TRANSPORT_BYCICLE = 1;
    const MEANS_OF_TRANSPORT_ON_FOOT = 2;
    const MEANS_OF_TRANSPORT_SCHOLAR = 3;
    const MEANS_OF_TRANSPORT_PRIVATE_COLLETIVE = 4;
    const MEANS_OF_TRANSPORT_PRIVATE = 5;
    const MEANS_OF_TRANSPORT_OTHER = 6;

    /**
     * Nenhuma.
     * Menos de 01 salário mínimo.
     * Acima de 01 até 02 salários mínimos.
     * Acima de 02 até 03 salários mínimos.
     * Acima de 03 até 04 salários mínimos.
     * Mais de 04 salários mínimos.
     */
    const MONTHLY_INCOME_NOTHING = 1;
    const MONTHLY_INCOME_LESS_THAN_ONE_MINIMUM_WAGE = 2;
    const MONTHLY_INCOME_BETWEEN_ONE_AND_TWO_MINIMUM_WAGES = 3;
    const MONTHLY_INCOME_BETWEEN_TWO_AND_THREE_MINIMUM_WAGES = 4;
    const MONTHLY_INCOME_BETWEEN_THREE_AND_FOUR_MINIMUM_WAGES = 5;
    const MONTHLY_INCOME_MORE_THAN_FOUR_MINIMUM_WAGES = 6;

    /**
     * Primeiro grau (ensino fundamental) incompleto.
     * Primeiro grau (ensino fundamental) completo.
     * Segundo grau (colegial) incompleto.
     * Segundo grau (colegial) completo.
     * Ensino superior (faculdade/universidade) incompleto.
     * Ensino superior (faculdade/universidade) completo.
     * Pós-graduação (especialização) completa.
     * Mestrado completo.
     * Doutorado completo.
     */
    const PARENT_SCHOOL_GRADE_INCOMPLETE_ELEMENTARY_SCHOOL = 1;
    const PARENT_SCHOOL_GRADE_COMPLETE_ELEMENTARY_SCHOOL = 2;
    const PARENT_SCHOOL_GRADE_INCOMPLETE_HIGH_SCHOOL = 3;
    const PARENT_SCHOOL_GRADE_COMPLETE_HIGH_SCHOOL = 4;
    const PARENT_SCHOOL_GRADE_INCOMPLETE_UNDERGRADUATE_COURSE = 5;
    const PARENT_SCHOOL_GRADE_COMPLETE_UNDERGRADUATE_COURSE = 6;
    const PARENT_SCHOOL_GRADE_GRADUATE_SPECIALIZATION = 7;
    const PARENT_SCHOOL_GRADE_MASTER_DEGREE = 8;
    const PARENT_SCHOOL_GRADE_DOCTORATE_DEGREE = 9;

    /**
     *
     * @var integer
     * @ORM\Column(name="pre_interview_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $preInterviewId;

    /**
     *
     * @var Recruitment\Entity\Registration
     * @ORM\OneToOne(targetEntity="Recruitment\Entity\Registration", inversedBy="preInterview")
     * @ORM\JoinColumn(name="registration_id", referencedColumnName="registration_id", nullable=false)
     */
    private $registration;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="pre_inteview_date", type="datetime", nullable=false)
     */
    private $preInterviewDate;

    /**
     *
     * @var string
     * @ORM\Column(name="pre_interview_personal_info", type="string", length=100, nullable=false)
     */
    private $preInterviewPersonalInfo;

    /**
     *
     * @var string
     * @ORM\Column(name="pre_interview_income_proof", type="string", length=100, nullable=false)
     */
    private $preInterviewIncomeProof;

    /**
     *
     * @var string
     * @ORM\Column(name="pre_interview_expense_receipt", type="string", length=100, nullable=false)
     */
    private $preInterviewExpenseReceipt;

    /**
     *
     * @var integer
     * @ORM\Column(name="pre_interview_elementary_school_type", type="smallint", nullable=false)
     */
    private $preInterviewElementarySchoolType;

    /**
     *
     * @var integer
     * @ORM\Column(name="pre_interview_high_school_type", type="smallint", nullable=false)
     */
    private $preInterviewHighSchoolType;

    /**
     *
     * @var string
     * @ORM\Column(name="pre_interview_high_school", type="string", length=150, nullable=false)
     */
    private $preInterviewHighSchool;

    /**
     *
     * @var integer
     * @ORM\Column(name="pre_interview_hs_conclusion_year", type="smallint", nullable=false)
     */
    private $preInterviewHSConclusionYear;

    /**
     * @var integer
     * @ORM\Column(name="pre_interview_preparation_school", type="smallint", nullable=false)
     */
    private $preInterviewPreparationSchool;

    /**
     *
     * @var integer
     * @ORM\Column(name="pre_interview_language_course", type="smallint", nullable=false)
     */
    private $preInterviewLanguageCourse;

    /**
     *
     * @var integer
     * @ORM\Column(name="pre_interview_current_study", type="smallint", nullable=false)
     */
    private $preInterviewCurrentStudy;

    /**
     * @var integer
     * @ORM\Column(name="pre_interview_live_with_number", type="smallint", nullable=false)
     */
    private $preInterviewLiveWithNumber;

    /**
     *
     * @var string
     * @ORM\Column(name="pre_interview_live_with_you", type="string", length=120, nullable=false)
     */
    private $preInterviewLiveWithYou;

    /**
     *
     * @var integer
     * @ORM\Column(name="pre_interview_number_of_rooms", type="smallint", nullable=false)
     */
    private $preInterviewNumberOfRooms;

    /**
     *
     * @var integer
     * @ORM\Column(name="pre_interview_means_of_transport", type="smallint", nullable=false)
     */
    private $preInterviewMeansOfTransport;

    /**
     *
     * @var integer
     * @ORM\Column(name="pre_interview_monthly_income", type="smallint", nullable=false)
     */
    private $preInterviewMonthlyIncome;

    /**
     *
     * @var integer
     * @ORM\Column(name="pre_interview_father_education_grade", type="smallint", nullable=false)
     */
    private $preInterviewFatherEducationGrade;

    /**
     *
     * @var integer
     * @ORM\Column(name="pre_interview_mother_education_grade", type="smallint", nullable=false)
     */
    private $preInterviewMotherEducationGrade;

    /**
     * @var string
     * @ORM\Column(name="pre_interview_expect_from_us", type="string", length=200, nullable=false)
     */
    private $preInterviewExpectFromUs;

    public function __construct()
    {
        $this->preInterviewDate = new \DateTime('now');
    }

    /**
     * 
     * @return integer
     */
    public function getPreInterviewId()
    {
        return $this->preInterviewId;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getPreInterviewDate()
    {
        return $this->preInterviewDate;
    }

    /**
     * 
     * @return string
     */
    public function getPreInterviewPersonalInfo()
    {
        return $this->preInterviewPersonalInfo;
    }

    /**
     * 
     * @return string
     */
    public function getPreInterviewIncomeProof()
    {
        return $this->preInterviewIncomeProof;
    }

    /**
     * 
     * @return string
     */
    public function getPreInterviewExpenseReceipt()
    {
        return $this->preInterviewExpenseReceipt;
    }

    /**
     * 
     * @return integer
     */
    public function getPreInterviewElementarySchoolType()
    {
        return $this->preInterviewElementarySchoolType;
    }

    /**
     * 
     * @return integer
     */
    public function getPreInterviewHighSchoolType()
    {
        return $this->preInterviewHighSchoolType;
    }

    /**
     * 
     * @return string
     */
    public function getPreInterviewHighSchool()
    {
        return $this->preInterviewHighSchool;
    }

    /**
     * 
     * @return integer
     */
    public function getPreInterviewHSConclusionYear()
    {
        return $this->preInterviewHSConclusionYear;
    }

    /**
     * 
     * @return integer
     */
    public function getPreInterviewLanguageCourse()
    {
        return $this->preInterviewLanguageCourse;
    }

    /**
     * 
     * @return integer
     */
    public function getPreInterviewCurrentStudy()
    {
        return $this->preInterviewCurrentStudy;
    }

    /**
     * 
     * @return integer
     */
    public function getPreInterviewLiveWithNumber()
    {
        return $this->preInterviewLiveWithNumber;
    }

    /**
     * 
     * @return string
     */
    public function getPreInterviewLiveWithYou()
    {
        return $this->preInterviewLiveWithYou;
    }

    /**
     * 
     * @return integer
     */
    public function getPreInterviewMeansOfTransport()
    {
        return $this->preInterviewMeansOfTransport;
    }

    /**
     * 
     * @return integer
     */
    public function getPreInterviewMonthlyIncome()
    {
        return $this->preInterviewMonthlyIncome;
    }

    /**
     * 
     * @return integer
     */
    public function getPreInterviewFatherEducationGrade()
    {
        return $this->preInterviewFatherEducationGrade;
    }

    /**
     * 
     * @return integer
     */
    public function getPreInterviewMotherEducationGrade()
    {
        return $this->preInterviewMotherEducationGrade;
    }

    /**
     * 
     * @return string
     */
    public function getPreInterviewExpectFromUs()
    {
        return $this->preInterviewExpectFromUs;
    }

    /**
     * 
     * @param string $preInterviewPersonalInfo
     * @return Recruitment\Entity\PreInterview
     */
    public function setPreInterviewPersonalInfo($preInterviewPersonalInfo)
    {
        $this->preInterviewPersonalInfo = $preInterviewPersonalInfo;
        return $this;
    }

    /**
     * 
     * @param string $preInterviewIncomeProof
     * @return Recruitment\Entity\PreInterview
     */
    public function setPreInterviewIncomeProof($preInterviewIncomeProof)
    {
        $this->preInterviewIncomeProof = $preInterviewIncomeProof;
        return $this;
    }

    /**
     * 
     * @param string $preInterviewExpenseReceipt
     * @return Recruitment\Entity\PreInterview
     */
    public function setPreInterviewExpenseReceipt($preInterviewExpenseReceipt)
    {
        $this->preInterviewExpenseReceipt = $preInterviewExpenseReceipt;
        return $this;
    }

    /**
     * 
     * @param integer $preInterviewElementarySchoolType
     * @return Recruitment\Entity\PreInterview
     */
    public function setPreInterviewElementarySchoolType($preInterviewElementarySchoolType)
    {
        $this->preInterviewElementarySchoolType = $preInterviewElementarySchoolType;
        return $this;
    }

    /**
     * 
     * @param integer $preInterviewHighSchoolType
     * @return Recruitment\Entity\PreInterview
     */
    public function setPreInterviewHighSchoolType($preInterviewHighSchoolType)
    {
        $this->preInterviewHighSchoolType = $preInterviewHighSchoolType;
        return $this;
    }

    /**
     * 
     * @param string $preInterviewHighSchool
     * @return Recruitment\Entity\PreInterview
     */
    public function setPreInterviewHighSchool($preInterviewHighSchool)
    {
        $this->preInterviewHighSchool = $preInterviewHighSchool;
        return $this;
    }

    /**
     * 
     * @param integer $preInterviewHSConclusionYear
     * @return Recruitment\Entity\PreInterview
     */
    public function setPreInterviewHSConclusionYear($preInterviewHSConclusionYear)
    {
        $this->preInterviewHSConclusionYear = $preInterviewHSConclusionYear;
        return $this;
    }

    /**
     * 
     * @param integer $preInterviewLanguageCourse
     * @return Recruitment\Entity\PreInterview
     */
    public function setPreInterviewLanguageCourse($preInterviewLanguageCourse)
    {
        $this->preInterviewLanguageCourse = $preInterviewLanguageCourse;
        return $this;
    }

    /**
     * 
     * @param integer $preInterviewCurrentStudy
     * @return Recruitment\Entity\PreInterview
     */
    public function setPreInterviewCurrentStudy($preInterviewCurrentStudy)
    {
        $this->preInterviewCurrentStudy = $preInterviewCurrentStudy;
        return $this;
    }

    /**
     * 
     * @param integer $preInterviewLiveWithNumber
     * @return Recruitment\Entity\PreInterview
     */
    public function setPreInterviewLiveWithNumber($preInterviewLiveWithNumber)
    {
        $this->preInterviewLiveWithNumber = $preInterviewLiveWithNumber;
        return $this;
    }

    /**
     * 
     * @param string $liveWithYou
     * @return Recruitment\Entity\PreInterview
     */
    public function addPreInterviewLiveWithYou($liveWithYou)
    {

        if (in_array($liveWithYou,
                array(
                self::LIVE_WITH_YOU_ALONE,
                self::LIVE_WITH_YOU_CHILDREN,
                self::LIVE_WITH_YOU_CHILDREN,
                self::LIVE_WITH_YOU_PARENTS,
                self::LIVE_WITH_YOU_CHILDREN,
                self::LIVE_WITH_YOU_SIBLINGS,
                self::LIVE_WITH_YOU_CHILDREN,
                self::LIVE_WITH_YOU_LIFE_PARTNER,
                self::LIVE_WITH_YOU_CHILDREN,
                self::LIVE_WITH_YOU_OTHER,
            ))) {

            if ($this->preInterviewLiveWithYou !== null) {
                $this->preInterviewLiveWithYou .= ';' . $liveWithYou;
            } else {
                $this->preInterviewLiveWithYou = $liveWithYou;
            }

            return $this;
        }
        throw new \InvalidArgumentException('invalid pre-interview live with you option.');
    }

    public function clearPreInterviewLiveWithYou()
    {
        $this->preInterviewLiveWithYou = null;
    }

    /**
     * 
     * @param integer $preInterviewMeansOfTransport
     * @return Recruitment\Entity\PreInterview
     */
    public function setPreInterviewMeansOfTransport($preInterviewMeansOfTransport)
    {
        $this->preInterviewMeansOfTransport = $preInterviewMeansOfTransport;
        return $this;
    }

    /**
     * 
     * @param integer $preInterviewMonthlyIncome
     * @return Recruitment\Entity\PreInterview
     */
    public function setPreInterviewMonthlyIncome($preInterviewMonthlyIncome)
    {
        $this->preInterviewMonthlyIncome = $preInterviewMonthlyIncome;
        return $this;
    }

    /**
     * 
     * @param integer $preInterviewFatherEducationGrade
     * @return Recruitment\Entity\PreInterview
     */
    public function setPreInterviewFatherEducationGrade($preInterviewFatherEducationGrade)
    {
        $this->preInterviewFatherEducationGrade = $preInterviewFatherEducationGrade;
        return $this;
    }

    /**
     * 
     * @param integer $preInterviewMotherEducationGrade
     * @return Recruitment\Entity\PreInterview
     */
    public function setPreInterviewMotherEducationGrade($preInterviewMotherEducationGrade)
    {
        $this->preInterviewMotherEducationGrade = $preInterviewMotherEducationGrade;
        return $this;
    }

    /**
     * 
     * @param string $preInterviewExpectFromUs
     * @return Recruitment\Entity\PreInterview
     */
    public function setPreInterviewExpectFromUs($preInterviewExpectFromUs)
    {
        $this->preInterviewExpectFromUs = $preInterviewExpectFromUs;
        return $this;
    }

    /**
     * 
     * @return integer
     */
    public function getPreInterviewPreparationSchool()
    {
        return $this->preInterviewPreparationSchool;
    }

    /**
     * 
     * @param integer $preInterviewPreparationSchool
     * @return Recruitment\Entity\PreInterview
     */
    public function setPreInterviewPreparationSchool($preInterviewPreparationSchool)
    {
        $this->preInterviewPreparationSchool = $preInterviewPreparationSchool;
        return $this;
    }

    /**
     * 
     * @return integer
     */
    public function getPreInterviewNumberOfRooms()
    {
        return $this->preInterviewNumberOfRooms;
    }

    /**
     * 
     * @param integer $preInterviewNumberOfRooms
     * @return Recruitment\Entity\PreInterview
     */
    public function setPreInterviewNumberOfRooms($preInterviewNumberOfRooms)
    {
        $this->preInterviewNumberOfRooms = $preInterviewNumberOfRooms;
        return $this;
    }

    /**
     * 
     * @param Recruitment\Entity\Registration $registration
     * @return Recruitment\Entity\PreInterview
     */
    public function setRegistration(Registration $registration)
    {
        $registration->setPreInterview($this);
        $this->registration = $registration;
        return $this;
    }

}
