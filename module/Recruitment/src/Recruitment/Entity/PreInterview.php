<?php
/*
 * Copyright (C) 2016 Márcio Dias <marciojr91@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Recruitment\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Recruitment\Entity\Registration;

/**
 * Classe que representa a tabela de pré-entrevista para candidatos do processo seletivo de alunos.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="pre_interview")
 * @ORM\Entity
 */
class PreInterview
{
    /* ################## RELAÇÕES E DADOS AUTOMÁTICOS ################ */

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
     * @var DateTime
     * @ORM\Column(name="pre_inteview_date", type="datetime", nullable=false)
     */
    private $preInterviewDate;

    /* ####################### SOCIOECONÔMICO ####################### */

    const LIVE_ALONE = 'Sozinho';
    const LIVE_WITH_PARENTS = 'Com os pais';
    const LIVE_WITH_LIFE_PARTNER = 'Cônjuge/Companheiro(a)';
    const LIVE_WITH_RELATIVES = 'Casa de familiares/Amigos';
    const LIVE_WITH_FRIENDS = 'República/Quarto/Pensão/Pensionato';
    const LIVE_OTHER = 'Outro';

    /**
     * Você mora?
     * 
     * @var string Resposta para a pergunta 'Você mora?'
     * @ORM\Column(name="pre_inteview_live", type="string", length=50, nullable=false)
     */
    private $live;

    const FINANCIAL_PARENTS = 'Pai e mãe';
    const FINANCIAL_PARENT = 'Somente um dos pais';
    const FINANCIAL_ME = 'Eu';
    const FINANCIAL_LIFE_PARTNER = 'Cônjuge/Companheiro(a)';
    const FINANCIAL_ACQUAINTED = 'Outros familiares/Amigos';

    /**
     * Quem é (são) o (os) responsável (is) pela manutenção financeira do grupo familiar.
     * 
     * @var string Resposta para a pergunta 'Quem é (são) o (os) responsável (is) pela manutenção financeira do 
     * grupo familiar'
     * @ORM\Column(name="pre_inteview_responsible_financial", type="string", length=50, nullable=false)
     */
    private $responsibleFinancial;

    /**
     * A casa onde mora têm:
     * 
     * @var Collection
     * @ORM\ManyToMany(targetEntity="InfrastructureElement", fetch="EAGER")
     * @ORM\JoinTable(name="pre_interview_infrastructure_element",
     *      joinColumns={@ORM\JoinColumn(name="pre_interview_id",
     *          referencedColumnName="pre_interview_id")
     *      },
     *      inverseJoinColumns={@ORM\JoinColumn(name="infrastructure_element_id", 
     *          referencedColumnName="infrastructure_element_id")}
     * )
     */
    private $infrastructureElements;

    const LIVE_AREA_URBAN_PERIPHERAL = 'Zona urbana periférica';
    const LIVE_AREA_URBAN_CENTRAL = 'Zona urbana central';
    const LIVE_AREA_COUNTRYSIDE = 'Zona rural';

    /**
     * Você reside em.
     * 
     * @var string Resposta para a pergunta: 'Você reside em:'
     * @ORM\Column(name="pre_inteview_live_area", type="string", length=50, nullable=false)
     */
    private $liveArea;

    /**
     *
     * @var string Quantidade de Tvs.
     * @ORM\Column(name="pre_inteview_item_tv", type="string", length=30, nullable=false)
     */
    private $itemTv;

    /**
     *
     * @var string Quantidade de banheiros.
     * @ORM\Column(name="pre_inteview_item_bathroom", type="string", length=30, nullable=false)
     */
    private $itemBathroom;

    /**
     *
     * @var string Quantidade de empregadas domésticas mensalistas.
     * @ORM\Column(name="pre_inteview_item_shousekeeper", type="string", length=30, nullable=false)
     */
    private $itemSalariedHousekeeper;

    /**
     *
     * @var string Quantidade de empregadas domésticas diaristas.
     * @ORM\Column(name="pre_inteview_item_dhousekeeper", type="string", length=30, nullable=false)
     */
    private $itemDailyHousekeeper;

    /**
     *
     * @var string Quantidade de máquinas de lavar roupa.
     * @ORM\Column(name="pre_inteview_item_wmachine", type="string", length=30, nullable=false)
     */
    private $itemWashingMachine;

    /**
     *
     * @var string Quantidade de geladeiras.
     * @ORM\Column(name="pre_inteview_item_refrigerator", type="string", length=30, nullable=false)
     */
    private $itemRefrigerator;

    /**
     *
     * @var string Quantidade de tvs a cabo.
     * @ORM\Column(name="pre_inteview_item_cabletv", type="string", length=30, nullable=false)
     */
    private $itemCableTv;

    /**
     *
     * @var string Quantidade de computadores.
     * @ORM\Column(name="pre_inteview_item_computer", type="string", length=30, nullable=false)
     */
    private $itemComputer;

    /**
     *
     * @var string Quantidade de smartphones.
     * @ORM\Column(name="pre_inteview_item_smartphone", type="string", length=30, nullable=false)
     */
    private $itemSmartphone;

    /**
     *
     * @var string Quantidade de quartos.
     * @ORM\Column(name="pre_inteview_item_bedroom", type="string", length=30, nullable=false)
     */
    private $itemBedroom;

    /**
     * Despesas da família.
     * 
     * var Collection Despesas da família
     * @ORM\OneToMany(targetEntity="FamilyIncomeExpense", mappedBy="preInterviewExpense", 
     * cascade={"all"}, orphanRemoval=true)
     */
    private $familyExpenses;

    /**
     * Receitas da família.
     * 
     * var Collection Receitas da família
     * @ORM\OneToMany(targetEntity="FamilyIncomeExpense", mappedBy="preInterviewIncome", 
     * cascade={"all"}, orphanRemoval=true)
     */
    private $familyIncome;


    /* ####################### VULNERABILIDADE ###################### */
    
    
    const FAMILY_ETHNICITY_CAUCASIAN = 'Caucasiana';
    const FAMILY_ETHNICITY_BLACK = 'Negra';
    const FAMILY_ETHNICITY_BROWN = 'Parda';
    const FAMILY_ETHNICITY_NATIVE = 'Indígena';
    
    /**
     * Etinia da família.
     * @ORM\Column(name="pre_interview_famethnicity", type="string", length=30, nullable=false);
     */
    private $familyEthnicity;

    /**
     * var Collection Bens imóveis da família.
     * @ORM\OneToMany(targetEntity="FamilyProperty", mappedBy="preInterview", 
     * cascade={"all"}, orphanRemoval=true)
     */
    private $familyProperties;

    /**
     * var Collection Bens móveis da família.
     * @ORM\OneToMany(targetEntity="FamilyGood", mappedBy="preInterview", 
     * cascade={"all"}, orphanRemoval=true)
     */
    private $familyGoods;

    /**
     * var Collection Membros da familia com problemas de saúde.
     * @ORM\OneToMany(targetEntity="FamilyHealth", mappedBy="preInterview", 
     * cascade={"all"}, orphanRemoval=true)
     */
    private $familyHealth;

    // Constantes para o tipo de escola onde o candidato estudou

    /**
     * @var string 'Somente pública'
     */
    const SCHOOL_TYPE_PUBLIC = 'Somente pública';

    /**
     * @var string 'Somente particular'
     */
    const SCHOOL_TYPE_PRIVATE = 'Somente particular';

    /**
     * @var string 'Somente particular com bolsa'
     */
    const SCHOOL_TYPE_PRIVATE_STUDENTSHIP = 'Somente particular com bolsa';

    /**
     * @var string 'Particular sem bolsa e depois em pública'
     */
    const SCHOOL_TYPE_PRIVATE_BEFORE_PUBLIC = 'Particular sem bolsa e depois em pública';

    /**
     * @var string 'Pública e depois em particular sem bolsa'
     */
    const SCHOOL_TYPE_PUBLIC_BEFORE_PRIVATE = 'Pública e depois em particular sem bolsa';

    /**
     * @var string 'Pública e Particular com bolsa'
     */
    const SCHOOL_TYPE_PUBLIC_PRIVATE_STUDENTSHIP = 'Pública e particular com bolsa';

    /**
     * A Instituição de ensino na qual cursou o ensino fundamental é?
     * 
     * @var string Resposta para a pergunta: 'A Instituição de ensino na qual cursou o ensino fundamental é?'
     * @ORM\Column(name="pre_interview_elementary_school_type", type="string", length=50, nullable=false)
     */
    private $elementarySchoolType;

    /**
     * @var string 'Pública e particular'
     */
    const SCHOOL_TYPE_PUBLIC_PRIVATE = 'Pública e particular';
    const SCHOOL_TYPE_TECH_PRIVATE = 'Técnica e particular';
    const SCHOOL_TYPE_TECH_PRIVATE_STUDENTSHIP = 'Técnica e particular com bolsa';
    const SCHOOL_TYPE_TECH_PUBLIC = 'Técnica e pública';
    const SCHOOL_TYPE_OTHER = 'Outra';

    /**
     * Você cursou/cursa o ensino médio em escola(s)
     *
     * @var string Resposta para a pergunta: 'Você cursou/cursa o ensino médio em escola(s)'
     * @ORM\Column(name="pre_interview_high_school_type", type="string", length=50, nullable=false)
     */
    private $highSchoolType;

    /**
     *
     * Ano de ingresso no ensino médio
     * 
     * @var int Ano de ingresso no ensino médio
     * @ORM\Column(name="pre_interview_hs_admission_year", type="smallint", nullable=false)
     */
    private $highSchoolAdmissionYear;

    /**
     *
     * Ano de conclusão/previsão de conclusão do ensino médio
     * 
     * @var int Ano de término do ensino médio
     * @ORM\Column(name="pre_interview_hs_conclusion_year", type="smallint", nullable=false)
     */
    private $highSchoolConclusionYear;

    /**
     * Tem irmãos que cursaram/cursam o ensino superior?
     * 
     * Se sim, o candidato deve colocar os nos nomes da(s) instituiçõe(s), caso contrário, deixar em branco.
     * 
     * @var bool Resposta para a pergunta 'Tem irmãos que cursaram/cursam o ensino superior?'
     * @ORM\Column(name="pre_interview_siblings_undergraduate", type="boolean", nullable=false)
     */
    private $siblingsUndergraduate;

    /**
     * Fala algum idioma estrangeiro?
     * 
     * Se sim, o candidato deve dizer qual e como estudou, caso contrário, deve deixar em branco.
     * 
     * @var string Resposta para a pergunta 'Fala algum idioma estrangeiro?'
     * @ORM\Column(name="pre_interview_other_languages", type="string", length=500, nullable=true)
     */
    private $otherLanguages;

    const HOME_RENTAL_PROPERTY = 'Alugado';
    const HOME_OWN_ALREADY_PAID = 'Próprio, já quitado';
    const HOME_OWN_BY_INHERITANCE = 'Próprio, por herança';
    const HOME_OWN_PAYING = 'Próprio, em pagamento';
    const HOME_GIVEN_PROPERTY = 'Emprestado ou cedido';
    const HOME_OTHER = 'Outro';

    /**
     * Imovel em que reside é?
     *
     * @var string Resposta para a pergunta: 'Imovel em que reside é?'
     * @ORM\Column(name="pre_interview_home_status", type="string", length=50, nullable=false)
     */
    private $homeStatus;

    const HOME_DESCRIPTION_FINISHED = 'Residência com acabamento';
    const HOME_DESCRIPTION_WITHOUT_FINISHING = 'Residência sem acabamento';

    /**
     * Marque a característica que melhor descreve a sua casa
     *
     * @var string Resposta para a pergunta: 'Marque a característica que melhor descreve a sua casa'
     * @ORM\Column(name="pre_interview_home_description", type="string", length=50, nullable=false)
     */
    private $homeDescription;

    const TRANSPORT_ON_FOOT_BYCICLE = 'A pé/Bicicleta';
    const TRANSPORT_HITCHHIKE = 'Carona';
    const TRANSPORT_PAID_COLLETIVE_TRANSPORTATION = 'Transporte coletivo pago com recursos próprios';
    const TRANSPORT_RENTED = 'Transporte locado';
    const TRANSPORT_SCHOLAR = 'Transporte oferecido gratuitamente por Prefeituras e/ou Escola';
    const TRANSPORT_OWN = 'Transporte próprio';
    const TRANSPORT_OTHER = 'Outro';

    /**
     * Qual será o principal meio de transporte utilizado
     * 
     * @var string Resposta para a pergunta: 'Qual será o principal meio de transporte utilizado?'
     * @ORM\Column(name="pre_interview_transport", type="string", length=100, nullable=false)
     */
    private $transport;

    /**
     * Membros da familia.
     *
     * @var Collection Membros da familia
     * @ORM\OneToMany(targetEntity="CandidateFamily", mappedBy="preInterview",
     * cascade={"all"}, orphanRemoval=true)
     */
    private $familyMembers;

    /* ####################### PERFIL DE ESTUDANTE ################## */

    /**
     * Fez algum curso extraclasse?
     * 
     * Se sim, o candidato deverá escrever quais cursos, caso contrário, deverá deixar em branco.
     * 
     * @var string Resposta para a pergunta fez algum curso extraclasse?
     * @ORM\Column(name="pre_interview_extra_courses", type="string", length=500, nullable=true)
     */
    private $extraCourses;

    /**
     * já fez curso pré-vestibular?
     * 
     * Se sim, o candidato deverá escrever qual(is) cursos pré-vestibulares já fez, se tinha bolsa, o percentual da 
     * bolsa. Caso contrário, deverá deixar o campo em branco.
     *
     * @var string Resposta para a pergunta 'já fez curso pré-vestibular?'
     * @ORM\Column(name="pre_interview_preparation_course", type="string", length=500, nullable=true)
     */
    private $preparationCourse;

    /**
     * Já prestou algum vestibular ou concurso?
     * 
     * Se sim, o candidato deverá escrever quail(is) os vestibulares ou concursos prestados, caso contrário, deverá
     * deixar o campo em branco.
     * 
     * @var string Resposta para a pergunta 'Já prestou algum vestibular ou concurso?'
     * @ORM\Column(name="pre_interview_entrance_exam", type="string", length=500, nullable=true)
     */
    private $entranceExam;

    /**
     * Já ingressou no ensino superior? Se sim, ainda cursa?
     * 
     * @var string Resposta para a pergunta 'Já ingressou no ensino superior? Se sim, ainda cursa?'
     * @ORM\Column(name="pre_interview_undergraduate_course", type="string", length=500, nullable=true)
     */
    private $undergraduateCourse;

    /**
     * O que espera de nós e o que pretende alcançar caso seja aprovado?
     * 
     * @var string Resposta para a pergunta 'O que espera de nós e o que pretende alcançar caso seja aprovado?'
     * @ORM\Column(name="pre_interview_waiting_forus", type="string", length=1000, nullable=true)
     */
    private $waitingForUs;


    /* ####################### OUTRAS INFORMAÇÕES ###################### */

    /**
     * Informe ou esclareça sobre dados não contemplados neste formulário ou situações especiais que julgar conveniente
     * 
     * @var string Resposta para pergunta outras informações
     * @ORM\Column(name="pre_interview_more_info", type="string", length=1000, nullable=true)
     */
    private $moreInformation;

    public function __construct()
    {
        // registro do sistema
        $this->preInterviewDate = new DateTime();

        // Vulnerabilidade
        $this->familyProperties = new ArrayCollection();
        $this->infrastructureElements = new ArrayCollection();
        $this->familyGoods = new ArrayCollection();
        $this->familyHealth = new ArrayCollection();
        $this->familyMembers = new ArrayCollection();

        // Socioeconômico
        $this->familyIncome = new ArrayCollection();
        $this->familyExpenses = new ArrayCollection();
    }
    /* ################## RELAÇÕES E DADOS AUTOMÁTICOS ################ */

    /**
     * 
     * @return int Identificador da pré-entrevista
     */
    public function getPreInterviewId()
    {
        return $this->preInterviewId;
    }

    /**
     * 
     * @return DateTime Data do preenchimento do formulário de pré-entrevista.
     */
    public function getPreInterviewDate()
    {
        return $this->preInterviewDate;
    }

    /**
     * 
     * @return string Tipo de escola(s) do ensino fundamental.
     */
    public function getPreInterviewElementarySchoolType()
    {
        return $this->preInterviewElementarySchoolType;
    }

    /**
     * 
     * @return string Tipo de escola(s) do ensino médio.
     */
    public function getPreInterviewHighSchoolType()
    {
        return $this->preInterviewHighSchoolType;
    }

    /**
     * Busca pela inscrição associada à pre-entrevista.
     * 
     * @return Registration Inscrição associada a pre-entrevista
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * Define o candidato da pré-entrevista.
     * 
     * @param Recruitment\Entity\Registration $registration Inscrição do candidato.
     * @return Recruitment\Entity\PreInterview Permite o uso de interface fluete.
     */
    public function setRegistration(Registration $registration)
    {
        $this->registration = $registration;
        return $this;
    }
    /* ####################### SOCIOECONÔMICO ####################### */

    /**
     * Tipos de lugares de moradia.
     * 
     * @return array Vetor de tipos de lugares de moradia.
     */
    public static function getLiveArray()
    {
        return [
            self::LIVE_ALONE => self::LIVE_ALONE,
            self::LIVE_WITH_FRIENDS => self::LIVE_WITH_FRIENDS,
            self::LIVE_WITH_LIFE_PARTNER => self::LIVE_WITH_LIFE_PARTNER,
            self::LIVE_WITH_PARENTS => self::LIVE_WITH_PARENTS,
            self::LIVE_WITH_RELATIVES => self::LIVE_WITH_RELATIVES,
            self::LIVE_OTHER => self::LIVE_OTHER,
        ];
    }

    /**
     * Responsáveis financeiros pelo grupo familiar.
     * 
     * @return array Vetor de tipos de responsáveis financeiros.
     */
    public static function getResponsibleFinancialArray()
    {
        return [
            self::FINANCIAL_ME => self::FINANCIAL_ME,
            self::FINANCIAL_LIFE_PARTNER => self::FINANCIAL_LIFE_PARTNER,
            self::FINANCIAL_PARENTS => self::FINANCIAL_PARENTS,
            self::FINANCIAL_PARENT => self::FINANCIAL_PARENT,
            self::FINANCIAL_ACQUAINTED => self::FINANCIAL_ACQUAINTED,
        ];
    }

    /**
     * Tipos de zona de moradia.
     * 
     * @return array Vetor de tipos de zona de moradia.
     */
    public static function getLiveAreaArray()
    {
        return [
            self::LIVE_AREA_URBAN_CENTRAL => self::LIVE_AREA_URBAN_CENTRAL,
            self::LIVE_AREA_URBAN_PERIPHERAL => self::LIVE_AREA_URBAN_PERIPHERAL,
            self::LIVE_AREA_COUNTRYSIDE => self::LIVE_AREA_COUNTRYSIDE,
        ];
    }

    public function getLive()
    {
        return $this->live;
    }

    public function setLive($live)
    {
        $this->live = $live;
        return $this;
    }

    public function getResponsibleFinancial()
    {
        return $this->responsibleFinancial;
    }

    public function setResponsibleFinancial($responsibleFinancial)
    {
        $this->responsibleFinancial = $responsibleFinancial;
        return $this;
    }

    public function getInfrastructureElements()
    {
        return $this->infrastructureElements;
    }

    public function addInfrastructureElements(
    Collection $infrastructureElements)
    {
        foreach ($infrastructureElements as $inEl) {
            if (!$this->hasInfrastructureElements($inEl)) {
                $this->infrastructureElements->add($inEl);
            }
        }

        return $this;
    }

    public function removeInfrastructureElements(
    Collection $infrastructureElements)
    {
        foreach ($infrastructureElements as $inEl) {
            $this
                ->infrastructureElements
                ->removeElement($inEl);
        }
        return $this;
    }

    public function hasInfrastructureElements(
    InfrastructureElement $infrastructureElement)
    {
        return $this->infrastructureElements->contains($infrastructureElement);
    }

    public function getLiveArea()
    {
        return $this->liveArea;
    }

    public function setLiveArea($liveArea)
    {
        $this->liveArea = $liveArea;
        return $this;
    }

    public function getItemTv()
    {
        return $this->itemTv;
    }

    public function setItemTv($itemTv)
    {
        $this->itemTv = $itemTv;
        return $this;
    }

    public function getItemBathroom()
    {
        return $this->itemBathroom;
    }

    public function setItemBathroom($itemBathroom)
    {
        $this->itemBathroom = $itemBathroom;
        return $this;
    }

    public function getItemSalariedHousekeeper()
    {
        return $this->itemSalariedHousekeeper;
    }

    public function setItemSalariedHousekeeper($itemSalariedHousekeeper)
    {
        $this->itemSalariedHousekeeper = $itemSalariedHousekeeper;
        return $this;
    }

    public function getItemDailyHousekeeper()
    {
        return $this->itemDailyHousekeeper;
    }

    public function setItemDailyHousekeeper($itemDailyHousekeeper)
    {
        $this->itemDailyHousekeeper = $itemDailyHousekeeper;
        return $this;
    }

    public function getItemWashingMachine()
    {
        return $this->itemWashingMachine;
    }

    public function setItemWashingMachine($itemWashingMachine)
    {
        $this->itemWashingMachine = $itemWashingMachine;
        return $this;
    }

    public function getItemRefrigerator()
    {
        return $this->itemRefrigerator;
    }

    public function setItemRefrigerator($itemRefrigerator)
    {
        $this->itemRefrigerator = $itemRefrigerator;
        return $this;
    }

    public function getItemCableTv()
    {
        return $this->itemCableTv;
    }

    public function setItemCableTv($itemCableTv)
    {
        $this->itemCableTv = $itemCableTv;
        return $this;
    }

    public function getItemComputer()
    {
        return $this->itemComputer;
    }

    public function setItemComputer($itemComputer)
    {
        $this->itemComputer = $itemComputer;
        return $this;
    }

    public function getItemSmartphone()
    {
        return $this->itemSmartphone;
    }

    public function setItemSmartphone($itemSmartphone)
    {
        $this->itemSmartphone = $itemSmartphone;
        return $this;
    }

    public function getItemBedroom()
    {
        return $this->itemBedroom;
    }

    public function setItemBedroom($itemBedroom)
    {
        $this->itemBedroom = $itemBedroom;
        return $this;
    }

    public function getFamilyExpenses()
    {
        return $this->familyExpenses;
    }

    public function addFamilyExpenses(Collection $familyExpenses)
    {
        foreach ($familyExpenses as $fe) {
            if (!$this->hasFamilyExpense($fe)) {
                $fe->setPreInterviewExpense($this);
                $this->familyExpenses->add($fe);
            }
        }
        return $this;
    }

    public function hasFamilyExpense(FamilyIncomeExpense $familyExpense)
    {
        return $this->familyExpenses->contains($familyExpense);
    }

    public function removeFamilyExpenses(Collection $familyExpenses)
    {
        foreach ($familyExpenses as $fe) {
            $fe->setPreInterviewExpense();
            $this->familyExpenses->removeElement($fe);
        }
        return $this;
    }

    public function getFamilyIncome()
    {
        return $this->familyIncome;
    }

    public function addFamilyIncome(Collection $familyIncome)
    {
        foreach ($familyIncome as $fi) {
            if (!$this->hasFamilyIncome($fi)) {
                $fi->setPreInterviewIncome($this);
                $this->familyExpenses->add($fi);
            }
        }
        return $this;
    }

    public function hasFamilyIncome(FamilyIncomeExpense $familyIncome)
    {
        return $this->familyExpenses->contains($familyIncome);
    }

    public function removeFamilyIncome(Collection $familyIncome)
    {
        foreach ($familyIncome as $fi) {
            $fi->setPreInterviewIncome();
            $this->familyIncome->removeElement($fi);
        }
        return $this;
    }
    /* ####################### PERFIL DE ESTUDANTE ################## */

    public function getExtraCourses()
    {
        return $this->extraCourses;
    }

    public function setExtraCourses($extraCourses)
    {
        $this->extraCourses = $extraCourses;
        return $this;
    }

    public function getPreparationCourse()
    {
        return $this->preparationCourse;
    }

    public function setPreparationCourse($preparationCourse)
    {
        $this->preparationCourse = $preparationCourse;
        return $this;
    }

    public function getEntranceExam()
    {
        return $this->entranceExam;
    }

    public function setEntranceExam($entranceExam)
    {
        $this->entranceExam = $entranceExam;
        return $this;
    }

    public function getUndergraduateCourse()
    {
        return $this->undergraduateCourse;
    }

    public function setUndergraduateCourse($undergraduateCourse)
    {
        $this->undergraduateCourse = $undergraduateCourse;
        return $this;
    }

    public function getWaitingForUs()
    {
        return $this->waitingForUs;
    }

    public function setWaitingForUs($waitingForUs)
    {
        $this->waitingForUs = $waitingForUs;
        return $this;
    }
    /* ####################### VULNERABILIDADE ###################### */

    
    /**
     * Retorna a declaração de etinia da família.
     * 
     * @return string
     */
    public function getFamilyEthnicity()
    {
        return $this->familyEthnicity;
    }

    /**
     * Define a etinia da família.
     * 
     * @param string $familyEthnicity
     * @return \Recruitment\Entity\PreInterview
     */
    public function setFamilyEthnicity($familyEthnicity)
    {
        $this->familyEthnicity = $familyEthnicity;
        return $this;
    }
    
    /**
     * Possíveis etinias.
     * 
     * @return array Possíveis etinias
     */
    public static function getFamilyEthnicityArray()
    {
        return [
            self::FAMILY_ETHNICITY_NATIVE => self::FAMILY_ETHNICITY_NATIVE,
            self::FAMILY_ETHNICITY_BLACK => self::FAMILY_ETHNICITY_BLACK,
            self::FAMILY_ETHNICITY_BROWN => self::FAMILY_ETHNICITY_BROWN,
            self::FAMILY_ETHNICITY_CAUCASIAN => self::FAMILY_ETHNICITY_CAUCASIAN,
        ];
    }
        
    /**
     * Tipos de escolas fundamentais.
     * 
     * @return array Vetor de tipos de escolhas fundamentais.
     */
    public static function getElementarySchoolTypeArray()
    {
        return [
            self::SCHOOL_TYPE_PUBLIC => self::SCHOOL_TYPE_PUBLIC,
            self::SCHOOL_TYPE_PRIVATE => self::SCHOOL_TYPE_PRIVATE,
            self::SCHOOL_TYPE_PRIVATE_STUDENTSHIP => self::SCHOOL_TYPE_PRIVATE_STUDENTSHIP,
            self::SCHOOL_TYPE_PRIVATE_BEFORE_PUBLIC => self::SCHOOL_TYPE_PRIVATE_BEFORE_PUBLIC,
            self::SCHOOL_TYPE_PUBLIC_BEFORE_PRIVATE => self::SCHOOL_TYPE_PUBLIC_BEFORE_PRIVATE,
            self::SCHOOL_TYPE_PUBLIC_PRIVATE_STUDENTSHIP => self::SCHOOL_TYPE_PUBLIC_PRIVATE_STUDENTSHIP,
        ];
    }

    /**
     * Tipos de escolas de ensino médio.
     * 
     * @return array Vetor de tipos de escolhas de ensino médio.
     */
    public static function getHighSchoolTypeArray()
    {
        return [
            self::SCHOOL_TYPE_PUBLIC => self::SCHOOL_TYPE_PUBLIC,
            self::SCHOOL_TYPE_PRIVATE => self::SCHOOL_TYPE_PRIVATE,
            self::SCHOOL_TYPE_PRIVATE_STUDENTSHIP => self::SCHOOL_TYPE_PRIVATE_STUDENTSHIP,
            self::SCHOOL_TYPE_PUBLIC_PRIVATE_STUDENTSHIP => self::SCHOOL_TYPE_PUBLIC_PRIVATE_STUDENTSHIP,
            self::SCHOOL_TYPE_PUBLIC_PRIVATE => self::SCHOOL_TYPE_PUBLIC_PRIVATE,
            self::SCHOOL_TYPE_TECH_PRIVATE => self::SCHOOL_TYPE_TECH_PRIVATE,
            self::SCHOOL_TYPE_TECH_PRIVATE_STUDENTSHIP => self::SCHOOL_TYPE_TECH_PRIVATE_STUDENTSHIP,
            self::SCHOOL_TYPE_TECH_PUBLIC => self::SCHOOL_TYPE_TECH_PUBLIC,
            self::SCHOOL_TYPE_OTHER => self::SCHOOL_TYPE_OTHER,
        ];
    }

    /**
     * Tipos de situação de residência.
     * 
     * @return array Vetor de tipos de situação de residência
     */
    public static function getHomeStatusArray()
    {
        return [
            self::HOME_RENTAL_PROPERTY => self::HOME_RENTAL_PROPERTY,
            self::HOME_OWN_ALREADY_PAID => self::HOME_OWN_ALREADY_PAID,
            self::HOME_OWN_BY_INHERITANCE => self::HOME_OWN_BY_INHERITANCE,
            self::HOME_OWN_PAYING => self::HOME_OWN_PAYING,
            self::HOME_GIVEN_PROPERTY => self::HOME_GIVEN_PROPERTY,
            self::HOME_OTHER => self::HOME_OTHER,
        ];
    }

    /**
     * Escolha de residência com ou sem acabamento.
     * 
     * @return array Vetor com escolhas de residência com ou sem acabamento.
     */
    public static function getHomeDescriptionArray()
    {
        return [
            self::HOME_DESCRIPTION_FINISHED => self::HOME_DESCRIPTION_FINISHED,
            self::HOME_DESCRIPTION_WITHOUT_FINISHING => self::HOME_DESCRIPTION_WITHOUT_FINISHING,
        ];
    }

    /**
     * Tipos de transporte.
     * 
     * @return array Vetor de tipos de transporte.
     */
    public static function getTransportArray()
    {
        return [
            self::TRANSPORT_ON_FOOT_BYCICLE => self::TRANSPORT_ON_FOOT_BYCICLE,
            self::TRANSPORT_HITCHHIKE => self::TRANSPORT_HITCHHIKE,
            self::TRANSPORT_OWN => self::TRANSPORT_OWN,
            self::TRANSPORT_PAID_COLLETIVE_TRANSPORTATION => self::TRANSPORT_PAID_COLLETIVE_TRANSPORTATION,
            self::TRANSPORT_RENTED => self::TRANSPORT_RENTED,
            self::TRANSPORT_SCHOLAR => self::TRANSPORT_SCHOLAR,
            self::TRANSPORT_OTHER => self::TRANSPORT_OTHER,
        ];
    }

    public function getFamilyMembers()
    {
        return $this->familyMembers;
    }

    public function addFamilyMembers(Collection $familyMembers)
    {
        foreach ($familyMembers as $fm) {
            if (!$this->hasFamilyMember($fm)) {
                $fm->setPreInterview($this);
                $this->familyMembers->add($fm);
            }
        }

        return $this;
    }

    public function hasFamilyMember(CandidateFamily $familyMember)
    {
        return $this->familyMembers->contains($familyMember);
    }

    public function removeFamilyMembers(Collection $familyMembers)
    {
        foreach ($familyMembers as $fm) {
            $fm->setPreInterview();
            $this->familyMembers->removeElement($fm);
        }

        return $this;
    }

    public function getFamilyHealth()
    {
        return $this->familyHealth;
    }

    public function addFamilyHealth(Collection $familyHealth)
    {
        foreach ($familyHealth as $fh) {
            if (!$this->hasFamilyHealth($fh)) {
                $fh->setPreInterview($this);
                $this->familyHealth->add($fh);
            }
        }

        return $this;
    }

    public function hasFamilyHealth(FamilyHealth $familyHealth)
    {
        return $this->familyHealth->contains($familyHealth);
    }

    public function removeFamilyHealth(Collection $familyHealth)
    {
        foreach ($familyHealth as $fh) {
            $fh->setPreInterview();
            $this->familyHealth->removeElement($fh);
        }

        return $this;
    }

    public function getFamilyGoods()
    {
        return $this->familyGoods;
    }

    public function addFamilyGoods(Collection $familyGoods)
    {
        foreach ($familyGoods as $fg) {
            if (!$this->hasFamilyGood($fg)) {
                $fg->setPreInterview($this);
                $this->familyGoods->add($fg);
            }
        }

        return $this;
    }

    public function hasFamilyGood(FamilyGood $fg)
    {
        return $this->familyGoods->contains($fg);
    }

    public function removeFamilyGoods(Collection $familyGoods)
    {
        foreach ($familyGoods as $fg) {
            $fg->setPreInterview();
            $this->familyGoods->removeElement($fg);
        }

        return $this;
    }

    public function getFamilyProperties()
    {
        return $this->familyProperties;
    }

    public function addFamilyProperties(Collection $familyProperties)
    {
        foreach ($familyProperties as $fp) {
            if (!$this->hasFamilyProperty($fp)) {
                $fp->setPreInterview($this);
                $this->familyProperties->add($fp);
            }
        }

        return $this;
    }

    public function hasFamilyProperty(FamilyProperty $familyProperty)
    {
        return $this->familyProperties->contains($familyProperty);
    }

    public function removeFamilyProperties(Collection $familyProperties)
    {
        foreach ($familyProperties as $fp) {
            $fp->setPreInterview();
            $this->familyProperties->removeElement($fp);
        }

        return $this;
    }

    public function getElementarySchoolType()
    {
        return $this->elementarySchoolType;
    }

    public function setElementarySchoolType($elementarySchoolType)
    {
        $this->elementarySchoolType = $elementarySchoolType;
        return $this;
    }

    public function getHighSchoolType()
    {
        return $this->highSchoolType;
    }

    public function setHighSchoolType($highSchoolType)
    {
        $this->highSchoolType = $highSchoolType;
        return $this;
    }

    public function getHighSchoolAdmissionYear()
    {
        return $this->highSchoolAdmissionYear;
    }

    public function setHighSchoolAdmissionYear($highSchoolAdmissionYear)
    {
        $this->highSchoolAdmissionYear = $highSchoolAdmissionYear;
        return $this;
    }

    public function getHighSchoolConclusionYear()
    {
        return $this->highSchoolConclusionYear;
    }

    public function setHighSchoolConclusionYear($highSchoolConclusionYear)
    {
        $this->highSchoolConclusionYear = $highSchoolConclusionYear;
        return $this;
    }

    public function getSiblingsUndergraduate()
    {
        return $this->siblingsUndergraduate;
    }

    public function setSiblingsUndergraduate($siblingsUndergraduate)
    {
        $this->siblingsUndergraduate = $siblingsUndergraduate;
        return $this;
    }

    public function getOtherLanguages()
    {
        return $this->otherLanguages;
    }

    public function setOtherLanguages($otherLanguages)
    {
        $this->otherLanguages = $otherLanguages;
        return $this;
    }

    public function getHomeStatus()
    {
        return $this->homeStatus;
    }

    public function setHomeStatus($homeStatus)
    {
        $this->homeStatus = $homeStatus;
        return $this;
    }

    public function getHomeDescription()
    {
        return $this->homeDescription;
    }

    public function setHomeDescription($homeDescription)
    {
        $this->homeDescription = $homeDescription;
        return $this;
    }

    public function getTransport()
    {
        return $this->transport;
    }

    public function setTransport($transport)
    {
        $this->transport = $transport;
        return $this;
    }
    /* ####################### OUTRAS INFORMAÇÕES ###################### */

    public function getMoreInformation()
    {
        return $this->moreInformation;
    }

    public function setMoreInformation($moreInformation)
    {
        $this->moreInformation = $moreInformation;
        return $this;
    }
}
