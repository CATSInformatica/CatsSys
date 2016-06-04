<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Controller;

use Database\Controller\AbstractEntityActionController;
use Exception;
use Recruitment\Entity\RecruitmentStatus;
use Recruitment\Form\CpfForm;
use Recruitment\Form\PreInterviewForm;
use Recruitment\Service\AddressService;
use Recruitment\Service\RegistrationStatusService;
use Recruitment\Service\RelativeService;
use RuntimeException;
use Zend\File\Transfer\Adapter\Http as HttpAdapter;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Description of PreInterviewController
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class PreInterviewController extends AbstractEntityActionController
{

    const PRE_INTERVIEW_DIR = './data/pre-interview/';
    const PERSONAL_FILE_SUFFIX = '_personal.pdf';
    const INCOME_FILE_SUFFIX = '_income.pdf';
    const EXPENDURE_FILE_SUFFIX = '_expendure.pdf';

    use RelativeService,
        AddressService,
        RegistrationStatusService;

    /**
     * @todo Verificar se a entrevista do candidato já foi feita, se sim, faz o bloqueio da pré-entrevista.
     * 
     * @return ViewModel
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        $form = new CpfForm();

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
                        $status = $registration->getCurrentRegistrationStatus();

                        if ($status->getRecruitmentStatus()->getNumericStatusType() ===
                            RecruitmentStatus::STATUSTYPE_CALLEDFOR_PREINTERVIEW) {

                            $studentContainer = new Container('pre_interview');
                            $studentContainer->offsetSet('regId', $registration->getRegistrationId());

                            return $this->redirect()->toRoute('recruitment/pre-interview',
                                    array(
                                    'action' => 'studentPreInterviewForm'
                            ));
                        }

                        $message = 'Candidato não convocado.';
                    } else {
                        $message = 'Candidato não encontrado.';
                    }
                } catch (\Exception $ex) {
                    $message = 'Erro inesperado. Não foi possível encontrar uma inscrição associada a este cpf.'
                        . $ex->getMessage();
                }
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
     * Formulário de pré-entrevista
     * 
     * Se a sessão de pré-entrevista não foi criada redireciona para o início da pré-entrevista (indexAction)
     * Salva o endereço se necessário, responsável se necessário, endereço do responsável se necessário e, é claro,
     * as informações da pré-entrevista.
     * 
     * @return ViewModel
     */
    public function studentPreInterviewFormAction()
    {
        $studentContainer = new Container('pre_interview');

        // id de inscrição não está na sessão redireciona para o início
        if (!$studentContainer->offsetExists('regId')) {
            return $this->redirect()->toRoute('recruitment/pre-interview',
                    array(
                    'action' => 'index',
            ));
        }

        $rid = $studentContainer->offsetGet('regId');

        try {

            $em = $this->getEntityManager();
            $registration = $em->getReference('Recruitment\Entity\Registration', $rid);

            // se o candidato já respondeu o formulário uma vez avisa que a pré-entrevista já foi cadastrada.
            if ($registration->getPreInterview() !== null) {

                $studentContainer->getManager()->getStorage()->clear('pre_interview');

                return new ViewModel(array(
                    'registration' => $registration,
                    'form' => null,
                    'message' => 'O formulário de pré-entrevista já foi enviado.',
                ));
            }

            $person = $registration->getPerson();

            $options = array(
                'person' => array(
                    'relative' => $person->isPersonUnderage(),
                    'address' => true,
                    'social_media' => false,
                ),
                'pre_interview' => true,
            );

            $form = new PreInterviewForm($em, $options);
            $form->bind($registration);
//            if ($request->isPost()) {
//                $form->setData($request->getPost());
//                if ($form->isValid()) {
//
//                    // manage duplicates in address, and relatives
//                    $this->adjustAddresses($person);
//                    $this->adjustRelatives($person);
//
//                    $preInterview = $registration->getPreInterview();
//                    $preInterview
//                        ->setPreInterviewPersonalInfo($rid . self::PERSONAL_FILE_SUFFIX)
//                        ->setPreInterviewIncomeProof($rid . self::INCOME_FILE_SUFFIX)
//                        ->setPreInterviewExpenseReceipt($rid . self::EXPENDURE_FILE_SUFFIX);
//
//                    $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_PREINTERVIEW_COMPLETE);
//
//                    $em->persist($registration);
//                    $em->flush();
//                    $studentContainer->getManager()->getStorage()->clear('pre_interview');
//
//                    return new ViewModel(array(
//                        'registration' => null,
//                        'form' => null,
//                        'message' => 'Pré-entrevista concluída com com sucesso.',
//                    ));
//                }
//            }
        } catch (Exception $ex) {
            return new ViewModel(array(
                'registration' => null,
                'form' => null,
                'message' => 'Erro inesperado. Por favor, entre em contato com o administrador do sistema.',
                'message' => $ex->getMessage(),
            ));
        }

        return new ViewModel(array(
            'registration' => $registration,
            'form' => $form,
            'message' => '',
        ));
    }

}
