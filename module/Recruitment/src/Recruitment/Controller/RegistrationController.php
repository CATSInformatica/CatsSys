<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Controller;

use Database\Service\EntityManagerService;
use DateTime;
use Exception;
use Recruitment\Entity\Person;
use Recruitment\Entity\Recruitment;
use Recruitment\Entity\Registration;
use Recruitment\Form\RegistrationFilter;
use Recruitment\Form\StudentRegistrationForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

/**
 * Description of RegistrationController
 *
 * @author marcio
 */
class RegistrationController extends AbstractActionController
{

    use EntityManagerService;

    public function indexAction()
    {
        return new ViewModel();
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
            $form->setInputFilter(new RegistrationFilter());
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
                    }

                    echo $data['person_birthday'];
                    // atualiza ou insere pela primeira vez os dados pessoais de cadastro
                    $person->setPersonFistName($data['person_firstname'])
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

}
