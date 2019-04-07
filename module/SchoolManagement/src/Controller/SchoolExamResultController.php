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

namespace SchoolManagement\Controller;

use Database\Controller\AbstractEntityActionController;
use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Recruitment\Entity\Recruitment;
use SchoolManagement\Entity\ExamApplication;
use SchoolManagement\Entity\ExamApplicationResult;
use SchoolManagement\Entity\ExamResult;
use Zend\Json\Json;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Correção de simulados a partir das respostas dos alunos e do gabarito oficial
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class SchoolExamResultController extends AbstractEntityActionController
{

    /**
     * Faz os cálculos a partir das planilhas de respostas dos alunos e do
     * gabarito oficial. Esta função está obsoleta
     *
     *
     * @return ViewModel
     */
    public function previewAction()
    {
        try {

            $em = $this->getEntityManager();

            $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')
                ->findByEndDateGratherThan(new DateTime('now'));

            return new ViewModel([
                'classes' => $classes,
                'message' => null,
            ]);
        } catch (Exception $ex) {
            return new ViewModel([
                'message' => $ex->getMessage(),
                'classes' => null,
            ]);
        }
    }

    /**
     * Exibe a interface para persistência de respostas de alunos em aplicações de prova
     *
     * @return ViewModel
     */
    public function uploadAnswersByClassAction()
    {

        try {

            $em = $this->getEntityManager();

            $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')
                ->findByEndDateGratherThan(new DateTime());

            $applications = $em->getRepository('SchoolManagement\Entity\ExamApplication')
                ->findBy([
                'status' => ExamApplication::EXAM_APP_CREATED
                ], [
                'examApplicationId' => 'desc',
            ]);

            return new ViewModel([
                'classes' => $classes,
                'apps' => $applications,
                'message' => null,
            ]);
        } catch (Exception $ex) {
            return new ViewModel([
                'message' => $ex->getMessage(),
                'classes' => null,
            ]);
        }
    }

    /**
     * Salva as respostas dos candidatos de vestibulinhos.
     *
     * @return \SchoolManagement\Controller\JsonModel
     */
    public function uploadAnswersByStdRecruitmentAction()
    {
        try {

            $em = $this->getEntityManager();

            $recruitments = $em->getRepository('Recruitment\Entity\Recruitment')->findBy([
                'recruitmentType' => Recruitment::STUDENT_RECRUITMENT_TYPE
                ], [
                'recruitmentId' => 'desc'
            ]);

            $applications = $em->getRepository('SchoolManagement\Entity\ExamApplication')
                ->findBy([
                'status' => ExamApplication::EXAM_APP_CREATED
                ], [
                'examApplicationId' => 'desc',
            ]);

            return new ViewModel([
                'recruitments' => $recruitments,
                'apps' => $applications,
                'message' => null,
            ]);
        } catch (Exception $ex) {
            return new ViewModel([
                'message' => $ex->getMessage(),
                'classes' => null,
            ]);
        }
    }

    public function saveAnswersAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost();
            try {

                $em = $this->getEntityManager();
                $exam = $em->getReference('SchoolManagement\Entity\Exam', $data['exam']);

                foreach ($data['individuals'] as $c) {

                    $registration = $em->getReference('Recruitment\Entity\Registration', $c['registration']);
                    $encodedAnswers = Json::encode([
                            'answers' => $c['answers'],
                            'parallels' => $c['parallels'],
                    ]);

                    $answerEntity = $em->getRepository('SchoolManagement\Entity\ExamResult')->findOneBy([
                        'registration' => $c['registration'],
                        'exam' => $data['exam'],
                    ]);

                    if ($answerEntity === null) {
                        $answerEntity = new ExamResult();
                        $answerEntity
                            ->setExam($exam)
                            ->setRegistration($registration);
                    }

                    $answerEntity->setAnswers($encodedAnswers);
                    $em->persist($answerEntity);
                }

                $em->flush();

                return new JsonModel([
                    'message' => 'Respostas salvas com sucesso',
                    'callback' => $data,
                ]);
            } catch (Exception $ex) {
                return new JsonModel([
                    'message' => $ex->getMessage(),
                ]);
            }
        }
    }

    public function getAnswersAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost();
            if (!empty($data)) {

                $em = $this->getEntityManager();
                $examResult = $em->getRepository('SchoolManagement\Entity\ExamResult')->findOneBy([
                    'registration' => $data['registration'],
                    'exam' => $data['exam']
                ]);

                $answers = null;
                if ($examResult) {
                    $answers = Json::decode($examResult->getAnswers());
                }

                return new JsonModel([
                    'examAnswers' => $answers,
                ]);
            }
        }
    }

    /**
     * Lista todas as aplicações de prova para escolha.
     *
     * @return ViewModel
     */
    public function answersTemplateAction()
    {

        $em = $this->getEntityManager();

        $applications = $em->getRepository('SchoolManagement\Entity\ExamApplication')
            ->findBy([
            'status' => ExamApplication::EXAM_APP_CREATED
            ], [
            'examApplicationId' => 'desc',
        ]);

        return new ViewModel([
            'apps' => $applications,
        ]);
    }

    /**
     * Busca todas as respostas da prova $id;
     * @throws Exception
     */
    public function getTemplateAnswersAction()
    {
        $id = $this->params('id', false);

        if ($id) {
            try {

                $em = $this->getEntityManager();

                $exam = $em->find('SchoolManagement\Entity\Exam', $id);
                $config = Json::decode($exam
                            ->getContent()
                            ->getConfig(), Json::TYPE_ARRAY);
                $ansJson = $exam->getAnswers();

                $answers = !empty($ansJson) ? Json::decode($exam->getAnswers()) : null;

                foreach ($config['groups'] as &$group) {
                    foreach ($group['subgroups'] as &$subgroup) {
                        if (key_exists('id', $subgroup)) {
                            $this->fillAnswers($em, $subgroup);
                        } else {
                            foreach ($subgroup as &$psubgroup) {
                                $this->fillAnswers($em, $psubgroup);
                            }
                        }
                    }
                }

                return new JsonModel([
                    'answers' => $answers,
                    'config' => $config
                ]);
            } catch (\Exception $ex) {
                $this->getResponse()->setStatusCode(400);
            }
        }

        $this->getResponse()->setStatusCode(400);
    }

    public function saveTemplateAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array) $request->getPost();

            if (!empty($data['templates'])) {

                $message = "";

                $em = $this->getEntityManager();

                foreach ($data['templates'] as $exam) {

                    if (empty($exam['template'])) {
                        $message .= "Prova " . $exam['id'] . ": Gabarito vazio.<br>";
                        continue;
                    }

                    $message .= "Prova " . $exam['id'] . " Gabarito salvo com sucesso<br>";

                    $e = $em->getReference('SchoolManagement\Entity\Exam', $exam['id']);
                    $e->setAnswers(Json::encode($exam['template']));
                    $em->merge($e);
                }

                $em->flush();

                return new JsonModel([
                    'message' => $message,
                ]);
            }
            return new JsonModel([
                'message' => 'Para salvar gabaritos é necessário selecionar ao menos uma prova',
            ]);
        }

        $this->getResponse()->setStatusCode(403);
    }

    public function resultAction()
    {
        try {


            $em = $this->getEntityManager();

            $applications = $em->getRepository('SchoolManagement\Entity\ExamApplication')
                ->findBy([
                'status' => ExamApplication::EXAM_APP_CREATED
                ], [
                'examApplicationId' => 'desc',
            ]);

            $lastRecruitment = $em->getRepository('Recruitment\Entity\Recruitment')->findNotEndedByTypeAsArray(Recruitment::STUDENT_RECRUITMENT_TYPE);

            $rec = [
                'id' => $lastRecruitment['recruitmentId'],
                'desc' => Recruitment::formatName($lastRecruitment['recruitmentNumber'], $lastRecruitment['recruitmentYear'])
            ];

            return new ViewModel([
                'apps' => $applications,
                'rec' => $rec
            ]);
        } catch (\Thrownable $ex) {
            die($ex->getMessage());
        }
    }

    private function fillAnswers(ObjectManager $em, &$subgroup)
    {
        foreach ($subgroup['questions'] as &$question) {
            $question['answer'] = $em
                ->find('SchoolManagement\Entity\ExamQuestion', $question['id'])
                ->getConvertedCorrectAnswer();
        }
    }

    public function getAllAnswersAction()
    {
        try {

            $examId = $this->params('id', false);

            if ($examId) {

                $em = $this->getEntityManager();
                $examAnswers = $em->getRepository('SchoolManagement\Entity\ExamResult')->findBy([
                    'exam' => $examId
                ]);

                $answers = [];

                foreach ($examAnswers as $ea) {
                    $registration = $ea->getRegistration();
                    $person = $registration->getPerson();
                    $status = $registration->getCurrentRegistrationStatus()->getRecruitmentStatus()->getStatusType();
                    $answers[] = [
                        'registrationId' => $registration->getRegistrationId(),
                        'registrationNumber' => $registration->getRegistrationNumber(),
                        'currentStatus' => $status,
                        'enrollment' => null,
                        'answers' => Json::decode($ea->getAnswers()),
                        'birth' => $person->getPersonBirthday(),
                    ];
                }

                return new JsonModel([
                    'examId' => $examId,
                    'answers' => $answers
                ]);
            }
        } catch (\Exception $ex) {

        }


        return [];
    }

    public function saveResultAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost();

            if (!empty($data['results']) && !empty($data['application']) && !empty($data['recruitment'])) {

                try {

                    $em = $this->getEntityManager();

                    $app = $em->getReference('SchoolManagement\Entity\ExamApplication', $data['application']);

                    $rec = $em->getReference('Recruitment\Entity\Recruitment', $data['recruitment']);
                    $app->setRecruitment($rec);


                    foreach ($data['results'] as $result) {

                        $appResult = $em->getRepository('SchoolManagement\Entity\ExamApplicationResult')->findOneBy([
                            'application' => $data['application'],
                            'registration' => $result['registrationId']
                        ]);

                        if ($appResult === null) {

                            $reg = $em->getReference('Recruitment\Entity\Registration', $result['registrationId']);
                            $appResult = new ExamApplicationResult();

                            $appResult
                                ->setApplication($app)
                                ->setRegistration($reg)
                                ->setResult(Json::encode([
                                        'partialResult' => $result['partialResult'],
                                        'result' => $result['result'],
                                        'groups' => $result['groups'],
                                        'position' => $result['position']
                            ]));
                        } else {
                            $appResult->setResult(Json::encode([
                                    'partialResult' => $result['partialResult'],
                                    'result' => $result['result'],
                                    'groups' => $result['groups'],
                                    'position' => $result['position']
                            ]));
                        }

                        $em->persist($appResult);
                    }

                    $em->persist($app);

                    $em->flush();

                    return new JsonModel([
                        'message' => 'Resultado salvo com sucesso'
                    ]);
                } catch (\Exception $ex) {
                    return new JsonModel([
                        'message' => 'Erro: ' . $ex->getMessage()
                    ]);
                }
            }
            return new JsonModel([
                'message' => 'Requisição sem dados',
            ]);
        }
    }

    public function getResultAction()
    {
        $id = $this->params('id', false);

        if ($id) {

            $em = $this->getEntityManager();

            $appResults = $em->getRepository('SchoolManagement\Entity\ExamApplicationResult')->findBy([
                'application' => $id,
            ]);

            $results = [];
            foreach ($appResults as $res) {

                $reg = $res->getRegistration();
                $r = Json::decode($res->getResult(), Json::TYPE_ARRAY);
                $status = $reg->getCurrentRegistrationStatus()->getRecruitmentStatus()->getStatusType();

                $results[] = [
                    'registrationId' => $reg->getRegistrationId(),
                    'registrationNumber' => $reg->getRegistrationNumber(),
                    'partialResult' => $r['partialResult'],
                    'currentStatus' => $status,
                    'result' => $r['result'],
                    'groups' => $r['groups'],
                    'position' => $r['position']
                ];
            }

            return new JsonModel($results);
        }
    }
}
