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
use Recruitment\Entity\Recruitment;
use Recruitment\Entity\Registration;
use Recruitment\Form\RegistrationForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\File\Transfer\Adapter\Http as HttpAdapter;

/**
 * Description of RegistrationController
 * 
 * @author marcio
 */
class RegistrationController extends AbstractActionController
{

    const PROFILE_DIR = './data/profile/';

    use EntityManagerService;

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
            $recruitments = $em->getRepository('Recruitment\Entity\Recruitment')->findBy(
                array('recruitmentType' => Recruitment::STUDENT_RECRUITMENT_TYPE), array('recruitmentId' => 'DESC')
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
        $form = new RegistrationForm($em);

        $request = $this->getRequest();
        // Se a requisição for post (o formulário foi preenchido e enviado para o servidor)
        if ($request->isPost()) {

            $registration = new Registration();
            $form->bind($registration);
            $form->setData($request->getPost());

            // Se o formulário de inscrição foi preenchido corretamente
            if ($form->isValid()) {

                try {
                    // verifica se a pessoa já está cadastrada, se não estiver cria um novo cadastro.

                    $newPerson = $registration->getPerson();

                    $person = $em->getRepository('Recruitment\Entity\Person')->findOneBy(array(
                        'personCpf' => $newPerson->getPersonCpf(),
                    ));

                    // Se a pessoa já possui cadastro atualiza alguns dos dados
                    if ($person !== null) {
                        $person->setPersonPhone($newPerson->getPersonPhone());
                        $person->setPersonEmail($newPerson->getPersonEmail());
                        $person->setPersonRg($newPerson->getPersonRg());
                        $registration->setPerson($person);
                    } else {
                        // se não possui define a imagem padrão do perfil
                        $newPerson->setPersonPhoto();
                    }

                    // atribui a qual processo seletivo a inscrição pertence
                    $registration->setRecruitment($recruitment);

                    // salva no banco
                    $em->persist($registration->getPerson());
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
                            . ' entre em contato com o administrador do sistema: ';
                    }
                }
            }
        } else {
            $message = '';
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
                    'recruitment' => $rid,
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
                $registration = $em->find('Recruitment\Entity\Registration', $id);

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
                        throw new \RuntimeException('Formato inválido, apenas imagens no '
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

                $registration = $em->find('Recruitment\Entity\Registration', $id);

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

                $registration = $em->find('Recruitment\Entity\Registration', $id);

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

                $registration = $em->find('Recruitment\Entity\Registration', $id);

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
