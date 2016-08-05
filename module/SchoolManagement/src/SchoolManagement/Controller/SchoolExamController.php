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
use Exception;
use SchoolManagement\Entity\Exam;
use SchoolManagement\Form\ExamForm;
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
     * Exibe uma tabela com todos os simulados gerados ou em desenvolvimento
     * 
     * @return ViewModel
     */
    public function indexAction()
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
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema.' . 'Erro: ' . $ex->getMessage(),
                'exams' => null,
            ));
        }
    }

    /**
     * Exibe o formulário de criação do simulado.
     * O formulário é salvo pela action saveConfig
     * 
     * @return ViewModel
     */
    public function createAction()
    {
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $exam = new Exam();
        
        try {
            $criteria = Criteria::create()
                    ->where(Criteria::expr()->neq("subjectName", "REDAÇÃO"))
                    ->andWhere(Criteria::expr()->isNull("parent"));
            
            $baseSubjects = $em->getRepository('SchoolManagement\Entity\Subject')
                    ->matching($criteria);
            
            $form = new ExamForm($em, $baseSubjects);
            $form->bind($exam);

            return new ViewModel(array(
                'message' => null,
                'form' => $form,
                'baseSubjects' => $baseSubjects,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema.' . 'Erro: ' . $ex->getMessage(),
                'form' => null,
                'baseSubjects' => null,
            ));
        }
    }

    /**
     * Preparação do simulado. Inclui configuração, seleção de questões e geração de PDFs
     * 
     * @return ViewModel
     */
    public function prepareAction()
    {
        $id = $this->params('id', false);
        if ($id) {
            try {
                $em = $this->getEntityManager();
                $exam = $em->find('SchoolManagement\Entity\Exam', $id);
                $examConfig = json_decode($exam->getExamConfig(), true);
                $quantities = [];
                
                $baseSubjects = [];
                $baseSubjects[] = $em->getRepository('SchoolManagement\Entity\Subject')->findOneBy(array('subjectName' => 'REDAÇÃO'));
                foreach ($examConfig['header']['areas'] as $baseSubjectId => $children) {
                    $baseSubjects[] = $em->find('SchoolManagement\Entity\Subject', $baseSubjectId);
                    foreach ($children as $subjectId => $quantity) {
                       $quantities[] = $quantity;
                    }
                }
                
                $form = new ExamForm($em, $baseSubjects, ["REDAÇÃO"]);
                
                $form->get('examNumberingStart')->setValue($examConfig['examNumberingStart']);
                $form->get('examBeginTime')->setValue($examConfig['header']['beginTime']);
                $form->get('examEndTime')->setValue($examConfig['header']['endTime']);
                $form->get('submit')->setValue('Salvar Configuração');
                $form->bind($exam);

                return new ViewModel(array(
                    'message' => null,
                    'baseSubjects' => $baseSubjects,
                    'quantities' => $quantities,
                    'form' => $form,
                    'examId' => $exam->getExamId(),
                ));
            } catch (Exception $ex) {
                return new ViewModel(array(
                    'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema.' . 'Erro: ' . $ex->getMessage(),
                    'baseSubjects' => null,
                    'quantities' => null,
                    'form' => null,
                    'examId' => null,
                ));
            }
        }
    }
    
    /**
     * Salva as configurações do simulado. 
     * Não salva as questões selecionadas. Esta parte é feita pela action saveExamQuestions
     * 
     * @return JsonModel
     */
    public function saveConfigAction() 
    {
        $request = $this->getRequest();
        $result = [
            'message' => '',
            'error' => false,
        ];

        if ($request->isPost()) {
            try {
                $em = $this->getEntityManager();
                
                $form = new ExamForm($em);
                $form->setData($request->getPost());
                $raw = $request->getPost();
                if (isset($raw['examId'])) {
                    $exam = $em->find('SchoolManagement\Entity\Exam', (int)$raw['examId']);
                    $form->bind($exam);
                }
                
                if ($form->isValid()) {
                    if (!isset($exam)) {
                        $exam = $form->getData();
                    }
                    
                    $elements = $form->getElements();
                    $baseSubjects = [];
                    foreach ($raw['baseSubjects'] as $baseSubject) {
                        $bSubject = $em->find('SchoolManagement\Entity\Subject', $baseSubject['sId']);
                        $baseSubjects[] = $bSubject;
                    }
                    
                    $examNumberingStart = (int)$elements['examNumberingStart']->getValue();
                    $examBeginTime = $elements['examBeginTime']->getValue();
                    if (\DateTime::createFromFormat('H:i', $examBeginTime) === false) {
                        $examBeginTime = '00:00';
                    }
                    $examEndTime = $elements['examEndTime']->getValue();
                    if (\DateTime::createFromFormat('H:i', $examEndTime) === false) {
                        $examEndTime = '00:00';
                    }
                    
                    $i = 0;
                    $areas = [];
                    foreach ($baseSubjects as $baseSubject) {
                        $areas[$baseSubject->getSubjectId()] = [];
                        foreach ($baseSubject->getChildren() as $j => $subject) {
                            $areas[$baseSubject->getSubjectId()][$subject->getSubjectId()] = $raw['examQuestionQuantity'][$i++]['quantity'];
                        }
                    }
                    
                    $questions = null;
                    if ($exam->getExamConfig() !== null) {
                        $json = json_decode($exam->getExamConfig(), true);
                        $questions = $json['questions'];
                    }
                    
                    $examConfig = [
                        "examNumberingStart" => $examNumberingStart,
                        "header" => [
                            "beginTime" => $examBeginTime,
                            "endTime" => $examEndTime,
                            "areas" => $areas,
                        ],
                        "questions" => $questions
                    ];
                    
                    $exam->setExamStatus(Exam::STATUS_CREATED);
                    $exam->setExamConfig(json_encode($examConfig));
                    
                    $em->persist($exam);
                    $em->flush();
                    
                    
                    $result = [
                        'message' => 'Configuração salva com sucesso.',
                        'error' => false,
                    ];
                    return new JsonModel($result);
                }
                $result = [
                    'message' => 'Ocorreu um erro ao salvar o formulário. Verifique se os campos foram preenchidos corretamente.',
                    'error' => true,
                ];
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
     * Salva as questões selecionadas na preparação do simulado
     * 
     * @return JsonModel
     */
    public function saveExamQuestionsAction() {
        $request = $this->getRequest();
        $result = [
            'message' => '',
            'error' => false,
        ];

        if ($request->isPost()) {
            try {
                $em = $this->getEntityManager();
                
                $data = $request->getPost();
                $exam = $em->find('SchoolManagement\Entity\Exam', (int)$data['examId']);
                $json = json_decode($exam->getExamConfig(), true);
                unset($json['questions']);
                $json['questions'] = $data['questions'];
                $exam->setExamConfig(json_encode($json));
                
                $em->persist($exam);
                $em->flush();


                $result = [
                    'message' => 'Configuração salva com sucesso.',
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
     * Retorna todas as questões do simulado do id passado por parâmetro
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
     *          "baseSubjectId": <integer>
     *      },
     *      ...
     *  ]
     */
    public function getExamQuestionsAction() {
        $request = $this->getRequest();
        $result = ['questions' => []];

        if ($request->isPost()) {
            try {
                $em = $this->getEntityManager();
                
                $data = $request->getPost();
                $exam = $em->find('SchoolManagement\Entity\Exam', (int)$data['examId']);
                $json = json_decode($exam->getExamConfig(), true);
                $questions = $json['questions'];
                
                $questionsData = [];
                foreach ($questions as $i => $question) {
                    $q = $em->find('SchoolManagement\Entity\ExamQuestion', $question['questionId']);
                    if ($q === null) {
                        $questionsData[] = [
                            'id' => $question['questionId'], 
                            'enunciation' => 'A questão foi removida', 
                            'alternatives' => null,
                            'answer' => null,
                            'subjectId' => null, 
                            'baseSubjectId' => null
                        ];
                        
                        continue;
                    }
                    
                    $answer = -1;
                    $alternatives = [];
                    foreach ($q->getAnswerOptions() as $i => $alternative) {
                        $alternatives[] = $alternative->getExamAnswerDescription();
                        if ($alternative->getIsCorrect()) {
                            $answer = $i;
                        }
                    }
                    
                    $baseSubject = $q->getSubject();
                    while ($baseSubject->getParent() !== null) {
                        $baseSubject = $baseSubject->getParent();
                    }
                    
                    $questionsData[] = [
                        'id' => $q->getExamQuestionId(), 
                        'enunciation' => $q->getExamQuestionEnunciation(), 
                        'alternatives' => $alternatives,
                        'answer' => $answer,
                        'subjectId' => $q->getSubject()->getSubjectId(), 
                        'baseSubjectId' => $baseSubject->getSubjectId()
                    ];
                }
                
                $result = ['questions' => $questionsData];
                return new JsonModel($result);
            } catch (Exception $ex) {
                $result = ['questions' => []];
            }
        }
        return new JsonModel($result);
    }

    /**
     * Remove o simulado selecionado
     * 
     * @return JsonModel
     */
    public function deleteAction()
    {
        $id = $this->params('id', false);
        
        if ($id) {
            try {
                $em = $this->getEntityManager();
                $exam = $em->getReference('SchoolManagement\Entity\Exam', $id);
                
                $em->remove($exam);
                $em->flush();
                
                $message = 'Simulado removido com sucesso.';
                return new JsonModel(array(
                    'message' => $message,
                    'callback' => array(
                        'examId' => $id,
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
                    $subject = $em->getReference('SchoolManagement\Entity\Subject',
                            $data['subject']);
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
                        $optsToRemove = new ArrayCollection($options->slice($numberOfOptions,
                                        $length));
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
                                        ->toRoute('school-management/school-exam',
                                                array('action' => 'question'));
                    }
                } else {
                    $typeBefore = $question->getExamQuestionType();
                    $form = new ExamQuestionForm($em, $typeBefore,
                            count($question->getAnswerOptions()->toArray()));

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
                return $this->redirect()->toRoute('school-management/school-exam',
                                array('action' => 'question'));
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

}
