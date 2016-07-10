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

namespace Recruitment\Controller;

use Database\Controller\AbstractEntityActionController;
use Exception;
use Recruitment\Entity\RecruitmentStatus;
use Recruitment\Form\CpfForm;
use Recruitment\Form\PreInterviewForm;
use Recruitment\Service\AddressService;
use Recruitment\Service\RegistrationStatusService;
use Recruitment\Service\RelativeService;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Responsável pela manipulação de  informações de inscrição e pré-entrevista.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class PreInterviewController extends AbstractEntityActionController
{

    use RelativeService,
        AddressService,
        RegistrationStatusService;

    /**
     * Obtém o cpf e verifica se o candidato poderá acessar o formulário 
     * de pré-entrevista.
     * 
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->layout('application-clean/layout');
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

                        $status = (int) $registration
                                ->getCurrentRegistrationStatus()
                                ->getRecruitmentStatus()
                                ->getNumericStatusType();

                        // se o status do candidato for um desses três 
                        // ele poderá acessar o formulário de pré-entrevista
                        // para preencher ou editar.
                        if (in_array($status, [
                                RecruitmentStatus::STATUSTYPE_CALLEDFOR_PREINTERVIEW,
                                RecruitmentStatus::STATUSTYPE_CALLEDFOR_INTERVIEW,
                                RecruitmentStatus::STATUSTYPE_PREINTERVIEW_COMPLETE,
                            ])) {

                            $studentContainer = new Container('pre_interview');
                            $studentContainer->offsetSet('regId', $registration->getRegistrationId());

                            return $this->redirect()->toRoute('recruitment/pre-interview', array(
                                    'action' => 'studentPreInterviewForm'
                            ));
                        }

                        $message = 'Candidato não convocado.';
                    } else {
                        $message = 'Candidato não encontrado.';
                    }
                } catch (Exception $ex) {
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
        $this->layout('application-clean/layout');
        $studentContainer = new Container('pre_interview');

        // id de inscrição não está na sessão redireciona para o início
        if (!$studentContainer->offsetExists('regId')) {
            return $this->redirect()->toRoute('recruitment/pre-interview', array(
                    'action' => 'index',
            ));
        }

        $rid = $studentContainer->offsetGet('regId');

        try {

            $request = $this->getRequest();

            $em = $this->getEntityManager();
            $registration = $em->getReference('Recruitment\Entity\Registration', $rid);

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
            if ($request->isPost()) {
                $form->setData($request->getPost());

                if ($form->isValid()) {

                    // gestão de duplicação de endereço e parentes
                    $this->adjustAddresses($person);
                    $this->adjustRelatives($person);

                    $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_PREINTERVIEW_COMPLETE);

                    $em->persist($registration);
                    $em->flush();
                    $studentContainer->getManager()->getStorage()->clear('pre_interview');

                    return new ViewModel(array(
                        'registration' => null,
                        'form' => null,
                        'message' => 'Pré-entrevista concluída com com sucesso. '
                        . 'O formulário de pré-entrevista continuará disponível para futuras edições até a '
                        . 'conclusão de sua entrevista.',
                    ));
                } else {
                    return new ViewModel(array(
                        'registration' => $registration,
                        'form' => $form,
                        'message' => 'Existe(m) algum(ns) campo(s) não preenchido(s).',
                    ));
                }
            }
        } catch (Exception $ex) {
            return new ViewModel(array(
                'registration' => null,
                'form' => null,
                'message' => 'Erro inesperado. Por favor, entre em contato com o administrador do sistema.',
//                'message' => $ex->getMessage(),
            ));
        }

        return new ViewModel([
            'registration' => $registration,
            'form' => $form,
            'message' => '',
        ]);
    }

    /**
     * Como o formulário é grande, para que a sessão não expire durante o preenchimento
     * a página do formulário de tempos em tempos manda requisições para manter a sessão ativa.
     */
    public function keepAliveAction()
    {

        $studentContainer = new Container('pre_interview');

        $alive = true;
        
        // id de inscrição não está na sessão redireciona para o início
        if (!$studentContainer->offsetExists('regId')) {
            $alive = false;
        }

        return new JsonModel([
            'alive' => $alive,
        ]);
    }
}
