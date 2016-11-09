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
use Exception;
use SchoolManagement\Entity\ApplicationResult;
use SchoolManagement\Entity\ExamApplication;
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
     * Salva as respostas dos candidatos em uma prova de uma aplicação de psa.
     * 
     * @return \SchoolManagement\Controller\JsonModel
     */
//    public function uploadAnswersByStdRecruitmentAction()
//    {
//        return new ViewModel([
//        ]);
//    }

    /**
     * Salva as respostas dos alunos em uma prova de uma aplicação de simulados.
     * 
     * @return \SchoolManagement\Controller\JsonModel
     */
    public function saveStudentAnswersAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost();
            try {

                $em = $this->getEntityManager();
                $application = $em->getReference('SchoolManagement\Entity\ExamApplication', $data['application']);

                foreach ($data['students'] as $st) {

                    $enrollment = $em->getReference('SchoolManagement\Entity\Enrollment', $st['enrollment']);
                    $encodedAnswers = Json::encode([
                            'answers' => $st['answers'],
                            'languageOption' => $st['languageOption'],
                    ]);

                    $answerEntity = $em->getRepository('SchoolManagement\Entity\ApplicationResult')->findOneBy([
                        'enrollment' => $enrollment->getEnrollmentId(),
                        'application' => $application->getExamApplicationId(),
                    ]);

                    if ($answerEntity === null) {
                        $answerEntity = new ApplicationResult();
                        $answerEntity
                            ->setApplication($application)
                            ->setEnrollment($enrollment);
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

    /**
     * Lista todas as aplicações de prova para escolha.
     * 
     * @return ViewModel
     */
    public function uploadAnswersTemplateAction()
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
    public function getAnswersAction()
    {
        $id = $this->params('id', false);

        if ($id) {
            try {

                $em = $this->getEntityManager();

                $exam = $em->find('SchoolManagement\Entity\Exam', $id);
                $answers = Json::decode($exam
                            ->getContent()
                            ->getConfig());

                return new JsonModel([$answers]);
            } catch (\Exception $ex) {
                $this->getResponse()->setStatusCode(400);
            }
        }

        $this->getResponse()->setStatusCode(400);
    }
}
