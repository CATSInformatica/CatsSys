<?php
/*
 * Copyright (C) 2016 Gabriel Pereira <rickardch@gmail.com>
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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use SchoolManagement\Entity\Exam;
use SchoolManagement\Entity\ExamApplication;
use SchoolManagement\Entity\ExamContent;
use SchoolManagement\Form\ExamForm;
use SchoolManagement\Form\ExamContentForm;
use SchoolManagement\Form\ExamApplicationForm;
use SchoolManagement\Entity\ExamQuestion;
use SchoolManagement\Form\ExamQuestionForm;
use SchoolManagement\Form\SearchQuestionsForm;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Description of SchoolExamController
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class SchoolExamController extends AbstractEntityActionController
{

    /**
     * Exibe uma tabela com todas as provas aplicadas
     * 
     * @return ViewModel
     */
    public function applicationsAction()
    {
        try {
            $em = $this->getEntityManager();
            $applications = $em->getRepository('SchoolManagement\Entity\ExamApplication')->findAll();

            return new ViewModel(array(
                'message' => null,
                'applications' => $applications,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema. Erro: ' . $ex->getMessage(),
                'applications' => null,
            ));
        }
    }

    /**
     * Exibe um formulário para criação de uma aplicação de prova
     * 
     * @return ViewModel
     */
    public function createApplicationAction()
    {
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $application = new ExamApplication();

        try {
            // apenas provas não aplicadas podem ser associadas
            $exams = $em->getRepository('SchoolManagement\Entity\Exam')->findBy([
                'application' => null,
            ]);
            $form = new ExamApplicationForm($em);
            $form->bind($application);

            if ($request->isPost()) {
                $rawData = $request->getPost();
                $form->setData($rawData);
                if ($form->isValid()) {
                    $examIds = $rawData['appExams'];
                    foreach ($examIds as $examId) {
                        $exam = $em->find('SchoolManagement\Entity\Exam', $examId);
                        $application->addExam($exam);
                    }

                    $em->persist($application);
                    $em->flush();
                    return $this->redirect()->toRoute('school-management/school-exam', array('action' => 'applications'));
                }

                return new ViewModel(array(
                    'message' => empty($rawData['appExams']) ? 'É obrigatório escolher ao menos uma prova' : null,
                    'exams' => $exams,
                    'form' => $form,
                ));
            }

            return new ViewModel(array(
                'message' => null,
                'exams' => $exams,
                'form' => $form,
            ));
        } catch (UniqueConstraintViolationException $ex) {
            return new ViewModel(array(
                'message' => 'Já existe uma aplicação com este nome.',
                'exams' => null,
                'form' => null,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema. Erro: ' . $ex->getMessage(),
                'exams' => null,
                'form' => null,
            ));
        }
    }

    /**
     * Exibe um formulário para edição de uma aplicação de prova
     * 
     * @return ViewModel
     */
    public function editApplicationAction()
    {
        $applicationId = $this->params('id', false);

        if ($applicationId) {
            $em = $this->getEntityManager();
            $request = $this->getRequest();

            try {
                // apenas provas não aplicadas podem ser associadas
                $exams = $em->getRepository('SchoolManagement\Entity\Exam')->findBy([
                    'application' => null,
                ]);
                $application = $em->find('SchoolManagement\Entity\ExamApplication', $applicationId);
                $selectedExams = $application->getExams()->toArray();

                $form = new ExamApplicationForm($em);
                $form->bind($application);
                $form->get('submit')->setAttribute('value', 'Editar Aplicação de Prova');

                if ($request->isPost()) {
                    $rawData = $request->getPost();
                    $form->setData($rawData);
                    if ($form->isValid()) {
                        $examIds = $rawData['appExams'];
                        $application->removeAllExams();
                        foreach ($examIds as $examId) {
                            $exam = $em->find('SchoolManagement\Entity\Exam', $examId);
                            $application->addExam($exam);
                        }
                        $em->persist($application);
                        $em->flush();
                        return $this->redirect()->toRoute('school-management/school-exam', array('action' => 'applications'));
                    }
                }

                return new ViewModel(array(
                    'message' => null,
                    'exams' => $exams,
                    'form' => $form,
                    'selectedExams' => $selectedExams,
                ));
            } catch (UniqueConstraintViolationException $ex) {
                return new ViewModel(array(
                    'message' => 'Já existe uma aplicação com este nome.',
                    'exams' => null,
                    'form' => null,
                    'selectedExams' => null,
                ));
            } catch (Exception $ex) {
                return new ViewModel(array(
                    'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema. Erro: ' . $ex->getMessage(),
                    'exams' => null,
                    'form' => null,
                    'selectedExams' => null
                ));
            }
        }
    }

    /**
     * Remove a aplicação de simulado selecionada
     * 
     * @return JsonModel
     */
    public function deleteApplicationAction()
    {
        $applicationId = $this->params('id', false);

        if ($applicationId) {
            try {
                $em = $this->getEntityManager();
                $application = $em->getReference('SchoolManagement\Entity\ExamApplication', $applicationId);
                foreach ($application->getExams() as $exam) {
                    $application->removeAllExams();
                    $exam->setApplication(null);
                }

                $em->remove($application);
                $em->flush();

                return new JsonModel(array(
                    'message' => 'Aplicação de prova removida com sucesso.',
                    'callback' => array(
                        'applicationId' => $applicationId,
                    ),
                ));
            } catch (Exception $ex) {
                $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                    'Erro: ' . $ex->getMessage();
            }
        } else {
            $message = 'Nenhuma aplicação de prova foi selecionada.';
        }
        return new JsonModel(array(
            'message' => $message
        ));
    }

    /**
     * Exibe uma interface para visualização e geração de PDFs referentes 
     * a aplicação de prova
     * 
     * @return ViewModel
     */
    public function prepareApplicationAction()
    {
        $applicationId = $this->params('id', false);

        if ($applicationId) {
            $em = $this->getEntityManager();
            try {
                $application = $em->find('SchoolManagement\Entity\ExamApplication', $applicationId);

                return new ViewModel(array(
                    'message' => null,
                    'application' => $application,
                    'applicationId' => $applicationId,
                ));
            } catch (Exception $ex) {
                return new ViewModel(array(
                    'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema. Erro: ' . $ex->getMessage(),
                    'application' => null,
                    'applicationId' => null,
                ));
            }
        }
    }

    /**
     * Exibe uma tabela com todas as provas
     * 
     * @return ViewModel
     */
    public function examsAction()
    {
        try {
            $em = $this->getEntityManager();
            $exams = $em->getRepository('SchoolManagement\Entity\Exam')->findAll();

            return new ViewModel(array(
                'message' => null,
                'exams' => $exams,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema. Erro: ' . $ex->getMessage(),
                'exams' => null,
            ));
        }
    }

    /**
     * Exibe o formulário de criação de provas
     * 
     * @return ViewModel
     */
    public function createExamAction()
    {
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $exam = new Exam();

        try {
            $form = new ExamForm($em);
            $form->bind($exam);

            if ($request->isPost()) {
                $rawFormData = $request->getPost();
                $form->setData($rawFormData);

                if ($form->isValid()) {
                    $contentId = $rawFormData['exam-fieldset']['examContent'];
                    $content = $em->find('SchoolManagement\Entity\ExamContent', $contentId);
                    $content->addExam($exam);

                    $em->persist($exam);
                    $em->flush();
                    return $this->redirect()->toRoute('school-management/school-exam', array('action' => 'exams'));
                }
            }
            return new ViewModel(array(
                'message' => null,
                'form' => $form,
            ));
        } catch (UniqueConstraintViolationException $ex) {
            return new ViewModel(array(
                'message' => 'Já existe um simulado com este nome.',
                'form' => null,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema. Erro: ' . $ex->getMessage(),
                'form' => null,
            ));
        }
    }

    /**
     * Exibe o formulário de edição de provas
     * 
     * @return ViewModel
     */
    public function editExamAction()
    {
        $examId = $this->params('id', false);

        if ($examId) {
            $em = $this->getEntityManager();
            $request = $this->getRequest();

            try {
                $exam = $em->find('SchoolManagement\Entity\Exam', $examId);

                $form = new ExamForm($em);
                $form->bind($exam);
                $form->get('submit')->setAttribute('value', 'Editar Prova');
                $form->get('exam-fieldset')
                    ->get('examContent')
                    ->setValue($exam->getContent()->getExamContentId());

                if ($request->isPost()) {
                    $rawFormData = $request->getPost();
                    $form->setData($rawFormData);

                    if ($form->isValid()) {
                        $contentId = $rawFormData['exam-fieldset']['examContent'];
                        $content = $em->find('SchoolManagement\Entity\ExamContent', $contentId);
                        $content->addExam($exam);

                        $em->persist($exam);
                        $em->flush();
                        return $this->redirect()->toRoute('school-management/school-exam', array('action' => 'exams'));
                    }
                }
                return new ViewModel(array(
                    'message' => null,
                    'form' => $form,
                ));
            } catch (UniqueConstraintViolationException $ex) {
                return new ViewModel(array(
                    'message' => 'Já existe um simulado com este nome.',
                    'form' => null,
                ));
            } catch (Exception $ex) {
                return new ViewModel(array(
                    'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema. Erro: ' . $ex->getMessage(),
                    'form' => null,
                ));
            }
        }
    }

    /**
     * Remove o simulado selecionado
     * 
     * @return JsonModel
     */
    public function deleteExamAction()
    {
        $examId = $this->params('id', false);

        if ($examId) {
            try {
                $em = $this->getEntityManager();
                $exam = $em->getReference('SchoolManagement\Entity\Exam', $examId);

                if ($exam->getApplication() !== null) {
                    return new JsonModel(array(
                        'message' => 'Essa prova não pode ser removida pois está associada a uma aplicação.',
                    ));
                }

                $em->remove($exam);
                $em->flush();

                return new JsonModel(array(
                    'message' => 'Simulado removido com sucesso.',
                    'callback' => array(
                        'examId' => $examId,
                    ),
                ));
            } catch (Exception $ex) {
                $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                    'Erro: ' . $ex->getMessage();
            }
        } else {
            $message = 'Nenhum simulado foi selecionado.';
        }
        return new JsonModel(array(
            'message' => $message
        ));
    }

    /**
     * Exibe uma tabela com todos os conteúdos
     * 
     * @return ViewModel
     */
    public function contentsAction()
    {
        try {
            $em = $this->getEntityManager();
            $contents = $em->getRepository('SchoolManagement\Entity\ExamContent')->findAll();
            return new ViewModel(array(
                'message' => null,
                'contents' => $contents,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema. Erro: ' . $ex->getMessage(),
                'contents' => null,
            ));
        }
    }

    /**
     * Salva o conteúdo com 
     * 
     * @param ExamContent $examContent - entidade que deve ser atualizada e/ou salva
     * @param Array $baseSubjects - Array com objetos de todas as disciplinas base
     * @param Array $quantities - Array com as quantidades de questões por disciplina
     *  Ex: 
     *  $quantities = [
     *      <base-subject-id> => [
     *          <child-subject-id> => <child-subject-quantity>, 
     *          .
     *          .
     *          .
     *      ],
     *      .
     *      .
     *      .
     *  ]
     * @param bool $editAllowed - Indica se o JSON deve ser atualizado ou não. 
     *  O JSON não pode ser atualizado quando o conteúdo está associado a 
     *  uma ou mais provas
     * @throws Exception - Qualquer exceção que possa ser lançada é devolvida
     *  para o método que invocou essa função
     */
    protected function saveContent($examContent, $baseSubjects = [], $quantities = [], $editAllowed = false)
    {
        try {
            $em = $this->getEntityManager();
            if ($editAllowed) {
                $areas = [];
                $i = 0;
                foreach ($baseSubjects as $baseSubject) {
                    $areas[$baseSubject->getSubjectId()] = [];
                    foreach ($baseSubject->getChildren() as $subject) {
                        $areas[$baseSubject->getSubjectId()][$subject->getSubjectId()] = $quantities[$i++]['quantity'];
                    }
                }

                $examContentConfig = [
                    "header" => [
                        "areas" => $areas,
                    ],
                    "questions" => null
                ];

                $examContent->setConfig(json_encode($examContentConfig));
            }
            $em->persist($examContent);
            $em->flush();
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Exibe uma página para preparação do conteúdo de uma prova (conjunto de questões)
     * 
     * @return ViewModel
     */
    public function createContentAction()
    {
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $examContent = new ExamContent();
        $editAllowed = true;

        try {
            $criteria = Criteria::create()
                ->where(Criteria::expr()->neq("subjectName", "REDAÇÃO"))
                ->andWhere(Criteria::expr()->isNull("parent"));

            $baseSubjects = $em->getRepository('SchoolManagement\Entity\Subject')
                ->matching($criteria);

            $form = new ExamContentForm($em, $baseSubjects);
            $form->bind($examContent);
            if ($request->isPost()) {
                $raw = $request->getPost();
                $quantities = $raw['examQuestionQuantity'];
                $form->setData($raw);

                if ($form->isValid()) {
                    $this->saveContent($examContent, $baseSubjects, $quantities, $editAllowed);
                    return $this->redirect()->toRoute('school-management/school-exam', array('action' => 'contents'));
                }
            }

            return new ViewModel(array(
                'message' => null,
                'form' => $form,
                'baseSubjects' => $baseSubjects,
                'editAllowed' => $editAllowed
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema. Erro: ' . $ex->getMessage(),
                'form' => null,
                'baseSubjects' => null,
                'editAllowed' => null
            ));
        }
    }

    /**
     * Formulário de edição de um conteúdo, se esse conteúdo já tiver sido 
     * utilizado em alguma prova, permite editar apenas a descrição
     * 
     * @return ViewModel
     */
    public function editContentAction()
    {
        $contentId = $this->params('id', false);

        if ($contentId) {
            try {
                $em = $this->getEntityManager();
                $request = $this->getRequest();

                $content = $em->find('SchoolManagement\Entity\ExamContent', $contentId);

                $editAllowed = $this->isExamContentEditable($content);

                $config = json_decode($content->getConfig(), true);
                $subjectQuantities = $config['header']['areas'];

                $criteria = Criteria::create()
                    ->where(Criteria::expr()->neq("subjectName", "REDAÇÃO"))
                    ->andWhere(Criteria::expr()->isNull("parent"));
                $baseSubjects = $em->getRepository('SchoolManagement\Entity\Subject')
                    ->matching($criteria);

                $form = new ExamContentForm($em, $baseSubjects);
                $form->bind($content);
                $form->get('submit')->setAttribute('value', 'Editar Conteúdo');
                if ($request->isPost()) {
                    $raw = $request->getPost();
                    $quantities = $raw['examQuestionQuantity'];
                    $form->setData($raw);

                    if ($form->isValid()) {
                        $this->saveContent($content, $baseSubjects, $quantities, $editAllowed);
                        return $this->redirect()->toRoute('school-management/school-exam', array('action' => 'contents'));
                    }
                }

                return new ViewModel(array(
                    'message' => null,
                    'form' => $form,
                    'baseSubjects' => $baseSubjects,
                    'subjectQuantities' => $subjectQuantities,
                    'editAllowed' => $editAllowed,
                ));
            } catch (Exception $ex) {
                return new ViewModel(array(
                    'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema. Erro: ' . $ex->getMessage(),
                    'form' => null,
                    'baseSubjects' => null,
                    'subjectQuantities' => null,
                    'editAllowed' => null
                ));
            }
        }
    }

    /**
     * Verifica se é possível editar o conteúdo de prova.
     * 
     * Só permite edição se o conteúdo nunca foi aplicado antes.
     * 
     * @param ExamContent $content Conteúdo de prova
     */
    private function isExamContentEditable(ExamContent $content)
    {
        $editAllowed = true;
        $associatedExams = $content->getExams();
        foreach ($associatedExams as $exam) {
            $application = $exam->getApplication();
            if ($application !== null && $application->getStatus() === ExamApplication::EXAM_APP_APPLIED) {
                $editAllowed = false;
                break;
            }
        }
        
        return $editAllowed;
    }

    /**
     * Remove o conteúdo selecionado
     * 
     * @return JsonModel
     */
    public function deleteContentAction()
    {
        $id = $this->params('id', false);

        if ($id) {
            try {
                $em = $this->getEntityManager();
                $content = $em->getReference('SchoolManagement\Entity\ExamContent', $id);

                if (count($content->getExams()) !== 0) {
                    return new JsonModel(array(
                        'message' => 'Esse conteúdo não pode ser removido pois está associado a uma ou mais provas.',
                    ));
                }

                $em->remove($content);
                $em->flush();

                return new JsonModel(array(
                    'message' => 'Conteúdo removido com sucesso.',
                    'callback' => array(
                        'contentId' => $id,
                    ),
                ));
            } catch (Exception $ex) {
                $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                    'Erro: ' . $ex->getMessage();
            }
        } else {
            $message = 'Nenhum conteúdo foi selecionado.';
        }
        return new JsonModel(array(
            'message' => $message
        ));
    }

    /**
     * Permite adicionar ou retirar questões do conteúdo de prova
     * 
     * @return ViewModel
     */
    public function prepareContentAction()
    {
        $contentId = $this->params('id', false);

        if ($contentId) {
            try {
                $em = $this->getEntityManager();
                $baseSubjects = $em->getRepository('SchoolManagement\Entity\Subject')
                    ->findBy(array("parent" => null));

                $content = $em->find('SchoolManagement\Entity\ExamContent', $contentId);
                $config = json_decode($content->getConfig(), true);
                $subjectQuantities = $config['header']['areas'];

                $editAllowed = $this->isExamContentEditable($content);

                return new ViewModel(array(
                    'message' => null,
                    'baseSubjects' => $baseSubjects,
                    'subjectQuantities' => $subjectQuantities,
                    'contentDescription' => $content->getDescription(),
                    'contentId' => $contentId,
                    'editAllowed' => $editAllowed,
                ));
            } catch (Exception $ex) {
                return new ViewModel(array(
                    'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema. Erro: ' . $ex->getMessage(),
                    'baseSubjects' => null,
                    'subjectQuantities' => null,
                    'contentDescription' => null,
                    'contentId' => null,
                    'editAllowed' => null,
                ));
            }
        }
    }

    /**
     * Salva as questões selecionadas na preparação do simulado
     * 
     * @return JsonModel
     */
    public function saveContentQuestionsAction()
    {
        $request = $this->getRequest();
        $result = [
            'message' => '',
            'error' => false,
        ];

        if ($request->isPost()) {
            try {
                $em = $this->getEntityManager();
                $data = $request->getPost();

                $examContent = $em->find('SchoolManagement\Entity\ExamContent', (int) $data['contentId']);
                $contentConfig = json_decode($examContent->getConfig(), true);
                unset($contentConfig['questions']);
                $contentConfig['questions'] = $data['questions'];
                $examContent->setConfig(json_encode($contentConfig));

                $em->persist($examContent);
                $em->flush();

                $result = [
                    'message' => 'Questões salvas com sucesso.',
                    'error' => false,
                ];
                return new JsonModel($result);
            } catch (Exception $ex) {
                $result = [
                    'message' => $ex->getMessage(),
                    'error' => true,
                ];
            }
        }
        return new JsonModel($result);
    }

    /**
     * Retorna todas as questões do conteúdo do id passado por parâmetro
     * 
     * @return JsonModel
     *  Retorno do tipo: [
     *      {
     *          "id": <integer>, 
     *          "enunciation": <string>, 
     *          "alternatives": [
     *              0: <string>,
     *              ...
     *          ],
     *          "answer": <integer>,
     *          "subjectId": <integer>, 
      "subjectName": <string>,
     *          "baseSubjectId": <integer>,
      "baseSubjectName": <string>
     *      },
     *      ...
     *  ]
     */
    public function getContentQuestionsAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            try {
                $em = $this->getEntityManager();
                $data = $request->getPost();

                $examContent = $em->find('SchoolManagement\Entity\ExamContent', (int) $data['contentId']);
                $contentConfig = json_decode($examContent->getConfig(), true);
                $contentQuestions = $contentConfig['questions'];
                if ($contentQuestions === null) {
                    return new JsonModel(['questions' => []]);
                }

                $formattedContentQuestions = [];
                foreach ($contentQuestions as $i => $question) {
                    $q = $em->find('SchoolManagement\Entity\ExamQuestion', $question['questionId']);
                    if ($q === null) {
                        $formattedContentQuestions[] = [
                            'id' => $question['questionId'],
                            'enunciation' => 'A questão foi removida',
                            'alternatives' => null,
                            'answer' => null,
                            'subjectId' => null,
                            'subjectName' => null,
                            'baseSubjectId' => null,
                            'baseSubjectName' => null,
                        ];

                        continue;
                    }

                    $qAnswer = -1;
                    $qAlternatives = [];
                    foreach ($q->getAnswerOptions() as $i => $alternative) {
                        $qAlternatives[] = $alternative->getExamAnswerDescription();
                        if ($alternative->getIsCorrect()) {
                            $qAnswer = $i;
                        }
                    }

                    $baseSubject = $q->getSubject();
                    while ($baseSubject->getParent() !== null) {
                        $baseSubject = $baseSubject->getParent();
                    }

                    $formattedContentQuestions[] = [
                        'id' => $q->getExamQuestionId(),
                        'enunciation' => $q->getExamQuestionEnunciation(),
                        'alternatives' => $qAlternatives,
                        'answer' => $qAnswer,
                        'subjectId' => $q->getSubject()->getSubjectId(),
                        'subjectName' => $q->getSubject()->getSubjectName(),
                        'baseSubjectId' => $baseSubject->getSubjectId(),
                        'baseSubjectName' => $baseSubject->getSubjectName(),
                    ];
                }

                return new JsonModel(['questions' => $formattedContentQuestions]);
            } catch (Exception $ex) {
                return new JsonModel(['questions' => []]);
            }
        }
        return new JsonModel(['questions' => []]);
    }

    /**
     * Retorna todas as questões cadastradas para a matéria $data['subject'] do tipo $data['questionType']
     * 
     * @return JsonModel
     *  Retorno do tipo: [
     *      {
     *          'questionId' => <integer>,
     *          'questionEnunciation' => <string>,
     *          'questionAlternatives' => [
     *              0: <string>,
     *              ...
     *          ],
     *          'questionCorrectAlternative' => <integer>
     *      }
     * ]
     */
    public function getQuestionsAction()
    {
        $request = $this->getRequest();
        $result = [];

        if ($request->isPost()) {
            try {
                $em = $this->getEntityManager();
                $form = new SearchQuestionsForm($em);
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $data = $form->getData();
                    $subject = $em->getReference('SchoolManagement\Entity\Subject', $data['subject']);
                    $questionType = $data['questionType'];
                    if ($questionType > 0) { // Um tipo específico de questão foi selecionado
                        $questions = $em->getRepository('SchoolManagement\Entity\ExamQuestion')->findBy([
                            'examQuestionType' => $questionType,
                            'subject' => $subject,
                        ]);
                    } else {
                        $questions = $em->getRepository('SchoolManagement\Entity\ExamQuestion')->findBy([
                            'subject' => $subject,
                        ]);
                    }
                    foreach ($questions as $q) {
                        $alternatives = [];
                        $answerOptions = $q->getAnswerOptions()->toArray();
                        $correctAlternative = -1;
                        foreach ($answerOptions as $i => $ao) {
                            $alternatives[$i] = $ao->getExamAnswerDescription();
                            if ($ao->getIsCorrect()) {
                                $correctAlternative = $i;
                            }
                        }
                        $result[] = array(
                            'questionId' => $q->getExamQuestionId(),
                            'questionEnunciation' => $q->getExamQuestionEnunciation(),
                            'questionAlternatives' => $alternatives,
                            'questionCorrectAlternative' => $correctAlternative,
                        );
                    }
                }
            } catch (Exception $ex) {
                $result[] = array(
                    'questionId' => -1,
                    'questionEnunciation' => 'Erro: ' . $ex,
                    'questionAlternatives' => -1,
                    'questionCorrectAlternative' => -1,
                );
            }
        }
        return new JsonModel($result);
    }

    /**
     * Exibe em uma tabela todas as questões cadastradas
     * 
     * @return ViewModel
     */
    public function questionAction()
    {
        try {
            $em = $this->getEntityManager();
            $form = new SearchQuestionsForm($em);

            return new ViewModel(array(
                'message' => null,
                'form' => $form,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema.' .
                'Erro: ' . $ex->getMessage(),
                'form' => null,
            ));
        }
    }

    /**
     * Exibe um formulário para adição de uma questão da disciplina selecionada ao banco de questões
     * 
     * @return ViewModel
     */
    public function addQuestionAction()
    {
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $examQuestion = new ExamQuestion();

        if ($request->isPost()) {
            $data = $request->getPost();
            $numberOfOptions = count($data['exam-question']['answerOptions']);
            $type = $data['exam-question']['examQuestionType'];
            $form = new ExamQuestionForm($em, $type, $numberOfOptions);
            $form->bind($examQuestion);
            $form->setData($data);

            if ($form->isValid()) {
                $ao = $examQuestion->getAnswerOptions()->toArray();
                $correctAnswer = (int) $data['exam-question']['correctAnswer'];
                $ao[$correctAnswer]->setIsCorrect(true);
                $em->persist($examQuestion);
                $em->flush();

                // Se o procedimento for bem sucedido, a página é redirecionada para o banco de questões
                return $this->redirect()->toRoute('school-management/school-exam', array('action' => 'question'));
            }
        } else {
            $form = new ExamQuestionForm($em);
            $form->bind($examQuestion);
        }

        return new ViewModel(array(
            'message' => null,
            'form' => $form,
        ));
    }

    /**
     * Exibe um formulário de edição para a questão selecionada
     * 
     * @return ViewModel
     */
    public function editQuestionAction()
    {
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $message = null;

        $q = $this->params('id', false);

        if ($q) {

            try {
                $question = $em->find('SchoolManagement\Entity\ExamQuestion', $q);
                $aId = 0;

                // @Todo trocar por algum tipo de Criteria IsCorrect = true
                foreach ($question->getAnswerOptions() as $i => $q) {
                    if ($q->getIsCorrect()) {
                        $aId = $i;
                        break;
                    }
                }

                if ($request->isPost()) {
                    $data = $request->getPost();
                    $numberOfOptions = count($data['exam-question']['answerOptions']);

                    $type = $data['exam-question']['examQuestionType'];
                    $form = new ExamQuestionForm($em, $type, $numberOfOptions);

                    // por algum motivo, não sei dizer qual, o formulário não
                    // faz a remoção de alternativas que sobram, caso:
                    // número de alternativas antes da edição > número de alternativas depois da edição.
                    // Faz a remoção manual.
                    $options = $question->getAnswerOptions();
                    if (($length = $options->count() - $numberOfOptions) > 0) {
                        $optsToRemove = new ArrayCollection($options->slice($numberOfOptions, $length));
                        $question->removeAnswerOptions($optsToRemove);
                    }

                    $form->bind($question);
                    $form->setData($data);

                    if ($form->isValid()) {

                        //  Conversão para inteiro
                        $ao = $question->getAnswerOptions()->toArray();
                        $correctAnswer = (int) $data['exam-question']['correctAnswer'];

                        // Se a resposta correta antiga ainda existe ela é desmarcada (isCorrect = false)
                        if ($aId < $numberOfOptions) {
                            $ao[$aId]->setIsCorrect(false);
                        }

                        $ao[$correctAnswer]->setIsCorrect(true);

                        $em->persist($question);
                        $em->flush();
                        return $this
                                ->redirect()
                                ->toRoute('school-management/school-exam', array('action' => 'question'));
                    }
                } else {
                    $typeBefore = $question->getExamQuestionType();
                    $form = new ExamQuestionForm($em, $typeBefore, count($question->getAnswerOptions()->toArray()));

                    $form->bind($question);

                    $sId = $question->getSubject()->getSubjectId();
                    $form
                        ->get('exam-question')
                        ->get('correctAnswer')
                        ->setValue($aId);

                    $form
                        ->get('exam-question')
                        ->get('subject')
                        ->setValue($sId);
                }

                return new ViewModel(array(
                    'message' => $message,
                    'form' => $form,
                ));
            } catch (Exception $ex) {
                $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                    'Erro: ' . $ex->getMessage();
            }
        } else {
            $message = 'Nenhuma questão foi selecionada.';
        }
        return new ViewModel(array(
            'message' => $message,
            'form' => null,
        ));
    }

    /**
     * Remove do banco de dados a questão selecionada
     * 
     * @return JsonModel
     */
    public function deleteQuestionAction()
    {
        $message = null;
        $q = $this->params('id', false);
        if ($q) {
            try {
                $em = $this->getEntityManager();
                $question = $em->find('SchoolManagement\Entity\ExamQuestion', $q);
                $em->remove($question);
                $em->flush();
                $message = 'Questão removida com sucesso.';
                return new JsonModel(array(
                    'message' => $message,
                    'callback' => array(
                        'questionId' => $q,
                    ),
                ));
            } catch (Exception $ex) {
                $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                    'Erro: ' . $ex->getMessage();
            }
        } else {
            $message = 'Nenhuma questão foi selecionada.';
        }
        return new JsonModel(array(
            'message' => $message,
        ));
    }
}
