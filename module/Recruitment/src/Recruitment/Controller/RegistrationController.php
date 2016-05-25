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
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema.',
                'form' => null,
            ));
        }
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
                ->findByTypeAndBetweenBeginAndEndDates($type, new DateTime('now'));
            if ($recruitment === null) {
                return new ViewModel(array(
                    'message' => 'Não existe nenhum processo seletivo vigente no momento.',
                    'form' => null,
                ));
            }
        } catch (Exception $ex) {
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
                        $registration, RecruitmentStatus::STATUSTYPE_REGISTERED,
                        $registration->getRegistrationDateAsDateTime()
                    );

                    // atribui a qual processo seletivo a inscrição pertence
                    $registration->setRecruitment($recruitment);

                    // salva no banco
                    $em->persist($registration);
                    $em->flush();

                    // redirecionar para a página que gera o comprovante de inscrição e envia o email.
                    if ($type == Recruitment::STUDENT_RECRUITMENT_TYPE) {

                        $subject = 'Processo Seletivo de Alunos';
                        $view = new ViewModel(array(
                            'title' => 'Comprovante de Inscrição',
                            'subtitle' => $subject,
                        ));

                        // gera o conteúdo do email do candidato.
                        $view->setTemplate('registration-card/template');
                        $view->setTerminal(true);
                        $emailBody = $this->viewRenderer->render($view);

                        // envia email para o candidato
                        $this->emailService
                            ->setSubject($subject)
                            ->setBody($emailBody)
                            ->setIsHtml(true)
                            ->addTo($registration->getPerson()->getPersonEmail());

                        if ($this->emailService->send()) {
                            // o email foi enviado.
                        } else {
                            // o email não pode ser enviado.
                        }

                        return new ViewModel(array(
                            'message' => 'Inscrição para o processo seletivo de alunos efetuada com sucesso.',
                            'form' => null,
                        ));
                    }

                    return new ViewModel(array(
                        'message' => 'Inscrição efetuada com sucesso. Um de nossos voluntários entrará em contato com '
                        . 'você (no período especificado no edital) para agendar uma entrevista.',
                        'form' => null,
                    ));
                } catch (Exception $ex) {
                    if ($ex instanceof UniqueConstraintViolationException) {
                        $message = 'Não é possível fazer mais de uma inscrição em um mesmo processo seletivo.';
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
     * Esta action é acessada via ajax pelo DataTable Precisa ser refeita
     * 
     * @return JsonModel
     */
    public function getRegistrationsAction()
    {

        $request = $this->getRequest();

        $result = [];

        if ($request->isPost()) {
            try {

                $em = $this->getEntityManager();
                $form = new SearchRegistrationsForm($em);
                $form->setData($request->getPost());

                if ($form->isValid()) {

                    $data = $form->getData();

                    $rid = $data['recruitment'];
                    $sid = $data['registrationStatus'];

                    $em = $this->getEntityManager();
                    $regs = $em->getRepository('Recruitment\Entity\Registration')->findByStatusType($rid, $sid);

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
            } catch (Exception $ex) {
                
            }
        }
        return new JsonModel($result);
    }

    /**
     * 
     * Refactor merge confirmation, convocation and acceptance into one
     * 
     * @return JsonModel
     */
    public function confirmationAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            try {
                $data = $request->getPost();
                $em = $this->getEntityManager();
                $registration = $em->getReference('Recruitment\Entity\Registration', $data['id']);

                $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_CONFIRMED);

                $em->persist($registration);
                $em->flush();

                return new JsonModel(array(
                    'message' => 'Candidato confirmado com sucesso.',
                ));
            } catch (Exception $ex) {
                return new JsonModel(array(
                    'message' => 'Erro inesperado: ' . $ex->getMessage(),
                ));
            }
        }

        return new JsonModel(array(
            'message' => 'Esta url só pode ser acessada via POST.',
        ));
    }

    public function convocationAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            try {
                $data = $request->getPost();
                $em = $this->getEntityManager();
                $registration = $em->getReference('Recruitment\Entity\Registration', $data['id']);

                /**
                 * Atualizar status do candidato
                 */
                $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_CALLEDFOR_PREINTERVIEW);

                $em->persist($registration);
                $em->flush();

                return new JsonModel(array(
                    'message' => 'Candidato convocado com sucesso.',
                ));
            } catch (Exception $ex) {
                return new JsonModel(array(
                    'message' => 'Erro inesperado: ' . $ex->getMessage(),
                ));
            }
        }

        return new JsonModel(array(
            'message' => 'Esta url só pode ser acessada via POST.',
        ));
    }

    public function acceptanceAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            try {
                $data = $request->getPost();
                $em = $this->getEntityManager();
                $registration = $em->getReference('Recruitment\Entity\Registration', $data['id']);

                /**
                 * Atualizar status do candidato
                 */
                $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_INTERVIEW_APPROVED);

                $em->persist($registration);
                $em->flush();

                return new JsonModel(array(
                    'message' => 'Candidato aprovado com sucesso.',
                ));
            } catch (Exception $ex) {
                return new JsonModel(array(
                    'message' => 'Erro inesperado: ' . $ex->getMessage(),
                ));
            }
        }

        return new JsonModel(array(
            'message' => 'Esta url só pode ser acessada via POST.',
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

                $uploadAdapter->addFilter('File\Rename',
                    array(
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

}
