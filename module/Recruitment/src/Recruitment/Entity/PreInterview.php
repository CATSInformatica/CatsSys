<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
 * @Todo
 *  - Tabela de familiares
 *  - Tabela de Doenças na familia
 *  - Tabela de receitas
 *  - Tabela de dispesas
 *  - Campo para outras informações relevantes (texto)
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="pre_interview")
 * @ORM\Entity
 */
class PreInterview
{

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
     */
    private $smartphone;

    /**
     *
     * @var string Quantidade de quartos.
     * @ORM\Column(name="pre_inteview_item_bedroom", type="string", length=30, nullable=false)
     */
    private $bedroom;

    /* ####################### VULNERABILIDADE ###################### */

    /**
     * var Collection
     * @ORM\OneToMany(targetEntity="FamilyProperty", mappedBy="preInterview")
     */
    private $familyProperties;

    /**
     * var Collection
     * @ORM\OneToMany(targetEntity="FamilyGood", mappedBy="preInterview")
     */
    private $familyGoods;

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
     * @var string Resposta para a pergunta 'Tem irmãos que cursaram/cursam o ensino superior?'
     * @ORM\Column(name="pre_interview_siblings_undergraduate", type="string", length=500, nullable=true)
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

    public function __construct()
    {
        $this->preInterviewDate = new DateTime('now');
        $this->infrastructureElements = new ArrayCollection();
        $this->familyProperties = new ArrayCollection();
        $this->familyGoods = new ArrayCollection();
    }

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
            self::LIVE_ALONE,
            self::LIVE_WITH_FRIENDS,
            self::LIVE_WITH_LIFE_PARTNER,
            self::LIVE_WITH_PARENTS,
            self::LIVE_WITH_RELATIVES,
            self::LIVE_OTHER,
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
            self::FINANCIAL_ME,
            self::FINANCIAL_LIFE_PARTNER,
            self::FINANCIAL_PARENTS,
            self::FINANCIAL_PARENT,
            self::FINANCIAL_ACQUAINTED,
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
            self::LIVE_AREA_URBAN_CENTRAL,
            self::LIVE_AREA_URBAN_PERIPHERAL,
            self::LIVE_AREA_COUNTRYSIDE,
        ];
    }

    /* ####################### PERFIL DE ESTUDANTE ################## */



    /* ####################### VULNERABILIDADE ###################### */

    /**
     * Tipos de escolas fundamentais.
     * 
     * @return array Vetor de tipos de escolhas fundamentais.
     */
    public static function getElementarySchoolTypeArray()
    {
        return [
            self::SCHOOL_TYPE_PUBLIC,
            self::SCHOOL_TYPE_PRIVATE,
            self::SCHOOL_TYPE_PRIVATE_STUDENTSHIP,
            self::SCHOOL_TYPE_PRIVATE_BEFORE_PUBLIC,
            self::SCHOOL_TYPE_PUBLIC_BEFORE_PRIVATE,
            self::SCHOOL_TYPE_PUBLIC_PRIVATE_STUDENTSHIP,
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
            self::SCHOOL_TYPE_PUBLIC,
            self::SCHOOL_TYPE_PRIVATE,
            self::SCHOOL_TYPE_PRIVATE_STUDENTSHIP,
            self::SCHOOL_TYPE_PUBLIC_PRIVATE_STUDENTSHIP,
            self::SCHOOL_TYPE_PUBLIC_PRIVATE,
            self::SCHOOL_TYPE_TECH_PRIVATE,
            self::SCHOOL_TYPE_TECH_PRIVATE_STUDENTSHIP,
            self::SCHOOL_TYPE_TECH_PUBLIC,
            self::SCHOOL_TYPE_OTHER,
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
            self::HOME_RENTAL_PROPERTY,
            self::HOME_OWN_ALREADY_PAID,
            self::HOME_OWN_BY_INHERITANCE,
            self::HOME_OWN_PAYING,
            self::HOME_GIVEN_PROPERTY,
            self::HOME_OTHER
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
            self::HOME_DESCRIPTION_FINISHED,
            self::HOME_DESCRIPTION_WITHOUT_FINISHING,
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
            self::TRANSPORT_ON_FOOT_BYCICLE,
            self::TRANSPORT_HITCHHIKE,
            self::TRANSPORT_OWN,
            self::TRANSPORT_PAID_COLLETIVE_TRANSPORTATION,
            self::TRANSPORT_RENTED,
            self::TRANSPORT_SCHOLAR,
            self::TRANSPORT_OTHER,
        ];
    }

}
