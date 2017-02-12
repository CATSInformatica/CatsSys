<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Controller;

use Authentication\Service\EmailSenderServiceInterface;
use Database\Controller\AbstractEntityActionController;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use InvalidArgumentException;
use Recruitment\Entity\Recruitment;
use Recruitment\Entity\RecruitmentStatus;
use Recruitment\Entity\Registration;
use Recruitment\Form\RegistrationForm;
use Recruitment\Form\SearchRegistrationsForm;
use Recruitment\Form\TimestampForm;
use Recruitment\Service\AddressService;
use Recruitment\Service\PersonService;
use Recruitment\Service\RegistrationStatusService;
use RuntimeException;
use Zend\File\Transfer\Adapter\Http as HttpAdapter;
use Zend\Form\View\Helper\Captcha\Image;
use Zend\Json\Json;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer as ViewRenderer;

/**
 * 
 * @todo Fazer as actions de convocação e aceitação
 * Description of RegistrationController
 * @author marcio
 */
class RegistrationController extends AbstractEntityActionController
{

    /**
     * Helpers que ajudam a salvar os dados pessoais, de endereço e status de inscrição.
     */
    use AddressService,
        PersonService,
        RegistrationStatusService;

    /**
     * Diretório onde estão armazenadas as fotos dos perfils de usuário.
     */
    const PROFILE_DIR = './data/profile/';

    /**
     *
     * @var EmailSenderServiceInterface Permite acessar o serviço de envio de emails.
     */
    protected $emailService;

    /**
     *
     * @var ViewRenderer Necessário para criar o cartão de inscrição e colocá-lo no corpo do email. 
     */
    protected $viewRenderer;

    public function __construct(EmailSenderServiceInterface $emailService, ViewRenderer $vr)
    {
        $this->emailService = $emailService;
        $this->viewRenderer = $vr;
    }

    /**
     * 
     * @todo criar índice no campo recruitmentType da entidade Recruitment
     * 
     * Exibe todas as inscrições do processo seletivo de alunos escolhido (inicialmente exibe o último 
     * processo seletivo vigente).
     * 
     * @return ViewModel
     */
    public function indexAction()
    {
        try {
            $em = $this->getEntityManager();
            $form = new SearchRegistrationsForm($em, Recruitment::STUDENT_RECRUITMENT_TYPE);

            return new ViewModel(array(
                'message' => null,
                'form' => $form,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema.',
                'form' => null,
            ));
        }
    }

    public function volunteerRegistrationsAction()
    {
        try {
            $em = $this->getEntityManager();
            $form = new SearchRegistrationsForm($em, Recruitment::VOLUNTEER_RECRUITMENT_TYPE);
            $timestampForm = new TimestampForm();

            return new ViewModel(array(
                'message' => null,
                'form' => $form,
                'timestamp' => $timestampForm,
            ));
        } catch (\Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema.',
                'form' => null,
            ));
        }
    }

    public function registrationFormsAction()
    {
        return new ViewModel([
        ]);
    }

    public function candidateAction()
    {
        try {
            $this->layout('application-clean/layout');

            $studentContainer = new Container('candidate');

            //se o id não estiver na sessão, redireciona para a página de acesso
            if (!$studentContainer->offsetExists('regId')) {
                return $this->redirect()->toRoute('recruitment/registration', array(
                        'action' => 'access',
                ));
            }

            $id = $studentContainer->offsetGet('regId');
            $em = $this->getEntityManager();
            $registration = $em->find('Recruitment\Entity\Registration', $id);
            $recruitment = $registration->getRecruitment();

            // para o formulário de edição de dados do candidato
            $request = $this->getRequest();
            $form = new RegistrationForm($em);
            $form->bind($registration);
            if ($request->isPost()) {

                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $em->merge($registration);
                    $em->flush();
                }
            }

            // Testes para decidir a situaçao dos blocos
            $currentStatus = $registration->getCurrentRegistrationStatus()->getRecruitmentStatus()->getNumericStatusType();
            $currentDate = new \DateTime();
            $examDate = $recruitment->getExamDateAsDateTime();
            $examResultDate = $recruitment->getExamResultDateAsDateTime();
            $resultDate = $recruitment->getResultDateAsDateTime();
            $preinterviewDate = $recruitment->getPreInterviewStartDateAsDateTime();
            $interviewDate = $recruitment->getInterviewStartDateAsDateTime();

            $blockStatus = [
                'registration' => false,
                'confirmation' => false,
                'exam' => $examDate <= $currentDate,
                'exam-result' => $examResultDate <= $currentDate,
                'preinterview' => false,
                'preinterview-complete' => false,
                'interview' => false,
                'interviewed' => false,
                'result' => $resultDate <= $currentDate,
                'enroll' => false,
            ];

            switch ($currentStatus) {
                case RecruitmentStatus::STATUSTYPE_INTERVIEW_APPROVED:
                    $blockStatus['enroll'] = $resultDate <= $currentDate;
                case RecruitmentStatus::STATUSTYPE_INTERVIEWED:
                    $blockStatus['interviewed'] = true;
                case RecruitmentStatus::STATUSTYPE_CALLEDFOR_INTERVIEW:
                case RecruitmentStatus::STATUSTYPE_PREINTERVIEW_COMPLETE:
                    $blockStatus['interview'] = $interviewDate <= $currentDate;
                    $blockStatus['preinterview-complete'] = true;
                case RecruitmentStatus::STATUSTYPE_CALLEDFOR_PREINTERVIEW:
                    $blockStatus['preinterview'] = $preinterviewDate <= $currentDate;
                case RecruitmentStatus::STATUSTYPE_EXAM_DISAPPROVED:
                case RecruitmentStatus::STATUSTYPE_EXAM_WAITING_LIST:
                case RecruitmentStatus::STATUSTYPE_CONFIRMED:
                    $blockStatus['confirmation'] = true;
                case RecruitmentStatus::STATUSTYPE_REGISTERED:
                    $blockStatus['registration'] = true;
            }

            return new ViewModel([
                'registration' => $registration,
                'blockStatus' => $blockStatus,
                'recruitment' => $registration->getRecruitment(),
                'form' => $form,
            ]);
        } catch (\Exception $ex) {
            return new ViewModel([
                'registration' => null,
            ]);
        }
    }

    public function accessAction()
    {
        $this->layout('application-clean/layout');
        $request = $this->getRequest();
        $form = new \Recruitment\Form\CpfForm();

        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);

            if ($form->isValid()) {
                $data = $form->getData();

                try {
                    $em = $this->getEntityManager();

                    $registration = $em->getRepository('Recruitment\Entity\Registration')
                        ->findOneByPersonCpf($data['person_cpf']);

                    if ($registration !== null) {
                        $studentContainer = new Container('candidate');
                        $studentContainer->offsetSet('regId', $registration->getRegistrationId());

                        return $this->redirect()->toRoute('recruitment/registration', array(
                                'action' => 'candidate'
                        ));
                    }
                } catch (Exception $ex) {
                    $message = 'Erro inesperado. Não foi possível encontrar uma inscrição associada a este cpf.'
                        . $ex->getMessage();
                }
                $message = 'Cpf não encontrado';
            } else {
                $message = null;
            }
        } else {
            $message = null;
        }

        return new ViewModel(array(
            'message' => $message,
            'form' => $form,
        ));
    }

    /**
     * 
     * @todo
     *      - Concluir o template do comprovante de inscrição
     *      - Mostrar o comprovante de inscrição no navegador após concluir a inscrição.
     * 
     * Exibe o formulário de inscrição (alunos e voluntários) e faz a validação do envio.
     * 
     * Uma nova inscrição poderá ser feita/será aceita se, e somente se, a seguintes condições forem satisfeitas
     *  - Existe um processo seletivo aberto \Recruitment\Entity\Recruitment
     *  - A pessoa que está se inscrevendo ainda não fez a inscrição no processo seletivo vigente
     * 
     * Ao fazer a inscrição, caso a pessoa já possua cadastro, alguns dados pessoais serão atualizados
     * e uma nova inscrição será cadastrada, ou seja:
     *  - Update em Recruitment\Entity\Person
     *  - Insert em Recruitment\Entity\Registration
     * 
     * Caso a pessoa não possua cadastro será criada uma nova pessoa e uma nova inscrição, ou seja:
     *  - Insert Recruitment\Entity\Person
     *  - Insert Recruitment\Entity\Registration
     * 
     * 
     * @return ViewModel Formulário de inscrição
     */
    public function registrationFormAction()
    {
        $this->layout('application-clean/layout');

        $type = (int) $this->params('id', Recruitment::STUDENT_RECRUITMENT_TYPE);

        try {
            $em = $this->getEntityManager();
            // Busca por um processo seletivo aberto
            $recruitment = $em->getRepository('Recruitment\Entity\Recruitment')
                ->findByTypeAndBetweenBeginAndEndDates($type, new \DateTime('now'));
            if ($recruitment === null) {
                return new ViewModel(array(
                    'message' => 'Não existe nenhum processo seletivo vigente no momento.',
                    'form' => null,
                ));
            }
        } catch (\Exception $ex) {
            return new ViewModel(array(
                'message' => 'Não foi possível verificar a existência de processos seletivos abertos.',
                'form' => null,
            ));
        }

        $options = array(
            'interview' => false,
            'person' => array(
                'address' => true,
                'relative' => false,
                'social_media' => true,
            ),
        );

        $request = $this->getRequest();
        $form = ($type === Recruitment::STUDENT_RECRUITMENT_TYPE ? new RegistrationForm($em) :
                new RegistrationForm($em, $type, $options));

        if ($request->isPost()) {

            $registration = new Registration();
            $form->bind($registration);
            $form->setData($request->getPost());

            if ($form->isValid()) {

                try {
                    // verifica se a pessoa já está cadastrada.
                    $this->adjustPerson($registration);

                    $this->updateRegistrationStatus(
                        $registration, RecruitmentStatus::STATUSTYPE_REGISTERED, $registration->getRegistrationDateAsDateTime()
                    );

                    // atribui a qual processo seletivo a inscrição pertence
                    echo '<pre>';
                    print_r($recruitment);
                    exit;
                    $registration->setRecruitment($recruitment);

                    // salva no banco
                    $em->persist($registration);
                    $em->flush();

                    // redirecionar para a página que gera o comprovante de inscrição e envia o email.
                    if ($type == Recruitment::STUDENT_RECRUITMENT_TYPE) {

                        $subject = 'Processo Seletivo de Alunos';

                        // comprovante de inscrição
                        $person = $registration->getPerson();

                        $registrationCardContent = [
                            'publicNotice' => $registration->getRecruitment()->getRecruitmentPublicNotice(),
                            'registrationNumber' => $registration->getRegistrationNumber(),
                            'name' => $person->getPersonName(),
                            'rg' => $person->getPersonRg(),
                            'cpf' => $person->getPersonCpf(),
                        ];

                        $view = new ViewModel($registrationCardContent);

                        // gera o conteúdo do email do candidato.
                        $view->setTemplate('registration-card/template');
                        $view->setTerminal(true);
                        $emailBody = $this->viewRenderer->render($view);

                        $person = $registration->getPerson();

                        // envia email para o candidato
                        $this->emailService
                            ->setSubject($subject . ' 🚀')
                            ->setBody($emailBody)
                            ->setIsHtml(true)
                            ->addTo($person->getPersonEmail(), $person->getPersonFirstName());


                        $this->emailService->send();

                        //Pegar id do candidato
                        $id = $registration->getRegistrationId();

                        $studentContainer = new Container('candidate');
                        $studentContainer->offsetSet('regId', $id);

                        return $this->redirect()->toRoute('recruitment/registration', array(
                                'action' => 'candidate'
                        ));
                    }

                    return new ViewModel(array(
                        'message' => 'Inscrição efetuada com sucesso. Um de nossos voluntários entrará em contato com '
                        . 'você (no período especificado no edital) para agendar uma entrevista.',
                        'form' => null,
                    ));
                } catch (Exception $ex) {
                    if ($ex instanceof UniqueConstraintViolationException) {

                        if ($type == Recruitment::STUDENT_RECRUITMENT_TYPE) {
                            $message = 'Já existe uma inscrição associada ao CPF informado. Por favor, consulte a área do candidato';
                        } else {
                            $message = 'Já existe uma inscrição associada ao CPF informado.';
                        }

                        $form = null;
                    } else {
                        $message = 'Erro inesperado. Por favor, tente novamente ou'
                            . ' entre em contato com o administrador do sistema: ' . $ex->getMessage();
                    }
                    return new ViewModel(array(
                        'message' => $message,
                        'form' => $form,
                        'type' => $type,
                    ));
                }
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'type' => $type,
        ));
    }

    /**
     * Busca todos as inscrições pro processo seletivo $rid com status atual $sid.
     * 
     * @return JsonModel
     */
    public function getRegistrationsAction()
    {

        $request = $this->getRequest();

        $result = [];

        try {
            if ($request->isPost()) {
                $data = $request->getPost();
                if (isset($data['recruitment']) && isset($data['registrationStatus'])) {

                    $em = $this->getEntityManager();
                    $regs = $em->getRepository('Recruitment\Entity\Registration')->findByStatusType($data['recruitment'], $data['registrationStatus']);

                    foreach ($regs as $r) {
                        $status = $r->getCurrentRegistrationStatus();
                        $timestamp = $status->getTimestamp();
                        $statusType = $status->getRecruitmentStatus()->getStatusType();
                        $person = $r->getPerson();

                        $result[] = array(
                            'registrationId' => $r->getRegistrationId(),
                            'registrationNumber' => $r->getRegistrationNumber(),
                            'registrationDate' => $r->getRegistrationDate(),
                            'personName' => $person->getPersonName(),
                            'personCpf' => $person->getPersonCpf(),
                            'personRg' => $person->getPersonRg(),
                            'personEmail' => $person->getPersonEmail(),
                            'status' => array(
                                'type' => $statusType,
                                'timestamp' => $timestamp,
                            )
                        );
                    }
                }
            }
        } catch (\Exception $ex) {
        }
        
        return new JsonModel($result);
    }

    public function getConfirmedAction()
    {

        $request = $this->getRequest();

        if ($request->isPost()) {

            $rec = $request->getPost();

            if (!empty($rec)) {
                try {

                    $em = $this->getEntityManager();
                    $regs = $em->getRepository('Recruitment\Entity\Registration')->findByStatusType($rec['id'], RecruitmentStatus::STATUSTYPE_CONFIRMED);
                    $candidates = [];

                    foreach ($regs as $r) {
                        $person = $r->getPerson();

                        $candidates[] = array(
                            'registrationId' => $r->getRegistrationId(),
                            'registrationNumber' => $r->getRegistrationNumber(),
                            'personName' => $person->getPersonName()
                        );
                    }

                    return new JsonModel([
                        'candidates' => $candidates,
                    ]);
                } catch (\Exception $ex) {
                    return new JsonModel([
                        'candidates' => null
                    ]);
                }
            }
        }
    }

    /**
     * Altera a situação do candidato do processo seletivo de alunos para 
     * Confirmado.
     * 
     * @return JsonModel
     */
    public function confirmationAction()
    {
        $id = $this->params('id', false);

        if ($id) {

            try {
                $em = $this->getEntityManager();
                $registration = $em->getReference('Recruitment\Entity\Registration', $id);

                $currentStatus = $registration->getCurrentRegistrationStatus()->getRecruitmentStatus()->getNumericStatusType();

                if ($currentStatus === RecruitmentStatus::STATUSTYPE_CONFIRMED) {
                    $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_REGISTERED);
                    $newStatus = RecruitmentStatus::STATUSTYPEDESC_REGISTERED;
                } else {
                    $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_CONFIRMED);
                    $newStatus = RecruitmentStatus::STATUSTYPEDESC_CONFIRMED;
                }

                $em->persist($registration);
                $em->flush();

                $dt = new \DateTime();

                return new JsonModel(array(
                    'message' => 'Status alterado com sucesso.',
                    'callback' => array(
                        'timestamp' => $dt->format('d/m/Y H:i:s'),
                        'status' => $newStatus,
                    ),
                ));
            } catch (Exception $ex) {
                return new JsonModel(array(
                    'message' => 'Erro inesperado: ' . $ex->getMessage(),
                ));
            }
        }

        return new JsonModel(array(
            'message' => 'Nenhum candidato selecionado',
        ));
    }

    /**
     * Altera a situação do candidato do processo seletivo de alunos para 
     * Desclassificado na Prova.
     * 
     * @return JsonModel
     */
    public function examDisapproveAction()
    {
        $id = $this->params('id', false);

        if ($id) {

            try {
                $em = $this->getEntityManager();
                $registration = $em->getReference('Recruitment\Entity\Registration', $id);


                $currentStatus = $registration->getCurrentRegistrationStatus()->getRecruitmentStatus()->getNumericStatusType();

                if ($currentStatus === RecruitmentStatus::STATUSTYPE_EXAM_DISAPPROVED) {
                    $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_CONFIRMED);
                    $newStatus = RecruitmentStatus::STATUSTYPEDESC_CONFIRMED;
                } else {
                    $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_EXAM_DISAPPROVED);
                    $newStatus = RecruitmentStatus::STATUSTYPEDESC_EXAM_DISAPPROVED;
                }

                $em->persist($registration);
                $em->flush();

                $dt = new \DateTime();

                return new JsonModel(array(
                    'message' => 'Status alterado com sucesso',
                    'callback' => array(
                        'timestamp' => $dt->format('d/m/Y H:i:s'),
                        'status' => $newStatus,
                    ),
                ));
            } catch (Exception $ex) {
                return new JsonModel(array(
                    'message' => 'Erro inesperado: ' . $ex->getMessage(),
                ));
            }
        }

        return new JsonModel(array(
            'message' => 'Nenhum candidato selecionado',
        ));
    }

    /**
     * Altera a situação do candidato do processo seletivo de alunos para 
     * Lista de Espera da Prova.
     * 
     * @return JsonModel
     */
    public function examWaitingListAction()
    {
        $id = $this->params('id', false);

        if ($id) {

            try {
                $em = $this->getEntityManager();
                $registration = $em->getReference('Recruitment\Entity\Registration', $id);

                $currentStatus = $registration->getCurrentRegistrationStatus()->getRecruitmentStatus()->getNumericStatusType();

                if ($currentStatus === RecruitmentStatus::STATUSTYPE_EXAM_WAITING_LIST) {
                    $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_CONFIRMED);
                    $newStatus = RecruitmentStatus::STATUSTYPEDESC_CONFIRMED;
                } else {
                    $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_EXAM_WAITING_LIST);
                    $newStatus = RecruitmentStatus::STATUSTYPEDESC_EXAM_WAITING_LIST;
                }

                $em->persist($registration);
                $em->flush();

                $dt = new \DateTime();

                return new JsonModel(array(
                    'message' => 'Status alterado com sucesso',
                    'callback' => array(
                        'timestamp' => $dt->format('d/m/Y H:i:s'),
                        'status' => $newStatus,
                    ),
                ));
            } catch (Exception $ex) {
                return new JsonModel(array(
                    'message' => 'Erro inesperado: ' . $ex->getMessage(),
                ));
            }
        }

        return new JsonModel(array(
            'message' => 'Nenhum candidato selecionado',
        ));
    }

    /**
     * Altera a situação do candidato do processo seletivo de alunos para 
     * Convocado.
     * 
     * @return JsonModel
     */
    public function convocationAction()
    {
        $id = $this->params('id', false);

        if ($id) {

            try {
                $em = $this->getEntityManager();
                $registration = $em->getReference('Recruitment\Entity\Registration', $id);

                /**
                 * Atualizar status do candidato
                 */
                $currentStatus = $registration->getCurrentRegistrationStatus()->getRecruitmentStatus()->getNumericStatusType();

                if ($currentStatus === RecruitmentStatus::STATUSTYPE_CALLEDFOR_PREINTERVIEW) {
                    $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_CONFIRMED);
                    $newStatus = RecruitmentStatus::STATUSTYPEDESC_CONFIRMED;
                } else {
                    $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_CALLEDFOR_PREINTERVIEW);
                    $newStatus = RecruitmentStatus::STATUSTYPEDESC_CALLEDFOR_PREINTERVIEW;
                }

                $em->persist($registration);
                $em->flush();

                $dt = new \DateTime();

                return new JsonModel(array(
                    'message' => 'Status alterado com sucesso.',
                    'callback' => array(
                        'timestamp' => $dt->format('d/m/Y H:i:s'),
                        'status' => $newStatus,
                    ),
                ));
            } catch (Exception $ex) {
                return new JsonModel(array(
                    'message' => 'Erro inesperado: ' . $ex->getMessage(),
                ));
            }
        }

        return new JsonModel(array(
            'message' => 'Nenhum candidato selecionado',
        ));
    }

    /**
     * Altera a situação do candidato do processo seletivo de alunos para 
     * Aprovado.
     * 
     * @return JsonModel
     */
    public function acceptanceAction()
    {
        $id = $this->params('id', false);

        if ($id) {

            try {
                $em = $this->getEntityManager();
                $registration = $em->getReference('Recruitment\Entity\Registration', $id);

                /**
                 * Atualizar status do candidato
                 */
                $currentStatus = $registration->getCurrentRegistrationStatus()->getRecruitmentStatus()->getNumericStatusType();

                if ($currentStatus === RecruitmentStatus::STATUSTYPE_INTERVIEW_APPROVED) {
                    $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_INTERVIEWED);
                    $newStatus = RecruitmentStatus::STATUSTYPEDESC_INTERVIEWED;
                } else {
                    $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_INTERVIEW_APPROVED);
                    $newStatus = RecruitmentStatus::STATUSTYPEDESC_INTERVIEW_APPROVED;
                }

                $em->persist($registration);
                $em->flush();

                $dt = new \DateTime();

                return new JsonModel(array(
                    'message' => 'Status alterado com sucesso.',
                    'callback' => array(
                        'timestamp' => $dt->format('d/m/Y H:i:s'),
                        'status' => $newStatus,
                    ),
                ));
            } catch (Exception $ex) {
                return new JsonModel(array(
                    'message' => 'Erro inesperado: ' . $ex->getMessage(),
                ));
            }
        }

        return new JsonModel(array(
            'message' => 'Nenhum candidato selecionado',
        ));
    }

    public function updateStatusAction()
    {

        $rid = $this->params('id', false);
        $sid = $this->params('sid', false);

        $request = $this->getRequest();

        if ($request->isPost() && $rid && $sid) {

            try {

                $em = $this->getEntityManager();
                $data = $request->getPost()->toArray();

                if (isset($data['timestamp'])) {
                    $form = new TimestampForm();
                    $form->setData($data);

                    if ($form->isValid()) {
                        $data = $form->getData();
                    } else {
                        throw new InvalidArgumentException('Data inválida');
                    }

                    $dt = new \DateTime($data['timestamp']);
                } else {
                    $dt = new \DateTime('now');
                }

                $registration = $em->getReference('Recruitment\Entity\Registration', $rid);

                /**
                 * Atualizar status do candidato
                 */
                $this->updateRegistrationStatus($registration, $sid, $dt);

                $em->persist($registration);
                $em->flush();




                return new JsonModel(array(
                    'message' => 'Situação do candidato alterada para ' . RecruitmentStatus::statusTypeToString($sid),
                    'callback' => array(
                        'timestamp' => $dt->format('d/m/Y H:i:s'),
                        'status' => RecruitmentStatus::statusTypeToString($sid),
                    ),
                ));
            } catch (Exception $ex) {

                return new JsonModel(array(
                    'message' => 'Erro: ' . $ex->getMessage(),
                ));
            }
        }

        return new JsonModel(array(
            'message' => 'Esta url só pode ser acessada via POST.',
        ));
    }

    /**
     * 
     * Método seguro para exibição da foto de perfil
     * 
     * @todo Fazer validação por usuário:
     *  - Voluntário: acesso apenas ao seu
     *  - aluno: acesso apenas ao seu
     *  - RH: acesso a todos
     * 
     * @return Image Foto do perfil do usuário. Caso não haja uma específica, utiliza as imagens padrões
     */
    public function photoAction()
    {
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', "image/png");
        $id = $this->params('id', false);

        if ($id) {

            try {
                $em = $this->getEntityManager();
                $person = $em->getReference('Recruitment\Entity\Person', $id);
                $photo = self::PROFILE_DIR . $person->getPersonPhoto();

                if (file_exists($photo) !== false) {
                    $photoContent = file_get_contents($photo);

                    $response->setStatusCode(200);
                    $response->setContent($photoContent);
                }
            } catch (\Exception $ex) {
                $response = 'Erro. Por favor entre em contato com o administrador do sistema.';
            }
        }

        return $response;
    }

    /**
     * @todo Fazer verificação por usuário de forma idêntica a action photo
     * 
     * Altera a foto de perfil, aceita apenas imagens no formato jpg ou png
     * 
     * @return ViewModel
     * @throws RuntimeException
     */
    public function changePhotoAction()
    {
        $id = $this->params('id', false);
        $request = $this->getRequest();
        if ($id && $request->isPost()) {
            try {
                $file = $request->getFiles()->profilePhoto;

                $em = $this->getEntityManager();
                $person = $em->find('Recruitment\Entity\Person', $id);

                $targetDir = self::PROFILE_DIR;
                $targetName = $id;

                switch ($file['type']) {
                    case 'image/jpeg':
                        $targetName .= '.jpg';
                        if (file_exists($targetDir . $id . '.png')) {
                            unlink($targetDir . $id . '.png');
                        }
                        break;
                    case 'image/png':
                        $targetName .= '.png';
                        if (file_exists($targetDir . $id . '.jpg')) {
                            unlink($targetDir . $id . '.jpg');
                        }
                        break;
                    default:
                        throw new RuntimeException('Formato inválido, apenas imagens no '
                        . 'formato jpg e png são aceitas.');
                }

                $targetFile = $targetDir . $targetName;

                $uploadAdapter = new HttpAdapter();

                $uploadAdapter->addFilter('File\Rename', array(
                    'target' => $targetFile,
                    'overwrite' => true
                ));

                $uploadAdapter->setDestination($targetDir);

                if (!$uploadAdapter->receive()) {
                    $messages = implode('\n', $uploadAdapter->getMessages());
                    throw new RuntimeException("Error: " . $messages);
                }

                $person->setPersonPhoto($targetName);

                $em->persist($person);
                $em->flush();

                return new JsonModel(array(
                    'message' => 'Imagem enviada com sucesso.',
                    'file' => '/recruitment/registration/photo/' . $id,
                ));
            } catch (Exception $ex) {
                return new JsonModel(array(
                    'message' => $ex->getMessage(),
                    'file' => null,
                ));
            }
        }
    }

    public function examResultAction()
    {
        $this->layout('application-clean/layout');
        $studentContainer = new Container('candidate');

        // id de inscrição não está na sessão redireciona para o início
        if (!$studentContainer->offsetExists('regId')) {
            return $this->redirect()->toRoute('recruitment/registration', array(
                    'action' => 'access',
            ));
        }

        $registrationId = $studentContainer->offsetGet('regId');

        try {

            $em = $this->getEntityManager();
            $reg = $em->find('Recruitment\Entity\Registration', $registrationId);

            $recruitment = $reg->getRecruitment();

            $application = $em->getRepository('SchoolManagement\Entity\ExamApplication')->findOneBy([
                'recruitment' => $recruitment->getRecruitmentId(),
            ]);


            if (empty($application)) {
                return new ViewModel([
                    'message' => 'Não foi encontrada nenhuma aplicação de prova associada a este processo seletivo',
                ]);
            }

            $appResults = $em->getRepository('SchoolManagement\Entity\ExamApplicationResult')->findBy([
                'application' => $application->getExamApplicationId(),
            ]);

            $results = [];
            foreach ($appResults as $res) {

                $appReg = $res->getRegistration();
                $r = Json::decode($res->getResult(), Json::TYPE_ARRAY);

                $results[] = [
                    'registrationId' => $appReg->getRegistrationId(),
                    'registrationNumber' => $appReg->getRegistrationNumber(),
                    'partialResult' => $r['partialResult'],
                    'result' => $r['result'],
                    'groups' => $r['groups'],
                    'position' => $r['position']
                ];
            }

            $status = $reg->getCurrentRegistrationStatus()->getRecruitmentStatus()->getStatusType();

            return new ViewModel([
                'message' => null,
                'registration' => $reg,
                'recruitment' => $recruitment,
                'results' => $results,
                'status' => $status,
            ]);
        } catch (\Throwable $ex) {
            return new ViewModel([
                'message' => $ex->getMessage(),
            ]);
        }
    }

    public function finalResultAction()
    {

        $this->layout('application-clean/layout');
        $studentContainer = new Container('candidate');

        // id de inscrição não está na sessão redireciona para o início
        if (!$studentContainer->offsetExists('regId')) {
            return $this->redirect()->toRoute('recruitment/registration', array(
                    'action' => 'access',
            ));
        }

        $registrationId = $studentContainer->offsetGet('regId');

        try {

            $em = $this->getEntityManager();
            $reg = $em->find('Recruitment\Entity\Registration', $registrationId);
            $recruitment = $reg->getRecruitment();
            $resultsArr = $em->getRepository('Recruitment\Entity\Registration')->findInterviewed($recruitment->getRecruitmentId());

            $year = $recruitment->getRecruitmentYear();
            $number = $recruitment->getRecruitmentNumber();

            $results = [];

            foreach ($resultsArr as $r) {
                $results[] = [
                    'registrationNumber' => Registration::generateRegistrationNumber($r['registrationId'], $year, $number),
                    'status' => RecruitmentStatus::statusTypeToString($r['statusType']),
                    'socioeconomic' => $r['interviewSocioeconomicGrade'],
                    'vulnerability' => $r['interviewVulnerabilityGrade'],
                    'student' => $r['interviewStudentGrade']
                ];
            }

            return new ViewModel([
                'message' => null,
                'results' => $results,
                'recruitment' => $recruitment,
                'registration' => $reg,
            ]);
        } catch (\Exception $ex) {
            return new ViewModel([
                'message' => 'Erro inesperado, por favor, entre em contato com o administrador do sistema',
            ]);
        }
    }
}
