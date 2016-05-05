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
use Exception;
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
     * Exibe uma tabela com todos os simulados gerados
     * 
     * @return ViewModel
     */
    public function indexAction()
    {
        try {
            $em = $this->getEntityManager();
            $baseSubjects = $em->getRepository('SchoolManagement\Entity\Subject')->findBy(array('parent' => null));

            return new ViewModel(array(
                'message' => null,
                'baseSubjects' => $baseSubjects,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema.' . 'Erro: ' . $ex->getMessage(),
                'baseSubjects' => null,
            ));
        }
    }

    /**
     * Retorna todas as questões cadastradas para a matéria $data['subject'] do tipo $data['questionType']
     * 
     * @return JsonModel
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
                        $answers = [];
                        $answerOptions = $q->getAnswerOptions()->toArray();
                        foreach ($answerOptions as $ao) {
                            $answers[] = $ao->getExamAnswerDescription();
                        }
                        $result[] = array(
                            'questionId' => $q->getExamQuestionId(),
                            'questionEnunciation' => $q->getExamQuestionEnunciation(),
                            'questionAnswers' => $answers,
                            'questionAnswersStr' => implode("<br>", $answers),
                        );
                    }
                }
            } catch (Exception $ex) {
                $result[] = array(
                    'questionId' => -1,
                    'questionEnunciation' => 'Erro: ' . $ex,
                    'questionAnswers' => '-',
                    'questionAnswersStr' => null,
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
                    // nómero de alternativas antes da edição > número de alternativas depois da edição.
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
                        $correctAnswer = (int) $data['correctAnswer'];

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
                $correctAnswer = (int) $data['correctAnswer'];
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

}
