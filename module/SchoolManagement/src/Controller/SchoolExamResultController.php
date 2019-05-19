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
use Recruitment\Entity\Registration;
use SchoolManagement\Entity\Enrollment;
use SchoolManagement\Entity\StudentClass;

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
            $regOrEnrollment = 'registration';
            $referecedEntityClass = Registration::class;

            try {

                $em = $this->getEntityManager();
                $exam = $em->getReference('SchoolManagement\Entity\Exam', $data['exam']);

                if ($data['isStudent']) {
                    $regOrEnrollment = 'enrollment';
                    $referecedEntityClass = Enrollment::class;
                }

                foreach ($data['individuals'] as $c) {
                    $registrationOrEnrolment = $em->getReference($referecedEntityClass, $c['registrationOrEnrollment']);
                    $encodedAnswers = Json::encode([
                        'answers' => $c['answers'],
                        'parallels' => $c['parallels'],
                        'filename' => $c['filename']
                    ]);

                    $answerEntity = $em->getRepository('SchoolManagement\Entity\ExamResult')->findOneBy([
                        'exam' => $data['exam'],
                        $regOrEnrollment => $c['registrationOrEnrollment'],
                    ]);

                    if (!$answerEntity) {
                        $answerEntity = new ExamResult();
                        $answerEntity->setExam($exam);

                        if ($data['isStudent']) {
                            $answerEntity->setEnrollment($registrationOrEnrolment);
                        } else {
                            $answerEntity->setRegistration($registrationOrEnrolment);
                        }
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
                $regOrEnrollment = $data['isStudent'] ? 'enrollment' : 'registration';
                $examResult = $em->getRepository('SchoolManagement\Entity\ExamResult')->findOneBy([
                    'exam' => $data['exam'],
                    $regOrEnrollment => $data['registrationOrEnrollment']
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
            $data = (array)$request->getPost();

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

            $applications = $em->getRepository(ExamApplication::class)
                ->findBy([
                    'status' => ExamApplication::EXAM_APP_CREATED
                ], [
                    'examApplicationId' => 'desc',
                ]);

            $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')->findByEndDateGratherThan(new DateTime());

            $lastRecruitment = $em->getRepository('Recruitment\Entity\Recruitment')->findNotEndedByTypeAsArray(Recruitment::STUDENT_RECRUITMENT_TYPE);

            $rec = [
                'id' => $lastRecruitment['recruitmentId'],
                'desc' => Recruitment::formatName($lastRecruitment['recruitmentNumber'], $lastRecruitment['recruitmentYear'])
            ];

            return new ViewModel([
                'classes' => $classes,
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
            $isStudent = $this->getRequest()->getQuery('isStudent');

            if ($examId) {
                $em = $this->getEntityManager();
                $examAnswers = $em->getRepository(ExamResult::class)->findAllAnswersForClassOrRecruitment($examId, (bool)$isStudent);

                $answers = [];
                foreach ($examAnswers as $ea) {
                    $data = [
                        'answers' => Json::decode($ea->getAnswers()),
                    ];

                    if ($isStudent) {
                        $enr = $ea->getEnrollment();
                        $registration = $enr->getRegistration();
                        $person = $registration->getPerson();

                        $data['registrationOrEnrollment'] = $enr->getEnrollmentId();
                        $data['birth'] = $person->getPersonBirthday();
                        $data['fullname'] = $person->getPersonName();
                    } else {
                        $registration = $ea->getRegistration();
                        $person = $registration->getPerson();
                        $status = $registration->getCurrentRegistrationStatus()->getRecruitmentStatus()->getStatusType();

                        $data['registrationOrEnrollment'] = $registration->getRegistrationId();
                        $data['registrationNumber'] = $registration->getRegistrationNumber();
                        $data['currentStatus'] = $status;
                        $data['birth'] = $person->getPersonBirthday();
                        $data['fullname'] = $person->getPersonName();
                    }

                    $answers[] = $data;
                }

                return new JsonModel([
                    'examId' => $examId,
                    'answers' => $answers
                ]);
            }
        } catch (\Exception $ex) { }

        return [];
    }

    public function saveResultAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost();

            if (!empty($data['results']) && !empty($data['application']) && !empty($data['recruitmentOrClass'])) {
                try {
                    $em = $this->getEntityManager();
                    $app = $em->getReference(ExamApplication::class, $data['application']);
                    $registrationOrEnrollment = 'registration';
                    $referencedClass = Registration::class;

                    if($data['isStudent']) {
                        $studentClass = $em->getReference(StudentClass::class, $data['recruitmentOrClass']);
                        $app->setStudentClass($studentClass);
                        $registrationOrEnrollment = 'enrollment';
                        $referencedClass = Enrollment::class;
                    } else {
                        $rec = $em->getReference(Recruitment::class, $data['recruitmentOrClass']);
                        $app->setRecruitment($rec);
                    }

                    foreach ($data['results'] as $result) {

                        $appResult = $em->getRepository(ExamApplicationResult::class)->findOneBy([
                            'application' => $data['application'],
                            $registrationOrEnrollment => $result['registrationOrEnrollment']
                        ]);

                        if ($appResult === null) {

                            $regOrEnroll = $em->getReference($referencedClass, $result['registrationOrEnrollment']);
                            $appResult = new ExamApplicationResult();

                            $appResult
                                ->setApplication($app)
                                ->setResult(Json::encode([
                                    'partialResult' => $result['partialResult'],
                                    'result' => $result['result'],
                                    'groups' => $result['groups'],
                                    'position' => $result['position']
                                ]));

                            if($data['isStudent']) {
                                $appResult->setEnrollment($regOrEnroll);
                            } else {
                                $appResult->setRegistration($regOrEnroll);
                            }

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

        $isStudent = $this->getRequest()->getQuery('isStudent');

        if ($id) {

            $em = $this->getEntityManager();
            $appResults = $em->getRepository(ExamApplicationResult::class)->findAllAnswersForClassOrRecruitment($id, $isStudent);

            $results = [];
            foreach ($appResults as $res) {
                $r = Json::decode($res->getResult(), Json::TYPE_ARRAY);
                $data = [
                    'partialResult' => $r['partialResult'],
                    'result' => $r['result'],
                    'groups' => $r['groups'],
                    'position' => $r['position']
                ];

                if ($isStudent) {
                    $enr = $res->getEnrollment();
                    $registration = $enr->getRegistration();
                    $person = $registration->getPerson();

                    $data['registrationOrEnrollment'] = $enr->getEnrollmentId();
                    $data['fullname'] = $person->getPersonName();
                } else {
                    $reg = $res->getRegistration();
                    $status = $reg->getCurrentRegistrationStatus()->getRecruitmentStatus()->getStatusType();

                    $data['registrationOrEnrollment'] = $reg->getRegistrationId();
                    $data['registrationNumber'] = $reg->getRegistrationNumber();
                    $data['currentStatus'] = $status;
                }

                $results[] = $data;
            }

            return new JsonModel($results);
        }
    }
}
