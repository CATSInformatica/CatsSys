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
use Recruitment\Form\CpfFilter;
use Recruitment\Form\CpfForm;
use Recruitment\Form\PreInterviewForm;
use RuntimeException;
use Zend\File\Transfer\Adapter\Http as HttpAdapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Description of PreInterviewController
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class PreInterviewController extends AbstractActionController
{

    const PRE_INTERVIEW_DIR = './data/pre-interview/';

    use EntityManagerService;

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
            $form->setInputFilter(new CpfFilter());
            $form->setData($data);

            if ($form->isValid()) {
                $data = $form->getData();

                try {

                    $em = $this->getEntityManager();

                    $registration = $em->getRepository('Recruitment\Entity\Registration')
                        ->findOneByPersonCpf($data['cpf']);

                    if ($registration !== null) {
                        if ($registration->getRegistrationConvocationDate() instanceof DateTime) {

                            $studentContainer = new Container('pre_interview');
                            $studentContainer->offsetSet('regId', $registration->getRegistrationId());

                            return $this->redirect()->toRoute('recruitment/pre-interview',
                                    array(
                                    'action' => 'studentPreInterviewFiles'
                            ));
                        }

                        $message = 'Candidato não convocado';
                    } else {
                        $message = 'Candidato não encontrado.';
                    }
                } catch (Exception $ex) {
                    $message = 'Erro inesperado, não foi possível encontrar uma inscrição associada a este cpf.'
                        . $ex->getMessage();
                }
            } else {
                $message = '';
            }
        } else {
            $message = null;
        }

        return new ViewModel(array(
            'message' => $message,
            'form' => $form,
        ));
    }

    public function studentPreInterviewFilesAction()
    {
        $studentContainer = new Container('pre_interview');

        if (!$studentContainer->offsetExists('regId')) {
            return $this->redirect()->toRoute('recruitment/pre-interview', array('action' => 'index'));
        }

        $request = $this->getRequest();

        $files['personal'] = $files['income'] = $files['expendure'] = false;

        $prefix = self::PRE_INTERVIEW_DIR . $studentContainer->offsetGet('regId') . '_';

        $files['personal'] = file_exists($prefix . 'personal.pdf');
        $files['income'] = file_exists($prefix . 'income.pdf');
        $files['expendure'] = file_exists($prefix . 'expendure.pdf');

        $message = null;
        if ($request->isPost()) {
            if ($files['personal'] && $files['income'] && $files['expendure']) {
                $this->redirect()->toRoute('recruitment/pre-interview',
                    array(
                    'action' => 'studentPreInterviewForm'
                ));
            } else {
                $message = 'Para prosseguir, por favor, envie todos os arquivos.';
            }
        }

        return new ViewModel(array(
            'message' => $message,
            'files' => $files
        ));
    }

    public function studentPreInterviewFormAction()
    {
        $studentContainer = new Container('pre_interview');

        if ($studentContainer->offsetExists('regId')) {

            $form = new PreInterviewForm('Pre-interview');

            try {

                $em = $this->getEntityManager();

                $registration = $em->getRepository('Recruitment\Entity\Registration')->findOneBy(array(
                    'registrationId' => $studentContainer->offsetGet('regId')
                ));
            } catch (Exception $ex) {
                $registration = null;
            }

            return new ViewModel(array(
                'registration' => $registration,
                'form' => $form,
            ));
        }

        return $this->redirect()->toRoute('recruitment/pre-interview',
                array(
                'action' => 'index',
        ));
    }

    /**
     * 
     * @return JsonModel
     * @throws RuntimeException
     */
    public function studentFileUploadAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $studentContainer = new Container('pre_interview');
            if ($studentContainer->offsetExists('regId')) {
                $type = $this->params('file', false);

                try {

                    if (!$type) {
                        throw new RuntimeException('O arquivo enviado não é de um dos tipos especificados.');
                    }

                    $file = $request->getFiles()->$type;

                    if ($file === null) {
                        throw new RuntimeException('Nenhum arquivo enviado.');
                    }

                    $targetDir = self::PRE_INTERVIEW_DIR;
                    $filename = $studentContainer->offsetGet('regId') . '_' . $type . '.pdf';
                    $targetFile = $targetDir . $filename;

                    $uploadAdapter = new HttpAdapter();

                    $uploadAdapter->addFilter('File\Rename',
                        array(
                        'target' => $targetFile,
                        'overwrite' => true
                    ));

                    $uploadAdapter->setDestination($targetDir);

                    if (!$uploadAdapter->receive($type)) {
                        throw new RuntimeException(implode('\n', $uploadAdapter->getMessages()));
                    }

                    return new JsonModel(array(
                        'message' => 'Arquivo salvo com sucesso.',
                        'file' => $file,
                        'target' => $targetFile
                    ));
                } catch (Exception $ex) {

                    if ($ex instanceof RuntimeException) {
                        $message = 'Erro: ' . $ex->getMessage();
                    } else {
                        $message = 'Erro inesperado. Entre em contato com o administrador do sistema. '
                            . $ex->getMessage();
                    }

                    return new JsonModel(array(
                        'message' => $message,
                    ));
                }
            }

            return new JsonModel(array(
                'message' => 'Sessão expirada. Por favor, retorne a página de inserção de cpf e tente novamente.',
            ));
        }

        return $this->redirect()->toRoute('recruitment/pre-interview', array(
                'index'
        ));
    }

    public function getUploadedFileAction()
    {
        $this->layout('empty/layout');
        $file = $this->params('file', false);
        $rid = $this->params('rid', false);

        if (!$rid) {
            $studentContainer = new Container('pre_interview');
            if ($studentContainer->offsetExists('regId')) {
                $rid = $studentContainer->offsetGet('regId');
            } else {
                return $this->redirect()->toRoute('authorization/index');
            }
        }

        if ($file) {

            $pdf = self::PRE_INTERVIEW_DIR . $rid . '_' . $file . '.pdf';

            if (file_exists($pdf) !== false) {
                $response = $this->getResponse();
                $response->getHeaders()->addHeaderLine('Content-Type', 'application/pdf');
                $editalContent = file_get_contents($pdf);
                $response->setStatusCode(200);
                $response->setContent($editalContent);
                return $response;
            }

            $message = 'Arquivo não encontrado.';
        } else {
            $message = 'Nenhum tipo de arquivo foi especificado.';
        }

        return new ViewModel(array(
            'message' => $message,
        ));
    }

}
