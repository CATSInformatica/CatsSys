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
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Exception;
use Recruitment\Entity\Recruitment;
use Recruitment\Entity\RecruitmentStatus;
use Recruitment\Entity\StudentInterview;
use Recruitment\Form\PreInterviewForm;
use Recruitment\Form\StudentInterviewForm;
use Recruitment\Form\VolunteerInterviewForm;
use Recruitment\Service\AddressService;
use Recruitment\Service\RegistrationStatusService;
use Recruitment\Service\RelativeService;
use RuntimeException;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Manipula informações de candidatos do psa e psv.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class InterviewController extends AbstractEntityActionController
{

    use RelativeService,
        AddressService,
        RegistrationStatusService;

    /**
     * Lista os candidatos de processos seletivos de alunos cuja a entrevista
     * pode ser realizada. Candidatos em um dos status:
     *  - RecruitmentStatus::STATUSTYPE_CALLEDFOR_PREINTERVIEW
     *  - RecruitmentStatus::STATUSTYPE_PREINTERVIEW_COMPLETE
     *  - RecruitmentStatus::STATUSTYPE_INTERVIEWED
     * 
     * @return ViewModel
     */
    public function studentListAction()
    {

        try {
            $em = $this->getEntityManager();
            $recruitment = $em->getRepository('Recruitment\Entity\Recruitment')
                ->findLastClosed(Recruitment::STUDENT_RECRUITMENT_TYPE);

            $candidates = [];

            if (isset($recruitment['recruitmentId'])) {
                $candidates = $em
                    ->getRepository('Recruitment\Entity\Registration')
                    ->findByStatusSimplified($recruitment['recruitmentId'], [
                    RecruitmentStatus::STATUSTYPE_CALLEDFOR_PREINTERVIEW,
                    RecruitmentStatus::STATUSTYPE_PREINTERVIEW_COMPLETE,
                    RecruitmentStatus::STATUSTYPE_INTERVIEWED,
                ]);
            }

            foreach ($candidates as $i => $candidate) {
                $candidateRegistration = $em->find('Recruitment\Entity\Registration', $candidate['registrationId']);
                $candidateInterview = $candidateRegistration->getStudentInterview();
                if ($candidateInterview !== null) {
                    $candidates[$i]['grades'] = [
                        'socioeconomic' => $candidateInterview->getInterviewSocioeconomicGrade(),
                        'vulnerability' => $candidateInterview->getInterviewVulnerabilityGrade(),
                        'student' => $candidateInterview->getInterviewStudentGrade()
                    ];
                } else {
                    $candidates[$i]['grades'] = null;
                }

                $candidates[$i]['statusType'] = RecruitmentStatus::statusTypeToString($candidates[$i]['statusType']);
            }

            return new ViewModel([
                'recruitment' => $recruitment,
                'candidates' => $candidates,
            ]);
        } catch (\Exception $ex) {
            return new ViewModel([
                'recruitment' => null,
                'candidates' => null,
            ]);
        }
    }

    /**
     * ???
     * 
     * @return ViewModel
     */
    public function studentAction()
    {
        $id = $this->params('id', false);
        $request = $this->getRequest();
        if ($id) {
            try {
                $em = $this->getEntityManager();
                $registration = $em->find('Recruitment\Entity\Registration', $id);
                $person = $registration->getPerson();

                $form = new PreInterviewForm($em, array(
                    'person' => array(
                        'relative' => $person->isPersonUnderage(),
                        'relative' => false,
                        'address' => true,
                        'social_media' => true,
                    ),
                    'pre_interview' => $registration->getPreInterview() !== null,
                ));

                $form->bind($registration);

                if ($request->isPost()) {
                    $form->setData($request->getPost());

                    if ($form->isValid()) {
                        $em->merge($registration);
                        $em->flush();
                    }
                }

                return new ViewModel(array(
                    'message' => '',
                    'registration' => $registration,
                    'form' => $form,
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
     * Entrevista para voluntários
     * 
     * @return ViewModel
     * @throws RuntimeException
     */
    public function volunteerAction()
    {
        $id = $this->params('id', false);
        $request = $this->getRequest();
        if ($id) {
            try {
                $em = $this->getEntityManager();
                $registration = $em->find('Recruitment\Entity\Registration', $id);

                $form = new VolunteerInterviewForm($em, array(
                    'interview' => true,
                    'person' => array(
                        'relative' => false,
                        'address' => true,
                        'social_media' => true,
                    ),
                ));

                $form->bind($registration);

                if ($request->isPost()) {

                    $currentStatusType = (int) $registration
                            ->getCurrentRegistrationStatus()
                            ->getRecruitmentStatus()
                            ->getNumericStatusType();

                    if ($currentStatusType == RecruitmentStatus::STATUSTYPE_REGISTERED) {
                        throw new RuntimeException('Este candidato ainda não foi convocado para entrevista '
                        . 'ou aula teste');
                    }

                    $form->setData($request->getPost());

                    if ($form->isValid()) {

                        if ($currentStatusType === RecruitmentStatus::STATUSTYPE_CALLEDFOR_INTERVIEW) {
                            $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_INTERVIEWED);
                        } else if ($currentStatusType === RecruitmentStatus::STATUSTYPE_CALLEDFOR_TESTCLASS) {
                            $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_TESTCLASS_COMPLETE);
                        }

                        $em->merge($registration);
                        $em->flush();
                    }
                }

                return new ViewModel(array(
                    'message' => '',
                    'registration' => $registration,
                    'form' => $form,
                ));
            } catch (Exception $ex) {
                return new ViewModel(array(
                    'message' => 'Erro: ' . $ex->getMessage(),
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
     * Formulário de entrevista para candidatos ao psa.
     * 
     * Faz/edita a entrevista do candidato. Se o candidato estiver em um dos
     * status abaixo ele avançará para o status 
     * RecruitmentStatus::STATUSTYPE_INTERVIEWED:
     * 
     *  - RecruitmentStatus::STATUSTYPE_CALLEDFOR_INTERVIEW
     *  - RecruitmentStatus::STATUSTYPE_CALLEDFOR_PREINTERVIEW
     *  - RecruitmentStatus::STATUSTYPE_PREINTERVIEW_COMPLETE
     * 
     * @todo Fazer o cálculo da nota no critério socioeconômico.
     * 
     * @return ViewModel
     */
    public function studentFormAction()
    {

        try {

            $rid = $this->params()->fromRoute('id', null);
            $em = $this->getEntityManager();
            $registration = $em->find('Recruitment\Entity\Registration', $rid);
            $interview = $registration->getStudentInterview();
            $person = $registration->getPerson();

            $request = $this->getRequest();

            if ($rid) {
                $studentInterviewForm = new StudentInterviewForm($em);

                if ($interview === null) {
                    $interview = new StudentInterview();
                    $registration->setStudentInterview($interview);
                }

                $studentInterviewForm->bind($interview);

                if ($request->isPost()) {

                    $data = $request->getPost();
                    $studentInterviewForm->setData($data);

                    if ($studentInterviewForm->isValid()) {

                        $status = (int) $registration
                                ->getCurrentRegistrationStatus()
                                ->getRecruitmentStatus()
                                ->getNumericStatusType();

                        /* Em qualquer um desses status o candidato avança para
                         * entrevistado. Fora desses status não há modificações
                         * de status, o candidato apenas tem sua entrevista atualizada.
                         */
                        if (in_array($status, [
                                RecruitmentStatus::STATUSTYPE_CALLEDFOR_INTERVIEW,
                                RecruitmentStatus::STATUSTYPE_CALLEDFOR_PREINTERVIEW,
                                RecruitmentStatus::STATUSTYPE_PREINTERVIEW_COMPLETE,
                            ])) {
                            $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_INTERVIEWED);
                        }

                        $iti = $interview->getInterviewTotalIncome();
                        $inofm = $interview->getInterviewNumberOfFamilyMembers();
                        $ims = $interview->getInterviewMaxScholarity();
                        $iht = $interview->getInterviewHomeType();
                        $ihs = $interview->getInterviewHomeSituation();
                        $imp = $interview->getInterviewMaxPosition();

                        $grade = StudentInterview::calculateSocioeconomicGrade($iti, $inofm, $ims, $iht, $ihs, $imp);

                        $interview->setInterviewSocioeconomicGrade($grade);

                        $em->merge($registration);
                        $em->flush();

                        return new ViewModel([
                            'form' => $studentInterviewForm,
                            'message' => 'Entrevista realizada com sucesso.',
                            'person' => $person,
                            'regId' => $rid,
                        ]);
                    }
                }

                return new ViewModel([
                    'form' => $studentInterviewForm,
                    'message' => null,
                    'person' => $person,
                ]);
            }

            return new ViewModel([
                'form' => null,
                'message' => 'Nenhum candidato foi escolhido',
            ]);
        } catch (\Exception $ex) {

            return new ViewModel([
                'form' => null,
                'message' => $ex->getMessage(),
            ]);
        }
    }

    /**
     * Retorna informações do candidato ao processo seletivo de alunos. 
     * 
     * @return \Recruitment\Controller\JsonModel
     */
    public function getStudentInfoAction()
    {

        $registrationId = $this->params('id', false);

        if ($registrationId) {

            try {

                $em = $this->getEntityManager();
                $hydrator = new DoctrineHydrator($em);

                $registration = $em->find('Recruitment\Entity\Registration', $registrationId);

                // informações de inscrição
                $data = $hydrator->extract($registration);
                $data['registrationNumber'] = $registration->getRegistrationNumber();

                // informações pessoais
                $person = $registration->getPerson();
                $data['person'] = $hydrator->extract($person);

                // informações de endereço
                $data['person']['addresses'] = [];
                $addresses = $person->getAddresses();

                foreach ($addresses as $addr) {
                    $data['person']['addresses'][] = $hydrator->extract($addr);
                }

                // informações de parentes (para menores de idade)
                // pega apenas o primeiro
                $relatives = $person->getRelatives();
                $data['person']['relatives'] = [];
                if (count($relatives) > 0) {
                    $data['person']['relatives'][] = $hydrator->extract($relatives[0]);
                }

                //informações da pré-entrevista
                $preInterview = $registration->getPreInterview();
                if ($preInterview !== null) {
                    $data['preInterview'] = $hydrator->extract($preInterview);

                    // informações sobre as despesas da família
                    $data['preInterview']['familyExpenses'] = [];
                    $familyExpenses = $preInterview->getFamilyExpenses();
                    foreach ($familyExpenses as $familyExpense) {
                        $data['preInterview']['familyExpenses'][] = $hydrator->extract($familyExpense);
                    }

                    // informações sobre os bens da família
                    $data['preInterview']['familyGoods'] = [];
                    $familyGoods = $preInterview->getFamilyGoods();
                    foreach ($familyGoods as $familyGood) {
                        $data['preInterview']['familyGoods'][] = $hydrator->extract($familyGood);
                    }

                    // informações sobre a saúde dos familiares
                    $data['preInterview']['familyHealth'] = [];
                    $familyHealth = $preInterview->getFamilyHealth();
                    foreach ($familyHealth as $individualHealth) {
                        $data['preInterview']['familyHealth'][] = $hydrator->extract($individualHealth);
                    }

                    // informações sobre a renda da família
                    $data['preInterview']['familyIncome'] = [];
                    $familyIncome = $preInterview->getFamilyIncome();
                    foreach ($familyIncome as $incomeSource) {
                        $data['preInterview']['familyIncome'][] = $hydrator->extract($incomeSource);
                    }

                    // informações sobre os membros da família
                    $data['preInterview']['familyMembers'] = [];
                    $familyMembers = $preInterview->getFamilyMembers();
                    foreach ($familyMembers as $familyMember) {
                        $data['preInterview']['familyMembers'][] = $hydrator->extract($familyMember);
                    }

                    // informações sobre as propriedades da família
                    $data['preInterview']['familyProperties'] = [];
                    $familyProperties = $preInterview->getFamilyProperties();
                    foreach ($familyProperties as $familyProperty) {
                        $data['preInterview']['familyProperties'][] = $hydrator->extract($familyProperty);
                    }

                    // informações sobre a infraestrutura do local de moradia
                    $data['preInterview']['infrastructureElements'] = [];
                    $infrastructureElements = $preInterview->getInfrastructureElements();
                    foreach ($infrastructureElements as $infrastructureElement) {
                        $data['preInterview']['infrastructureElements'][] = $hydrator->extract($infrastructureElement);
                    }
                }

                // informações sobre a entrevista
                $studentInterview = $registration->getStudentInterview();
                if ($studentInterview !== null) {
                    $data['studentInterview'] = $hydrator->extract($studentInterview);
                }

                return new JsonModel([
                    'info' => $data,
                ]);
            } catch (\Throwable $ex) {
                return new JsonModel([
                    'message' => $ex->getMessage(),
                ]);
            }
        }

        return new JsonModel([]);
    }

    /**
     * Retorna as notas que compõem a nota final do candidato ao processo seletivo de alunos. 
     * 
     * @return JsonModel
     */
    public function getStudentGradesAction()
    {

        $registrationId = $this->params('id', false);

        if ($registrationId) {
            try {
                $em = $this->getEntityManager();

                $registration = $em->find('Recruitment\Entity\Registration', $registrationId);
                $studentInterview = $registration->getStudentInterview();

                $grades = [
                    'socioeconomic' => $studentInterview->getInterviewSocioeconomicGrade(),
                    'vulnerability' => $studentInterview->getInterviewVulnerabilityGrade(),
                    'student' => $studentInterview->getInterviewStudentGrade(),
                ];

                return new JsonModel([
                    'grades' => $grades
                ]);
            } catch (\Throwable $ex) {
                return new JsonModel([
                    'message' => $ex->getMessage(),
                ]);
            }
        }

        return new JsonModel([]);
    }

    /**
     * Utilizado apenas para manter a sessão ativa.
     * 
     * Utilizada nas páginas de entrevista para alunos e análise.
     * 
     * @return JsonModel
     */
    public function keepAliveAction()
    {
        return new JsonModel([
            'alive' => true,
        ]);
    }
}
