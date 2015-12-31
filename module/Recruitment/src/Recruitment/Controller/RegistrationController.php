<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Controller;

use Database\Service\EntityManagerService;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Recruitment\Entity\Person;
use Recruitment\Entity\Recruitment;
use Recruitment\Entity\Registration;
use Recruitment\Form\StudentRegistrationFilter;
use Recruitment\Form\StudentRegistrationForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\File\Transfer\Adapter\Http as HttpAdapter;

/**
 * 
 * @todo Fazer as actions de convocação e aceitação
 * 
 * 
 * Description of RegistrationController
 * 
 * @author marcio
 */
class RegistrationController extends AbstractActionController
{

    use EntityManagerService;

    /**
     * 
     * @todo criar índice no campo recruitmentType da entidade Recruitment
     * 
     * Exibe todas as inscrições do processo seletivo escolhido (inicialmente exibe o último 
     * processo seletivo vigente).
     * 
     * @return ViewModel
     */
    public function showStudentRegistrationsAction()
    {
        try {

            $em = $this->getEntityManager();
            $recruitments = $em->getRepository('Recruitment\Entity\Recruitment')->findBy(
                    array('recruitmentType' => Recruitment::STUDENT_RECRUITMENT_TYPE), array('recruitmentId' => 'DESC')
            );


            $this->layout()->toolbar = array(
                'menu' => array(
                    array(
                        'url' => '/recruitment/registration/studentProfile/$id',
                        'title' => 'Perfil do Candidato',
                        'description' => 'Analizar Perfil do Candidato',
                        'class' => 'fa fa-file-text-o bg-blue',
                        'target' => '_blank',
                        'fntype' => 'selectedHttpClick',
                    ),
                ),
            );


            return new ViewModel(array(
                'message' => null,
                'recruitments' => $recruitments,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema.',
                'recruitments' => null,
            ));
        }
    }

    /**
     * 
     * @todo redirecionar para a página que gera o comprovante de inscrição e envia o email.
     * 
     * Exibe o formulário de inscrição e faz a validação do envio
     * 
     * Uma nova inscrição poderá ser feita/será aceita se, e somente se, a seguintes condições forem satisfeitas
     *  - Existe um processo seletivo aberto \Recruitment\Entity\Recruitment
     *  - A pessoa que está se inscrevendo ainda não fez a inscrição no processo seletivo vigente
     * 
     * Ao fazer a inscrição, caso a pessoa já possua cadastro, o dados pessoais serão atualizados
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
    public function studentRegistrationAction()
    {

        try {
            $em = $this->getEntityManager();

            // Busca por um processo seletivo aberto
            $recruitment = $em->getRepository('Recruitment\Entity\Recruitment')
                    ->findByTypeAndBetweenBeginAndEndDates(Recruitment::STUDENT_RECRUITMENT_TYPE, new DateTime('now'));

            if ($recruitment === null) {
                return new ViewModel(array(
                    'message' => 'Não existe nenhum processo seletivo de alunos vigente no momento.',
                    'form' => null,
                ));
            }
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro ao buscar processos seletivos vigentes.',
                'form' => null,
            ));
        }

        // Se existe um processo seletivo de alunos vigente ....

        $request = $this->getRequest();
        $message = null;
        $form = new StudentRegistrationForm($request->getBaseUrl() . '/recruitment/captcha/generate', 'Inscrição');

        // Se a requisição for post (o formulário foi preenchido e envidado para o servidor)
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setInputFilter(new StudentRegistrationFilter());
            $form->setData($data);

            // Se o formulário de inscrição foi preenchido corretamente
            if ($form->isValid()) {
                // recupera os dados tratados pelo filtro do formulário de inscrição
                $data = $form->getData();
                try {

                    // verifica se a pessoa já está cadastrada, se não estiver cria um novo cadastro.

                    $person = $em->getRepository('Recruitment\Entity\Person')->findOneBy(array(
                        'personCpf' => $data['person_cpf'],
                    ));

                    if ($person == null) {
                        echo 'hello?';
                        $person = new Person();
                        $personDefaultPhoto = '';
                        if ($data['person_gender'] == Person::GENDER_M) {
                            $personDefaultPhoto = 'default-male-profile.png';
                        } else if ($data['person_gender'] == Person::GENDER_F) {
                            $personDefaultPhoto = 'default-female-profile.png';
                        }

                        $person->setPersonPhoto($personDefaultPhoto);
                    }

                    echo $data['person_birthday'];
                    // atualiza ou insere pela primeira vez os dados pessoais de cadastro
                    $person->setPersonFirstName($data['person_firstname'])
                            ->setPersonLastName($data['person_lastname'])
                            ->setPersonGender($data['person_gender'])
                            ->setPersonBirthday(new DateTime($data['person_birthday']))
                            ->setPersonRg($data['person_rg'])
                            ->setPersonCpf($data['person_cpf'])
                            ->setPersonEmail($data['person_email'])
                            ->setPersonPhone($data['person_phone']);

                    // cria uma nova inscrição 
                    $registration = new Registration();

                    // concatena as opções de como soube do processo seletivo
                    foreach ($data['registration_know_about'] as $rka) {
                        $registration->addRegistrationKnowAbout($rka);
                    }

                    // atribui a qual processo seletivo e pessoa a inscrição pertence
                    $registration->setRecruitment($recruitment);
                    $registration->setPerson($person);

                    $em->persist($person);
                    $em->persist($registration);

                    $em->flush();

                    // redirecionar para a página que gera o comprovante de inscrição e envia o email.
                    $message = 'Inscrição efetuada com sucesso.';
                    $form = null;
                } catch (Exception $ex) {

                    if ($ex instanceof UniqueConstraintViolationException) {
                        $message = 'Não é possível fazer mais de uma inscrição em um mesmo processo seletivo.';
                        $form = null;
                    } else {
                        $message = 'Erro inesperado.Por favor, tente novamente ou'
                                . ' entre em contato com o administrador do sistema.';
                    }
                }
            }
        }

        return new ViewModel(array(
            'message' => $message,
            'form' => $form,
        ));
    }

    /**
     * Busca todos as inscrições pro processo seletivo $rid. 
     * Esta action é acessada via ajax pelo DataTable
     * 
     * @return JsonModel
     */
    public function getStudentRegistrationsAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {

            $rid = $request->getPost()['rid'];

            $resultSet = ['data' => [
            ]];

            try {
                $em = $this->getEntityManager();

                $regs = $em->getRepository('Recruitment\Entity\Registration')->findBy(array(
                    'recruitment' => $rid
                ));

                foreach ($regs as $r) {
                    $person = $r->getPerson();
                    $resultSet['data'][] = array(
                        'DT_RowClass' => 'cats-row',
                        'DT_RowAttr' => [
                            'data-id' => $r->getRegistrationId()
                        ],
                        $r->getRegistrationNumber(),
                        $r->getRegistrationDate()->format('d/m/Y H:i:s'),
                        $person->getPersonFirstName() . ' ' . $person->getPersonLastName(),
                        $person->getPersonCpf(),
                        $person->getPersonRg(),
                        $person->getPersonEmail(),
                    );
                }
            } catch (Exception $ex) {
                
            }

            return new JsonModel($resultSet);
        }
    }

    /**
     * 
     * @return ViewModel Retorna a inscrição do candidato que possui a inscrição com id = $id
     */
    public function studentProfileAction()
    {
        $id = $this->params('id', false);

        if ($id) {
            try {
                $em = $this->getEntityManager();
                $registration = $em->getRepository('Recruitment\Entity\Registration')->findOneBy(array(
                    'registrationId' => $id
                ));


                $this->layout()->toolbar = array(
                    'menu' => array(
                        array(
                            'url' => '/recruitment/registration/confirmation/' . $id,
                            'id' => 'fn-confirmation',
                            'title' => 'Confirmar',
                            'description' => 'Confirmar/Desconfirmar a inscrição do candidato.',
                            'class' => 'fa fa-check bg-red',
                            'fntype' => 'ajaxClick',
                        ),
                        array(
                            'url' => '/recruitment/registration/convocation/' . $id,
                            'id' => 'fn-convocation',
                            'title' => 'Convocar',
                            'description' => 'Convocar/Desconvocar o candidato para a pré-entrevista.',
                            'class' => 'fa fa-users bg-blue fn-ajaxClick',
                            'fntype' => 'ajaxClick',
                        ),
                        array(
                            'url' => '/recruitment/registration/acceptance/' . $id,
                            'title' => 'Aprovar Candidato',
                            'id' => 'fn-acceptance',
                            'description' => 'Aprova/remove aprovação do candidato. A aprovação é condição suficiente para a matrícula.',
                            'class' => 'fa fa-graduation-cap bg-yellow',
                            'fntype' => 'ajaxClick',
                        ),
                    ),
                );


                return new ViewModel(array(
                    'message' => '',
                    'registration' => $registration
                ));
            } catch (Exception $ex) {
                return new ViewModel(array(
                    'message' => 'Não foi possível encontrar o registro do candidato: ' . $ex->getMessage(),
                    'registration' => null
                ));
            }
        }

        return new ViewModel(array(
            'message' => 'nenhum candidato foi especificado.',
            'registration' => null
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
                $photo = './data/profile/' . $person->getPersonPhoto();

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
     * @throws \RuntimeException
     */
    public function changePhotoAction()
    {
        $id = $this->params('id', false);
        $request = $this->getRequest();
        if ($id && $request->isPost()) {
            $file = $request->getFiles()->profilePhoto;
            try {

                $em = $this->getEntityManager();
                $registration = $em->getRepository('Recruitment\Entity\Registration')->findOneBy(array(
                    'registrationId' => $id
                ));

                $targetDir = './data/profile/';
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
                        throw new \RuntimeException('Formato inválido, apenas imagens no '
                        . 'formato jpg e png são aceitas.');
                }

                $targetFile = $targetDir . $targetName;

                $uploadAdapter = new HttpAdapter();

                $uploadAdapter->addFilter('File\Rename', array(
                    'target' => $targetFile,
                    'overwrite' => true
                ));

                $uploadAdapter->setDestination($targetDir);

                if (!$uploadAdapter->receive($file['name'])) {
                    $messages = implode('\n', $uploadAdapter->getMessages());
                    throw new \RuntimeException($messages);
                }

                $person = $registration->getPerson();
                $person->setPersonPhoto($targetName);

                $em->persist($person);
                $em->flush();

                return new JsonModel(array(
                    'message' => 'Imagem enviada com sucesso.',
                    'file' => '/recruitment/registration/photo/' . $id,
                ));
            } catch (Exception $ex) {
                return new ViewModel(array(
                    'message' => $ex->getMessage(),
                    'file' => null,
                ));
            }
        }
    }

    /**
     * Faz a confirmação/Desconfirma a inscrição de um candidato
     * 
     * @return JsonModel essa action é acionada via ajax
     */
    public function confirmationAction()
    {
        $id = $this->params('id', false);

        if ($id) {

            try {
                $em = $this->getEntityManager();

                $registration = $em->getRepository('Recruitment\Entity\Registration')->findOneBy(array(
                    'registrationId' => $id
                ));

                if ($registration->getRegistrationConfirmationDate() !== null) {
                    $registration->setRegistrationConfirmationDate(null);
                    $msg = 'Confirmação revogada com sucesso.';
                } else {
                    $registration->setRegistrationConfirmationDate(new \DateTime('now'));
                    $msg = 'Candidato confirmado com sucesso.';
                }

                $em->persist($registration);
                $em->flush();
            } catch (Exception $ex) {
                $msg = 'Erro ao tentar alterar a confirmação: ' . $ex->getMessage();
            }

            return new JsonModel(array(
                'message' => $msg,
            ));
        }

        return new JsonModel(array(
            'message' => 'Nenhum identificador especificado.',
        ));
    }

    /**
     * Faz a convocação/Desconvoca um candidato para a pré-entrevista
     * 
     * @return JsonModel essa action é acionada via ajax
     */
    public function convocationAction()
    {
        $id = $this->params('id', false);

        if ($id) {

            try {
                $em = $this->getEntityManager();

                $registration = $em->getRepository('Recruitment\Entity\Registration')->findOneBy(array(
                    'registrationId' => $id
                ));

                if ($registration->getRegistrationConvocationDate() !== null) {
                    $registration->setRegistrationConvocationDate(null);
                    $msg = 'Convocação revogada com sucesso.';
                } else {
                    $registration->setRegistrationConvocationDate(new \DateTime('now'));
                    $msg = 'Candidato convocado com sucesso.';
                }

                $em->persist($registration);
                $em->flush();
            } catch (Exception $ex) {
                $msg = 'Erro ao tentar alterar a convocação: ' . $ex->getMessage();
            }

            return new JsonModel(array(
                'message' => $msg,
            ));
        }

        return new JsonModel(array(
            'message' => 'Nenhum identificador especificado.',
        ));
    }

    /**
     * Faz a aprovação/Desprovação de um candidato
     * 
     * @return JsonModel essa action é acionada via ajax
     */
    public function acceptanceAction()
    {
        $id = $this->params('id', false);

        if ($id) {

            try {
                $em = $this->getEntityManager();

                $registration = $em->getRepository('Recruitment\Entity\Registration')->findOneBy(array(
                    'registrationId' => $id
                ));

                if ($registration->getRegistrationAcceptanceDate() !== null) {
                    $registration->setRegistrationAcceptanceDate(null);
                    $msg = 'Aprovação revogada com sucesso.';
                } else {
                    $registration->setRegistrationAcceptanceDate(new \DateTime('now'));
                    $msg = 'Candidato aprovado com sucesso.';
                }

                $em->persist($registration);
                $em->flush();
            } catch (Exception $ex) {
                $msg = 'Erro ao tentar alterar a aprovação: ' . $ex->getMessage();
            }

            return new JsonModel(array(
                'message' => $msg,
            ));
        }

        return new JsonModel(array(
            'message' => 'Nenhum identificador especificado.',
        ));
    }

    /**
     * Retorna os candidatos do processo seletivo $id aptos a realizarem a matrícula
     * 
     * @return JsonModel
     */
    public function getAcceptedStudentsAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {

            $rid = $request->getPost()['rid'];

            $resultSet = ['data' => [
            ]];

            try {

                $em = $this->getEntityManager();
                $regs = $em->getRepository('Recruitment\Entity\Registration')
                        ->findByAccepted($rid);

                foreach ($regs as $r) {
                    $person = $r->getPerson();
                    $resultSet['data'][] = array(
                        'DT_RowClass' => 'cats-row',
                        'DT_RowAttr' => [
                            'data-id' => $r->getRegistrationId()
                        ],
                        $r->getRegistrationNumber(),
                        $r->getRegistrationDate()->format('d/m/Y H:i:s'),
                        $person->getPersonFirstName() . ' ' . $person->getPersonLastName(),
                        $person->getPersonCpf(),
                        $person->getPersonRg(),
                        $person->getPersonEmail(),
                    );
                }
            } catch (Exception $ex) {
                return new JsonModel([$ex->getMessage()]);
            }

            return new JsonModel($resultSet);
        }

        return new JsonModel([]);
    }

}
