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

use Doctrine\ORM\Mapping as ORM;
use Recruitment\Entity\Registration;

/**
 * Contém informações da entrevista de candidatos ao processo seletivo de alunos
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="student_interview")
 * @ORM\Entity
 */
class StudentInterview
{

    const INTERVIEWER_SEPARATOR = ';';

    /**
     * Identificador da entrevista.
     *
     * @var int
     * @ORM\Column(name="student_interview_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY");
     */
    private $studentInterviewId;

    /**
     * Associação 1:1 com o objeto de registro de inscrição.
     *
     * @var Registration
     * @ORM\OneToOne(targetEntity="Registration", mappedBy="studentInterview")
     */
    private $registration;

    /* ########################### PREPARAÇÃO ################################# */

    /**
     * Horário de início da entrevista.
     * @var \DateTime
     * @ORM\Column(name="student_interview_starttime", type="time", nullable=false)
     */
    private $interviewStartTime;

    /**
     * Horário de término da entrevista.
     * @var \DateTime
     * @ORM\Column(name="student_interview_endtime", type="time", nullable=false)
     */
    private $interviewEndTime;

    /**
     * Nomes dos Entrevistadores separados por self::INTERVIWER_SEPARATOR.
     *
     * @var string
     * @ORM\Column(name="student_interview_interviewers", type="string",
     * length=300, nullable=false)
     */
    private $interviewers;


    /* ################# CONTATO DIRETO COM O CANDIDATO ##################### */

    /**
     * Comentário dos entrevistadores sobre a postura do candidato.
     *
     * @var string
     * @ORM\Column(name="student_interview_comintro", type="string", length=2000, nullable=true)
     */
    private $interviewerCommentIntro;

    /**
     * Situação da casa e localização.
     * 
     * @var string
     * @ORM\Column(name="student_interview_homesitcomm", type="string", length=500, nullable=true);
     */
    private $interviewHomeSitComm;

    /**
     * Bens e despesas básicas.
     * 
     * @var string
     * @ORM\Column(name="student_interview_expcomm", type="string", length=500, nullable=true);
     */
    private $interviewExpComm;

    /**
     * Membros da família e renda.
     * 
     * @var string
     * @ORM\Column(name="student_interview_faminccomm", type="string", length=500, nullable=true);
     */
    private $interviewFamIncComm;

    /**
     * Problemas com os membros (Procure por vícios, drogas. Doenças graves ou crônicas.)
     * 
     * @var string
     * @ORM\Column(name="student_interview_famprobcomm", type="string", length=500, nullable=true);
     */
    private $interviewFamProbComm;

    /**
     * Membros da família e sua relação e pensamento sobre os estudos/trabalho
     * 
     * @var string
     * @ORM\Column(name="student_interview_famsuppcomm", type="string", length=500, nullable=true);
     */
    private $interviewFamSuppComm;

    /**
     * Trabalhos do candidato e rotina atual (atividades e hábitos).
     * 
     * @var string
     * @ORM\Column(name="student_interview_routcomm", type="string", length=500, nullable=true);
     */
    private $interviewRoutComm;

    /**
     * Histórico escolar e comportamento como aluno.
     * 
     * @var string
     * @ORM\Column(name="student_interview_studbehacomm", type="string", length=500, nullable=true);
     */
    private $interviewStudBehaComm;

    /**
     * Cursos técnicos, profissionalizantes, de idioma, etc.
     * 
     * @var string
     * @ORM\Column(name="student_interview_courscomm", type="string", length=500, nullable=true);
     */
    private $interviewCoursComm;

    /**
     * Rotina de estudos e melhores formas de estudar (horas por semana, agenda, estudar por tarefas).
     * 
     * @var string
     * @ORM\Column(name="student_interview_studwaycomm", type="string", length=500, nullable=true);
     */
    private $interviewStudWayComm;

    /**
     * Curso pré-vestibular. Verifique se o candidato já fez simulados, vestibulares e concursos. Converse sobre suas experiências.
     * 
     * @var string
     * @ORM\Column(name="student_interview_studexpcomm", type="string", length=500, nullable=true);
     */
    private $interviewStudExpComm;

    /**
     * Comentário dos entrevistadores sobre o perfil de estudante do candidato.
     * @var string
     * @ORM\Column(name="student_interview_comst", type="string", length=2000, nullable=true)
     */
    private $interviewerCommentStudent;

    /**
     * Comentários dos entrevistadores sobre o nossas atividades.
     * @var string
     * @ORM\Column(name="student_interview_ouract", type="string", length=2000, nullable=true)
     */
    private $interviewerOurActivities;

    /* ##################### AVALIAÇÃO SOCIOECONÔMICA ####################### */

    const TOTAL_INCOME_HALF_SALARY = 'Até meio salário mínimo';
    const TOTAL_INCOME_HALFTOONE_SALARY = 'Entre meio e um salário mínimo';
    const TOTAL_INCOME_ONETOONEANDHALF_SALARY = 'Entre um e um e meio salário mínimo';
    const TOTAL_INCOME_ONEANDHALFTOTWO_SALARY = 'Entre um e meio e dois salários mínimos';
    const TOTAL_INCOME_TWOTOFOUR_SALARY = 'Entre dois e quatro salários mínimos';
    const TOTAL_INCOME_FOURTOEIGHT_SALARY = 'Entre quatro e oito salários mínimos';
    const TOTAL_INCOME_EIGHTTOSIXTEEN_SALARY = 'Entre oito e dezesseis salários mínimos';
    const TOTAL_INCOME_MORETHANSIXTEEN_SALARY = 'Mais de dezesseis salários mínimos';

    /**
     * Total de rendimentos da família.
     * @var string
     * @ORM\Column(name="student_interview_tincome", type="string", length=100, nullable=true)
     */
    private $interviewTotalIncome;

    const FAMILY_MEMBERS_ONEORTWO = 'um ou dois';
    const FAMILY_MEMBERS_THREEORFOUR = 'três ou quatro';
    const FAMILY_MEMBERS_FIVEORSIX = 'cinco ou seis';
    const FAMILY_MEMBERS_SEVENOREIGHT = 'sete ou oito';
    const FAMILY_MEMBERS_NINEORMORE = 'nove ou mais';

    /**
     * Quantos membros residentes na família.
     *
     * @var string
     * @ORM\Column(name="student_interview_nfamily", type="string", length=20, nullable=false)
     */
    private $interviewNumberOfFamilyMembers;

    const MAXSCHOLARITY_GRADUATIONORMORE = 'Ensino superior ou mais';
    const MAXSCHOLARITY_HIGHSCHOOL = 'Ensino superior incompleto ou médio completo';
    const MAXSCHOLARITY_ELEMENTARYSCHOOLII = 'Ensino médio incompleto ou fundamental II completo. (até 9º ano)';
    const MAXSCHOLARITY_ELEMENTARYSCHOOLI = 'Fundamental II incompleto';
    const MAXSCHOLARITY_ELEMENTARYSCHOOLI_COMPLETE = 'Fundamental I completo (até o 5º ano)';
    const MAXSCHOLARITY_ELEMENTARYSCHOOLI_INCOMPLETE = 'Fundamental I incompleto';
    const MAXSCHOLARITY_LITERATE = 'Alfabetizado';
    const MAXSCHOLARITY_ILITERATE = 'Analfabeto';

    /**
     * Qual é a maior escolaridade registrada entre os membros da familia.
     *
     * @var string
     * @ORM\Column(name="student_interview_mscholarity", type="string", length=100, nullable=false)
     */
    private $interviewMaxScholarity;

    const HOME_TYPE_OWN = 'Própria';
    const HOME_TYPE_FUNDED = 'Financiada';
    const HOME_TYPE_RENTED = 'Alugada';
    const HOME_TYPE_GIVEIN = 'Cedida';

    /**
     * Qual é a situação da casa em que vive o candidato.
     *
     * @var string
     * @ORM\Column(name="student_interview_hometype", type="string", length=50, nullable=false)
     */
    private $interviewHomeType;

    const HOME_SITUATION_UNSATISFACTORY = 'Insatisfatória';
    const HOME_SITUATION_GOOD = 'Boa';
    const HOME_SITUATION_REGULAR = 'Regular';
    const HOME_SITUATION_GREAT = 'Ótima';

    /**
     * Avaliando o tipo, modalidade, acomodações, localização e infra-estrutura. Qual item
     * descreve melhor a casa do candidato.
     *
     * @var string
     * @ORM\Column(name="student_interview_hsituation", type="string", length=50, nullable=false)
     */
    private $interviewHomeSituation;

    const MAX_POSITION_BUSINESSMAN = 'EMPRESÁRIO';
    const MAX_POSITION_BUSINESSMAN_DESC = ' Proprietários na agricultura, agroindústria, indústria, comércio, sistema financeiro, serviços, etc.';
    const MAX_POSITION_HIGH_ADMINISTRADOR = 'ALTA ADMINISTRAÇÃO';
    const MAX_POSITION_HIGH_ADMINISTRADOR_DESC = 'Juízes, Promotores, Diretores, Administradores, Gerentes, Supervisores, Assessores, Consultores, etc';
    const MAX_POSITION_LIBERAL_AUTONOMOUS = 'LIBERAL AUTÔNOMO';
    const MAX_POSITION_LIBERAL_AUTONOMOUS_DESC = 'Médico, Advogado, Contador, Arquiteto, Engenheiro, Dentista, Representante comercial, Oculista, Auditor, etc.';
    const MAX_POSITION_ADMINISTRATOR = 'ASSALARIADO ADMINISTRATIVO';
    const MAX_POSITION_ADMINISTRATOR_DESC = 'Chefias em geral, Assistentes, Ocupações de nível médio e superior, Analistas, Atletas profissionais, Técnicosem geral, Servidores públicos de nível superior, etc.';
    const MAX_POSITION_PRODUCTION = 'ASSALARIADO DE PRODUÇÃO';
    const MAX_POSITION_PRODUCTION_DESC = 'Trabalhadores assalariados da produção, bens e serviços e da administração (indústria, comércio, serviços, setor público e sistema financeiro), ajudantes e auxiliares, etc.';
    const MAX_POSITION_AUTONOMOUS = 'AUTÔNOMO';
    const MAX_POSITION_AUTONOMOUS_DESC = ' Pedreiros, Caminhoneiros, Marceneiros, Feirantes, Cabelereiros, Taxistas, Vendedores etc.';
    const MAX_POSITION_SMALL_PRODUCERS = 'PEQUENO PRODUTOR RURAL';
    const MAX_POSITION_SMALL_PRODUCERS_DESC = 'Pequenos produtores rurais: Meeiro, Parceiro, Chacareiro, etc.';
    const MAX_POSITION_DOMESTICS = 'EMPREGADO DOMÉSTICO';
    const MAX_POSITION_DOMESTICS_DESC = 'Empregados domésticos: Jardineiros, Diaristas, Mensalista, Faxineiro, Cozinheiro, Mordomo, Babá, Motorista Particular, Atendentes, etc.';
    const MAX_POSITION_RURAL_WORKER = 'TRABALHADOR RURAL';
    const MAX_POSITION_RURAL_WORKER_DESC = 'Trabalhadores rurais assalariados, volantes e assemelhados: Ambulantes, Chapa, Bóia- Fria, Ajudantes Gerais, etc.';
    const MAX_POSITION_STUDENTOROTHER = 'ESTUDANTE';
    const MAX_POSITION_STUDENTOROTHER_DESC = 'Estudante/Sem ocupações anteriores.';

    /**
     * Maior nível ocupacional.
     *
     * @var string
     * @ORM\Column(name="student_interview_maxposition", type="string", length=50, nullable=false)
     */
    private $interviewMaxPosition;
    
    /**
     * Nota obtida no critério socioeconômico.
     * 
     * @var float
     * @ORM\Column(name="student_interview_segrade", type="float", nullable=false);
     */
    private $interviewSocioeconomicGrade;
    
    /**
     * Justificativa para as escolhas no critério socioeconômico.
     *
     * @var string
     * @ORM\Column(name="student_interview_segjust", type="string", length=2000, nullable=true)
     */
    private $interviewerSocioecGradeJustification;

    /* ####################### Avaliação da vulnerabilidade ################### */

    const FAM_PROVIDER_YES = 'Provedor da família';
    const FAM_PROVIDER_NO = 'Não é provedor da família';

    /**
     * Provedor de família
     * @var string
     * @ORM\Column(name="student_interview_famprovider", type="string", length=50, nullable=false)
     */
    private $interviewFamilyProvider;

    const HASCHILDREN_YES = 'Tem filhos';
    const HASCHILDREN_NO = 'Não tem filhos';

    /**
     * Ter filhos.
     * @var string
     * @ORM\Column(name="student_interview_haschildren", type="string", length=50, nullable=false)
     */
    private $interviewHasChildren;

    const HASDESEASE_YES = 'Doenças incapacitantes na família';
    const HASDESEASE_NO = 'Sem doências na família';

    /**
     * Doenças na família.
     * @var string
     * @ORM\Column(name="student_interview_hasdesease", type="string", length=50, nullable=false)
     */
    private $interviewHasDisease;

    const HIGHSCHOOL_PUBLIC_YES = 'Ensino médio em escola pública';
    const HIGHSCHOOL_PUBLIC_NO = 'Sem ensino médio em escola pública';

    /**
     * Ensino médio em escola pública.
     * @var string
     * @ORM\Column(name="student_interview_highschool", type="string", length=50, nullable=false)
     */
    private $interviewHighSchool;

    const FAMILYSUPPORT_YES = 'Possui apoio da família nos estudos';
    const FAMILYSUPPORT_NO = 'Falta de apoio da família nos estudos';

    /**
     * Falta de apoio da família nos estudos.
     * @var string
     * @ORM\Column(name="student_interview_famsupport", type="string", length=50, nullable=false)
     */
    private $interviewFamSupport;

    const FAMDEPENDENCY_YES = 'Família depende de terceiros';
    const FAMDEPENDENCY_NO = 'Família não depende de terceiros';

    /**
     * Família depende de terceiros.
     * @var string
     * @ORM\Column(name="student_interview_famdependency", type="string", length=50, nullable=false)
     */
    private $interviewFamDependency;

    const FAMNEEDTOWORK_YES = 'Precisa trabalhar';
    const FAMNEEDTOWORK_NO = 'Não precisa trabalhar';

    /**
     * Precisa trabalhar.
     * @var string
     * @ORM\Column(name="student_interview_needtowork", type="string", length=50, nullable=false)
     */
    private $intervewNeedToWork;

    const SINGLETON_YES = 'Filho único';
    const SINGLETON_NO = 'Possui irmão(s)';

    /**
     *
     * @var string
     * @ORM\Column(name="student_interview_singleton", type="string", length=50, nullable=false)
     */
    private $interviewSingleton;

    const FAMILYPROPANDGOODS_JUSTNEEDED_YES = 'Somente imóveis/móveis necessários.';
    const FAMILYPROPANDGOODS_JUSTNEEDED_NO = 'Imóveis/móveis além do necessário.';

    /**
     * Somente imóveis/móveis necessários.
     * @var string
     * @ORM\Column(name="student_interview_fampropandgoods", type="string", length=50, nullable=false)
     */
    private $intervewFamilyPropAndGoods;

    const VULNERABILITY_HIGH = 'ALTA VULNERABILIDADE';
    const VULNERABILITY_HIGH_DESC = 'o candidato apresenta grande dificuldade em satisfazer suas necessidades básicas, o que pode resultar em abandono dos estudos por insuficiência de recurso social ou moral. O candidato classificado nesse índice, caso aprovado, com certeza precisará de apoio para se manter firme nos estudos, necessitando de acompanhamento para não desistir, devido a vários problemas sérios que acompanham seu contexto social. Poderá receber a bolsa da mensalidade, além de outros auxílios psicológicos.';
    const VULNERABILITY_MIDDLE = 'MÉDIA VULNERABILIDADE';
    const VULNERABILITY_MIDDLE_DESC = 'o candidato apresenta nível de dificuldade intermediário em satisfazer suas necessidades básicas. O candidato classificado provavelmente solicitará apoio e acompanhamento para não desistir, sendo que apresenta algum problema sério, ou vários pequenos que acompanham seu contexto social.';
    const VULNERABILITY_LOW = 'BAIXA VULNERABILIDADE';
    const VULNERABILITY_LOW_DESC = 'o candidato apresenta nível de dificuldade pequeno para satisfazer suas necessidades básicas. Ele aproveitará o apoio, contudo não necessita de acompanhamento especial durante o ano. Possui algum problema pequeno que acompanha seu contexto social.';
    const VULNERABILITY_TEMPORARY = 'VULNERABILIDADE TEMPORÁRIA';
    const VULNERABILITY_TEMPORARY_DESC = 'o candidato apresenta uma necessidade de apoio momentâneo para permanecer nos estudos. Sua situação atualmente é de relativa vulnerabilidade, em comparação a outros momentos. Nesse caso o mesmo poderá ser aprovado, de acordo com a disponibilidade de vagas e parecer quanto a sua responsabilidade e interesse.';
    const VULNERABILITY_NONE = 'NENHUMA VULNERABILIDADE';
    const VULNERABILITY_NONE_DESC = 'o candidato e sua família possuem condições estáveis e suficientes para sua manutenção. Não apresenta nenhum tipo de problema ligado a seu contexto social';

    /**
     * Em que perfil de vulnerabilidade o candidato se encaixa.
     * @var string
     * @ORM\Column(name="student_interview_svulnerability", type="string", length=50, nullable=false)
     */
    private $interviewStudentVulnerability;

    /**
     * Nota no critério vulnerabilidade.
     *
     * @var float
     * @ORM\Column(name="student_interview_vgrade", type="float", nullable=false)
     */
    private $interviewVulnerabilityGrade;

    const INTERVIEW_STUDENT_COMMON = 'PERFIL COMUM';
    const INTERVIEW_STUDENT_COMMON_DESC = 'O candidato enxerga a importância do ensino superior, contudo não possui muito foco quanto ao que quer pra si (aqui é importante diferenciar quando o candidato não possui objetivo por falta de informação condicionada, como moradores de zona rural ou pessoas muito carentes, a quando o candidato não possui objetivo por não ter buscado mais informações) . Não possui noções de estudo, como rotina ou material. Não possui nenhum tipo de experiência em simulados, vestibulares e concursos, além de geralmente não ter realizado nenhum tipo de curso profissionalizante ainda.';
    const INTERVIEW_STUDENT_MODERATE = 'PRÉ-VESTIBULANDO';
    const INTERVIEW_STUDENT_MODERATE_DESC = 'O candidato demonstra forte interesse em relação ao ensino superior, já tem ideias quanto a áreas de atuação (humanas, exatas, biológicas), contudo não possui noções de estudo ainda, como rotina e material. Normalmente não possui experiência em simulados, vestibulares e concursos, e a vontade de estudar já começa a estar vinculada somente à pessoa.';
    const INTERVIEW_STUDENT_ADVANCED = 'VESTIBULANDO';
    const INTERVIEW_STUDENT_ADVANCED_DESC = 'O candidato é objetivo em relação aos estudos, possui foco, já tendo rotina de estudos e ideias quanto a profissões (engenharia, direito, licenciatura). Tem noção das dificuldades do período, possuindo ou não experiência em simulados, vestibulares e concursos. A vontade de estudar é completamente desvinculada à opinião da família ou próximos.';
    const INTERVIEW_STUDENT_PROBLEMATIC = 'PROBLEMÁTICO';
    const INTERVIEW_STUDENT_PROBLEMATIC_DESC = 'O candidato foi aluno em anos anteriores, não chegou a ser expulso, mas teve graves problemas com indisciplina ou com frequência. É o caso de ex-alunos que aproveitaram mal a última oportunidade que tiveram, contudo ainda podem ter chances de entrar.';
    const INTERVIEW_STUDENT_NOTMET = 'SEM PERFIL';
    const INTERVIEW_STUDENT_NOTMET_DESC = ' O candidato enxerga o ensino superior como uma oportunidade, contudo ainda não visualiza sua importância. Normalmente está sem outras atividades e para não ficar com tempo livre decidiu estudar.';

    /**
     * Justificativa para nota do entrevistador no critério vulnerabilidade
     *
     * @var string
     * @ORM\Column(name="student_interview_vugjust", type="string", length=2000, nullable=false)
     */
    private $interviewerVulnerabilityGradeJustification;
    
    /**
     * Perfil do estudante.
     *
     * @var string
     * @ORM\Column(name="student_interview_squestion", type="string", length=30, nullable=false)
     */
    private $interviewStudentQuestion;

    /**
     * Nota no critério perfil de estudante.
     *
     * @var float
     * @ORM\Column(name="student_interview_sgrade", type="float", nullable=false)
     */
    private $interviewStudentGrade;

    /**
     * Justificativa para nota do entrevistador no critério perfil de estudante
     *
     * @var string
     * @ORM\Column(name="student_interview_stgjust", type="string", length=2000, nullable=false)
     */
    private $interviewerStudentGradeJustification;

    public function __construct()
    {
        $this->interviewSocioeconomicGrade = 0;
        $this->interviewVulnerabilityGrade = 0;
        $this->interviewStudentGrade = 0;
    }

    /**
     *
     * @return int
     */
    public function getStudentInterviewId()
    {
        return $this->studentInterviewId;
    }

    /**
     *
     * @return Registration
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * Define a qual inscrição a entrevista é referente.
     *
     * @param Registration $registration Inscrição
     * @return \Recruitment\Entity\StudentInterview Permite o uso de interface flutente
     */
    public function setRegistration(Registration $registration)
    {
        $this->registration = $registration;
        return $this;
    }
    /* ########################### PREPARAÇÃO ################################# */

    /**
     * Returna os entrevistadores separados por self::INTERVIEWER_SEPARATOR
     *
     * @return string Nomes dos entrevistadores
     */
    public function getInterviewers()
    {
        return $this->interviewers;
    }

    /**
     * Organiza os entrevistadores em um vetor.
     *
     * @return array Vetor de entrevistadores
     */
    public function getInterviewersArray()
    {

        $interviewers = explode(self::INTERVIEWER_SEPARATOR, $this->interviewers);
        foreach ($interviewers as &$int) {
            $int = trim($int);
        }
        return $interviewers;
    }

    /**
     * Define quem são os entrevistadores.
     *
     * @param string $interviewers Entrevistadores separados por self::INTERVIEWER_SEPARATOR
     * @return \Recruitment\Entity\StudentInterview Permite o uso de interface fluente
     */
    public function setInterviewers($interviewers)
    {
        $this->interviewers = $interviewers;
        return $this;
    }

    /**
     * Busca pelo horário de início da entrevista.
     * @param string|null $format Formato válido para o \DateTime
     * @return string|null Horário de início da entrevista.
     */
    public function getInterviewStartTime($format = 'H:i')
    {
        if ($this->interviewStartTime !== null) {
            return $this->interviewStartTime->format($format);
        }
    }

    /**
     * Define o horário de início da entrevista.
     *
     * @param \DateTime $time Horário de início da entrevista.
     * @return \Recruitment\Entity\StudentInterview Permite o uso de interface fluente
     */
    public function setInterviewStartTime(\DateTime $time)
    {
        $this->interviewStartTime = $time;
        return $this;
    }

    /**
     * Busca pelo horário de término da entrevista.
     * @param string|null $format Formato válido para o \DateTime
     * @return string|null Horário de término da entrevista.
     */
    public function getInterviewEndTime($format = 'H:i')
    {
        if ($this->interviewEndTime !== null) {
            return $this->interviewEndTime->format($format);
        }
    }

    /**
     * Define o horário de término da entrevista.
     *
     * @param \DateTime $time Horário de término da entrevista.
     * @return \Recruitment\Entity\StudentInterview Permite o uso de interface fluente
     */
    public function setInterviewEndTime(\DateTime $time)
    {
        $this->interviewEndTime = $time;
        return $this;
    }
    /* ################# CONTATO DIRETO COM O CANDIDATO ##################### */

    /**
     *
     * @return string
     */
    public function getInterviewerCommentIntro()
    {
        return $this->interviewerCommentIntro;
    }

    /**
     *
     * @param string $interviewerCommentIntro
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewerCommentIntro($interviewerCommentIntro)
    {
        $this->interviewerCommentIntro = $interviewerCommentIntro;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getInterviewerCommentStudent()
    {
        return $this->interviewerCommentStudent;
    }

    /**
     * 
     * @return string
     */
    public function getInterviewHomeSitComm()
    {
        return $this->interviewHomeSitComm;
    }

    /**
     * 
     * @param string $interviewHomeSitComm
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewHomeSitComm($interviewHomeSitComm)
    {
        $this->interviewHomeSitComm = $interviewHomeSitComm;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getInterviewExpComm()
    {
        return $this->interviewExpComm;
    }

    /**
     * 
     * @param string $interviewExpComm
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewExpComm($interviewExpComm)
    {
        $this->interviewExpComm = $interviewExpComm;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getInterviewFamIncComm()
    {
        return $this->interviewFamIncComm;
    }

    /**
     * 
     * @param string $interviewFamIncComm
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewFamIncComm($interviewFamIncComm)
    {
        $this->interviewFamIncComm = $interviewFamIncComm;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getInterviewFamProbComm()
    {
        return $this->interviewFamProbComm;
    }

    /**
     * 
     * @param string $interviewFamProbComm
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewFamProbComm($interviewFamProbComm)
    {
        $this->interviewFamProbComm = $interviewFamProbComm;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getInterviewFamSuppComm()
    {
        return $this->interviewFamSuppComm;
    }

    /**
     * 
     * @param string $interviewFamSuppComm
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewFamSuppComm($interviewFamSuppComm)
    {
        $this->interviewFamSuppComm = $interviewFamSuppComm;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getInterviewRoutComm()
    {
        return $this->interviewRoutComm;
    }

    /**
     * 
     * @param string $interviewRoutComm
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewRoutComm($interviewRoutComm)
    {
        $this->interviewRoutComm = $interviewRoutComm;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getInterviewStudBehaComm()
    {
        return $this->interviewStudBehaComm;
    }

    /**
     * 
     * @param string $interviewStudBehaComm
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewStudBehaComm($interviewStudBehaComm)
    {
        $this->interviewStudBehaComm = $interviewStudBehaComm;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getInterviewCoursComm()
    {
        return $this->interviewCoursComm;
    }

    /**
     * 
     * @param string $interviewCoursComm
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewCoursComm($interviewCoursComm)
    {
        $this->interviewCoursComm = $interviewCoursComm;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getInterviewStudWayComm()
    {
        return $this->interviewStudWayComm;
    }

    /**
     * 
     * @param string $interviewStudWayComm
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewStudWayComm($interviewStudWayComm)
    {
        $this->interviewStudWayComm = $interviewStudWayComm;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getInterviewStudExpComm()
    {
        return $this->interviewStudExpComm;
    }

    /**
     * 
     * @param string $interviewStudExpComm
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewStudExpComm($interviewStudExpComm)
    {
        $this->interviewStudExpComm = $interviewStudExpComm;
        return $this;
    }

    /**
     *
     * @param string $interviewerCommentStudent
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewerCommentStudent($interviewerCommentStudent)
    {
        $this->interviewerCommentStudent = $interviewerCommentStudent;
        return $this;
    }

    /**
     * @return string Resposta do candidato em relação a participação em nossas atividades
     */
    public function getInterviewerOurActivities()
    {
        return $this->interviewerOurActivities;
    }

    /**
     * @param string $interviewerOurActivities Resposta do candidato em relação as atividades letivas
     * @return Self Permite o uso de interface fluente.
     */
    public function setInterviewerOurActivities($interviewerOurActivities)
    {
        $this->interviewerOurActivities = $interviewerOurActivities;
        return $this;
    }
    /* ##################### AVALIAÇÃO SOCIOECONÔMICA ####################### */

    /**
     *
     * @return string
     */
    public function getInterviewTotalIncome()
    {
        return $this->interviewTotalIncome;
    }

    /**
     *
     * @return array
     */
    public static function getInterviewTotalIncomeArray()
    {
        return [
            self::TOTAL_INCOME_HALF_SALARY => self::TOTAL_INCOME_HALF_SALARY,
            self::TOTAL_INCOME_HALFTOONE_SALARY => self::TOTAL_INCOME_HALFTOONE_SALARY,
            self::TOTAL_INCOME_ONETOONEANDHALF_SALARY => self::TOTAL_INCOME_ONETOONEANDHALF_SALARY,
            self::TOTAL_INCOME_ONEANDHALFTOTWO_SALARY => self::TOTAL_INCOME_ONEANDHALFTOTWO_SALARY,
            self::TOTAL_INCOME_TWOTOFOUR_SALARY => self::TOTAL_INCOME_TWOTOFOUR_SALARY,
            self::TOTAL_INCOME_FOURTOEIGHT_SALARY => self::TOTAL_INCOME_FOURTOEIGHT_SALARY,
            self::TOTAL_INCOME_EIGHTTOSIXTEEN_SALARY => self::TOTAL_INCOME_EIGHTTOSIXTEEN_SALARY,
            self::TOTAL_INCOME_MORETHANSIXTEEN_SALARY => self::TOTAL_INCOME_MORETHANSIXTEEN_SALARY,
        ];
    }

    /**
     *
     * @param string $interviewTotalIncome
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewTotalIncome($interviewTotalIncome)
    {
        $this->interviewTotalIncome = $interviewTotalIncome;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getInterviewNumberOfFamilyMembers()
    {
        return $this->interviewNumberOfFamilyMembers;
    }

    /**
     *
     * @return array
     */
    public static function getInterviewNumberOfFamilyMembersArray()
    {
        return [
            self::FAMILY_MEMBERS_ONEORTWO => self::FAMILY_MEMBERS_ONEORTWO,
            self::FAMILY_MEMBERS_THREEORFOUR => self::FAMILY_MEMBERS_THREEORFOUR,
            self::FAMILY_MEMBERS_FIVEORSIX => self::FAMILY_MEMBERS_FIVEORSIX,
            self::FAMILY_MEMBERS_SEVENOREIGHT => self::FAMILY_MEMBERS_SEVENOREIGHT,
            self::FAMILY_MEMBERS_NINEORMORE => self::FAMILY_MEMBERS_NINEORMORE,
        ];
    }

    /**
     *
     * @param string $interviewNumberOfFamilyMembers
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewNumberOfFamilyMembers($interviewNumberOfFamilyMembers)
    {
        $this->interviewNumberOfFamilyMembers = $interviewNumberOfFamilyMembers;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getInterviewMaxScholarity()
    {
        return $this->interviewMaxScholarity;
    }

    /**
     *
     * @return array
     */
    public static function getInterviewMaxScholarityArray()
    {
        return [
            self::MAXSCHOLARITY_GRADUATIONORMORE => self::MAXSCHOLARITY_GRADUATIONORMORE,
            self::MAXSCHOLARITY_HIGHSCHOOL => self::MAXSCHOLARITY_HIGHSCHOOL,
            self::MAXSCHOLARITY_ELEMENTARYSCHOOLII => self::MAXSCHOLARITY_ELEMENTARYSCHOOLII,
            self::MAXSCHOLARITY_ELEMENTARYSCHOOLI => self::MAXSCHOLARITY_ELEMENTARYSCHOOLI,
            self::MAXSCHOLARITY_ELEMENTARYSCHOOLI_COMPLETE => self::MAXSCHOLARITY_ELEMENTARYSCHOOLI_COMPLETE,
            self::MAXSCHOLARITY_ELEMENTARYSCHOOLI_INCOMPLETE => self::MAXSCHOLARITY_ELEMENTARYSCHOOLI_INCOMPLETE,
            self::MAXSCHOLARITY_LITERATE => self::MAXSCHOLARITY_LITERATE,
            self::MAXSCHOLARITY_ILITERATE => self::MAXSCHOLARITY_ILITERATE,
        ];
    }

    /**
     *
     * @param string $interviewMaxScholarity
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewMaxScholarity($interviewMaxScholarity)
    {
        $this->interviewMaxScholarity = $interviewMaxScholarity;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getInterviewHomeType()
    {
        return $this->interviewHomeType;
    }

    /**
     *
     * @return array
     */
    public static function getInterviewHomeTypeArray()
    {
        return [
            self::HOME_TYPE_OWN => self::HOME_TYPE_OWN,
            self::HOME_TYPE_FUNDED => self::HOME_TYPE_FUNDED,
            self::HOME_TYPE_RENTED => self::HOME_TYPE_RENTED,
            self::HOME_TYPE_GIVEIN => self::HOME_TYPE_GIVEIN
        ];
    }

    /**
     *
     * @param string $interviewHomeType
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewHomeType($interviewHomeType)
    {
        $this->interviewHomeType = $interviewHomeType;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getInterviewHomeSituation()
    {
        return $this->interviewHomeSituation;
    }

    /**
     *
     * @return array
     */
    public static function getInterviewHomeSituationArray()
    {
        return [
            self::HOME_SITUATION_UNSATISFACTORY => self::HOME_SITUATION_UNSATISFACTORY,
            self::HOME_SITUATION_GOOD => self::HOME_SITUATION_GOOD,
            self::HOME_SITUATION_REGULAR => self::HOME_SITUATION_REGULAR,
            self::HOME_SITUATION_GREAT => self::HOME_SITUATION_GREAT,
        ];
    }

    /**
     *
     * @param string $interviewHomeSituation
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewHomeSituation($interviewHomeSituation)
    {
        $this->interviewHomeSituation = $interviewHomeSituation;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getInterviewMaxPosition()
    {
        return $this->interviewMaxPosition;
    }

    /**
     *
     * @return array
     */
    public static function getInterviewMaxPositionArray()
    {
        return [
            self::MAX_POSITION_BUSINESSMAN => self::MAX_POSITION_BUSINESSMAN . ': ' .
            self::MAX_POSITION_BUSINESSMAN_DESC,
            self::MAX_POSITION_HIGH_ADMINISTRADOR => self::MAX_POSITION_HIGH_ADMINISTRADOR . ': ' .
            self::MAX_POSITION_HIGH_ADMINISTRADOR_DESC,
            self::MAX_POSITION_LIBERAL_AUTONOMOUS => self::MAX_POSITION_LIBERAL_AUTONOMOUS . ': ' .
            self::MAX_POSITION_LIBERAL_AUTONOMOUS_DESC,
            self::MAX_POSITION_ADMINISTRATOR => self::MAX_POSITION_ADMINISTRATOR . ': ' .
            self::MAX_POSITION_ADMINISTRATOR_DESC,
            self::MAX_POSITION_PRODUCTION => self::MAX_POSITION_PRODUCTION . ': ' .
            self::MAX_POSITION_PRODUCTION_DESC,
            self::MAX_POSITION_AUTONOMOUS => self::MAX_POSITION_AUTONOMOUS . ': ' .
            self::MAX_POSITION_AUTONOMOUS_DESC,
            self::MAX_POSITION_SMALL_PRODUCERS => self::MAX_POSITION_SMALL_PRODUCERS . ': ' .
            self::MAX_POSITION_SMALL_PRODUCERS_DESC,
            self::MAX_POSITION_DOMESTICS => self::MAX_POSITION_DOMESTICS . ': ' .
            self::MAX_POSITION_DOMESTICS_DESC,
            self::MAX_POSITION_RURAL_WORKER => self::MAX_POSITION_RURAL_WORKER . ': ' .
            self::MAX_POSITION_RURAL_WORKER_DESC,
            self::MAX_POSITION_STUDENTOROTHER => self::MAX_POSITION_STUDENTOROTHER . ': ' .
            self::MAX_POSITION_STUDENTOROTHER_DESC,
        ];
    }

    /**
     *
     * @param string $interviewMaxPosition
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewMaxPosition($interviewMaxPosition)
    {
        $this->interviewMaxPosition = $interviewMaxPosition;
        return $this;
    }
    
    /**
     * 
     * @return float
     */
    public function getInterviewSocioeconomicGrade()
    {
        return $this->interviewSocioeconomicGrade;
    }

    /**
     * 
     * @param float $interviewSocioeconomicGrade
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewSocioeconomicGrade($interviewSocioeconomicGrade)
    {
        $this->interviewSocioeconomicGrade = $interviewSocioeconomicGrade;
        return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getInterviewerSocioecGradeJustification()
    {
        return $this->interviewerSocioecGradeJustification;
    }

    /**
     *
     * @param string $interviewerSocioecGradeJustification
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewerSocioecGradeJustification($interviewerSocioecGradeJustification)
    {
        $this->interviewerSocioecGradeJustification = $interviewerSocioecGradeJustification;
        return $this;
    }
    /* ####################### Avaliação da vulnerabilidade ################### */

    /**
     *
     * @return string
     */
    public function getInterviewFamilyProvider()
    {
        return $this->interviewFamilyProvider;
    }

    /**
     *
     * @param string $interviewFamilyProvider
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewFamilyProvider($interviewFamilyProvider)
    {
        $this->interviewFamilyProvider = $interviewFamilyProvider;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getInterviewHasChildren()
    {
        return $this->interviewHasChildren;
    }

    /**
     *
     * @param string $interviewHasChildren
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewHasChildren($interviewHasChildren)
    {
        $this->interviewHasChildren = $interviewHasChildren;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getInterviewHasDisease()
    {
        return $this->interviewHasDisease;
    }

    /**
     *
     * @param string $interviewHasDisease
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewHasDisease($interviewHasDisease)
    {
        $this->interviewHasDisease = $interviewHasDisease;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getInterviewHighSchool()
    {
        return $this->interviewHighSchool;
    }

    /**
     *
     * @param string $interviewHighSchool
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewHighSchool($interviewHighSchool)
    {
        $this->interviewHighSchool = $interviewHighSchool;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getInterviewFamSupport()
    {
        return $this->interviewFamSupport;
    }

    /**
     *
     * @param string $interviewFamSupport
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewFamSupport($interviewFamSupport)
    {
        $this->interviewFamSupport = $interviewFamSupport;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getInterviewFamDependency()
    {
        return $this->interviewFamDependency;
    }

    /**
     *
     * @param string $interviewFamDependency
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewFamDependency($interviewFamDependency)
    {
        $this->interviewFamDependency = $interviewFamDependency;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getIntervewNeedToWork()
    {
        return $this->intervewNeedToWork;
    }

    /**
     *
     * @param string $intervewNeedToWork
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setIntervewNeedToWork($intervewNeedToWork)
    {
        $this->intervewNeedToWork = $intervewNeedToWork;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getInterviewSingleton()
    {
        return $this->interviewSingleton;
    }

    /**
     *
     * @param string $interviewSingleton
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewSingleton($interviewSingleton)
    {
        $this->interviewSingleton = $interviewSingleton;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getIntervewFamilyPropAndGoods()
    {
        return $this->intervewFamilyPropAndGoods;
    }

    /**
     *
     * @param string $intervewFamilyPropAndGoods
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setIntervewFamilyPropAndGoods($intervewFamilyPropAndGoods)
    {
        $this->intervewFamilyPropAndGoods = $intervewFamilyPropAndGoods;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getInterviewStudentVulnerability()
    {
        return $this->interviewStudentVulnerability;
    }

    /**
     *
     * @return array
     */
    public static function getInterviewStudentVulnerabilityArray()
    {
        return [
            self::VULNERABILITY_HIGH => '(8 a 10 pontos) ' . self::VULNERABILITY_HIGH .
            ': ' . self::VULNERABILITY_HIGH_DESC,
            self::VULNERABILITY_MIDDLE => '(5 a 8 pontos) ' . self::VULNERABILITY_MIDDLE .
            ': ' . self::VULNERABILITY_MIDDLE_DESC,
            self::VULNERABILITY_LOW => '(3 a 5 pontos) ' . self::VULNERABILITY_LOW .
            ': ' . self::VULNERABILITY_LOW_DESC,
            self::VULNERABILITY_TEMPORARY => '(1 a 3 pontos) ' . self::VULNERABILITY_TEMPORARY .
            ': ' . self::VULNERABILITY_TEMPORARY_DESC,
            self::VULNERABILITY_NONE => '(zero pontos) ' . self::VULNERABILITY_NONE .
            ': ' . self::VULNERABILITY_NONE_DESC,
        ];
    }

    /**
     *
     * @param string $interviewStudentVulnerability
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewStudentVulnerability($interviewStudentVulnerability)
    {
        $this->interviewStudentVulnerability = $interviewStudentVulnerability;
        return $this;
    }

    /**
     *
     * @return float
     */
    public function getInterviewVulnerabilityGrade()
    {
        return $this->interviewVulnerabilityGrade;
    }

    /**
     *
     * @param float $interviewVulnerabilityGrade
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewVulnerabilityGrade($interviewVulnerabilityGrade)
    {
        $this->interviewVulnerabilityGrade = $interviewVulnerabilityGrade;
        return $this;
    }
    
    /**
     * Retorna a justificativa para nota no critério vulnerabilidade.
     * 
     * @return string
     */
    public function getInterviewerVulnerabilityGradeJustification()
    {
        return $this->interviewerVulnerabilityGradeJustification;
    }

    /**
     * Define a justificativa para nota no critério vulnerabilidade.
     * @param string $interviewerVulnerabilityGradeJustification
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewerVulnerabilityGradeJustification($interviewerVulnerabilityGradeJustification)
    {
        $this->interviewerVulnerabilityGradeJustification = $interviewerVulnerabilityGradeJustification;
        return $this;
    }
    
    
    /* #################### Avaliação do perfil do estudante ################## */

    /**
     *
     * @return string
     */
    public function getInterviewStudentQuestion()
    {
        return $this->interviewStudentQuestion;
    }

    /**
     *
     * @return array
     */
    public static function getInterviewStudentQuestionArray()
    {
        return [
            self::INTERVIEW_STUDENT_ADVANCED => '(2 a 4 pontos) ' . self::INTERVIEW_STUDENT_ADVANCED . ': ' .
            self::INTERVIEW_STUDENT_ADVANCED_DESC,
            self::INTERVIEW_STUDENT_MODERATE => '(4 a 7 pontos) ' . self::INTERVIEW_STUDENT_MODERATE . ': ' .
            self::INTERVIEW_STUDENT_MODERATE_DESC,
            self::INTERVIEW_STUDENT_COMMON => '(7 a 10 pontos) ' . self::INTERVIEW_STUDENT_COMMON . ': ' .
            self::INTERVIEW_STUDENT_COMMON_DESC,
            self::INTERVIEW_STUDENT_PROBLEMATIC => '(1 a 2 pontos) ' . self::INTERVIEW_STUDENT_PROBLEMATIC . ': ' .
            self::INTERVIEW_STUDENT_PROBLEMATIC_DESC,
            self::INTERVIEW_STUDENT_NOTMET => '(zero pontos) ' . self::INTERVIEW_STUDENT_NOTMET . ': ' .
            self::INTERVIEW_STUDENT_NOTMET_DESC,
        ];
    }

    /**
     *
     * @param string $interviewStudentQuestion
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewStudentQuestion($interviewStudentQuestion)
    {
        $this->interviewStudentQuestion = $interviewStudentQuestion;
        return $this;
    }

    /**
     *
     * @return float
     */
    public function getInterviewStudentGrade()
    {
        return $this->interviewStudentGrade;
    }

    /**
     *
     * @param float $interviewStudentGrade
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewStudentGrade($interviewStudentGrade)
    {
        $this->interviewStudentGrade = $interviewStudentGrade;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getInterviewerStudentGradeJustification()
    {
        return $this->interviewerStudentGradeJustification;
    }

    /**
     *
     * @param string $interviewerStudentGradeJustification
     * @return \Recruitment\Entity\StudentInterview
     */
    public function setInterviewerStudentGradeJustification($interviewerStudentGradeJustification)
    {
        $this->interviewerStudentGradeJustification = $interviewerStudentGradeJustification;
        return $this;
    }
    
    /**
     * Cálculo da nota no critério socioeconômico.
     * 
     * @param string $iti Renda total
     * @param string $inofm Numero de membros na família
     * @param string $ims Maior escolaridade
     * @param string $iht Tipo de residência
     * @param string $ihs Situação da residência
     * @param string $imp Maior cargo na família
     * @return float Nota no critério socioeconômico
     */
    public static function calculateSocioeconomicGrade($iti, $inofm, $ims, $iht, $ihs, $imp)
    {
        $interviewTotalIncomeArr = [
            self::TOTAL_INCOME_MORETHANSIXTEEN_SALARY => 0.83,
            self::TOTAL_INCOME_EIGHTTOSIXTEEN_SALARY => 1.67,
            self::TOTAL_INCOME_FOURTOEIGHT_SALARY => 2.5,
            self::TOTAL_INCOME_TWOTOFOUR_SALARY => 3.33,
            self::TOTAL_INCOME_ONEANDHALFTOTWO_SALARY => 4.17,
            self::TOTAL_INCOME_ONETOONEANDHALF_SALARY => 5,
            self::TOTAL_INCOME_HALFTOONE_SALARY => 7.5,
            self::TOTAL_INCOME_HALF_SALARY => 10,
        ];

        $interviewNumberOfFamilyMembersArr = [
            self::FAMILY_MEMBERS_ONEORTWO => 1.67,
            self::FAMILY_MEMBERS_THREEORFOUR => 3.33,
            self::FAMILY_MEMBERS_FIVEORSIX => 5,
            self::FAMILY_MEMBERS_SEVENOREIGHT => 6.67,
            self::FAMILY_MEMBERS_NINEORMORE => 10,
        ];

        $interviewMaxScholarityArr = [
            self::MAXSCHOLARITY_GRADUATIONORMORE => 0,
            self::MAXSCHOLARITY_HIGHSCHOOL => 1.43,
            self::MAXSCHOLARITY_ELEMENTARYSCHOOLII => 2.86,
            self::MAXSCHOLARITY_ELEMENTARYSCHOOLI => 4.29,
            self::MAXSCHOLARITY_ELEMENTARYSCHOOLI_COMPLETE => 4.29,
            self::MAXSCHOLARITY_ELEMENTARYSCHOOLI_INCOMPLETE => 5.71,
            self::MAXSCHOLARITY_LITERATE => 7.14,
            self::MAXSCHOLARITY_ILITERATE => 10,
        ];

        $interviewHomeTypeArr = [
            self::HOME_TYPE_OWN => 2.5,
            self::HOME_TYPE_FUNDED => 5,
            self::HOME_TYPE_RENTED => 7.5,
            self::HOME_TYPE_GIVEIN => 10,
        ];

        $interviewHomeSituationArr = [
            self::HOME_SITUATION_UNSATISFACTORY => 10,
            self::HOME_SITUATION_GOOD => 6.67,
            self::HOME_SITUATION_REGULAR => 3.33,
            self::HOME_SITUATION_GREAT => 0,
        ];

        $interviewMaxPositionArr = [
            self::MAX_POSITION_BUSINESSMAN => 0,
            self::MAX_POSITION_HIGH_ADMINISTRADOR => 0.77,
            self::MAX_POSITION_LIBERAL_AUTONOMOUS => 2.31,
            self::MAX_POSITION_ADMINISTRATOR => 3.85,
            self::MAX_POSITION_PRODUCTION => 5.38,
            self::MAX_POSITION_AUTONOMOUS => 5.38,
            self::MAX_POSITION_SMALL_PRODUCERS => 6.92,
            self::MAX_POSITION_DOMESTICS => 7.69,
            self::MAX_POSITION_RURAL_WORKER => 8.46,
            self::MAX_POSITION_STUDENTOROTHER => 10,
        ];
        
        $grade = (
            $interviewTotalIncomeArr[$iti]
            + $interviewNumberOfFamilyMembersArr[$inofm]
            + $interviewMaxScholarityArr[$ims]
            + $interviewHomeTypeArr[$iht]
            + $interviewHomeSituationArr[$ihs]
            + $interviewMaxPositionArr[$imp]
            ) / 6;
        
        return $grade;
    }
}
