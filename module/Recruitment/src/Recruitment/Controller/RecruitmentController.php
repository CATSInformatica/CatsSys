<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Controller;

use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Recruitment\Entity\Recruitment;
use Recruitment\Form\RecruitmentFilter;
use Recruitment\Form\RecruitmentForm;
use RuntimeException;
use Zend\File\Transfer\Adapter\Http as HttpAdapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Description of RecruitmentController
 *
 * @author marcio
 */
class RecruitmentController extends AbstractActionController
{

    use \Database\Service\EntityManagerService;

    public function indexAction()
    {
        $em = $this->getEntityManager();

        $recruitments = $em->getRepository('Recruitment\Entity\Recruitment')->findAll();

        return new ViewModel(array(
            'recruitments' => $recruitments
        ));
    }

    public function editalAction()
    {
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/pdf');
        $id = $this->params('id', false);

        if ($id) {

            $em = $this->getEntityManager();
            $recruitment = $em->getReference('Recruitment\Entity\Recruitment', $id);
            $edital = './data/edital/' . $recruitment->getRecruitmentPublicNotice();

            if (file_exists($edital) !== false) {
                $editalContent = file_get_contents($edital);

                $response->setStatusCode(200);
                $response->setContent($editalContent);
            }
        }

        return $response;
    }

    public function createAction()
    {
        $request = $this->getRequest();


        $form = new RecruitmentForm();

        if ($request->isPost()) {
            $file = $request->getFiles()->toArray();

            $data = array_merge_recursive(
                    $request->getPost()->toArray(), $file
            );

            $form->setData($data);
            $form->setInputFilter(new RecruitmentFilter());

            if ($form->isValid()) {
                $data = $form->getData();

                try {

                    $targetDir = './data/edital/';

                    $filename = $data['recruitment_year']
                            . $data['recruitment_number']
                            . $data['recruitment_type']
                            . '.pdf';

                    $targetFile = $targetDir . $filename;


                    if (file_exists($targetFile)) {
                        throw new RuntimeException('Arquivo do edital já existe. '
                        . 'Por favor entre em contato com o administrador do sistema.');
                    }

                    $uploadAdapter = new HttpAdapter();

                    $uploadAdapter->addFilter('File\Rename', array(
                        'target' => $targetFile,
                        'overwrite' => false
                    ));

                    $uploadAdapter->setDestination($targetDir);

                    if (!$uploadAdapter->receive($file['name'])) {
                        $messages = implode('\n', $uploadAdapter->getMessages());
                        throw new \RuntimeException($messages);
                    }

                    $em = $this->getEntityManager();

                    $recruitment = new Recruitment();

                    $recruitment->setRecruitmentNumber($data['recruitment_number'])
                            ->setRecruitmentYear($data['recruitment_year'])
                            ->setRecruitmentBeginDate(new DateTime($data['recruitment_begindate']))
                            ->setRecruitmentEndDate(new DateTime($data['recruitment_enddate']))
                            ->setRecruitmentPublicNotice($filename)
                            ->setRecruitmentType($data['recruitment_type']);

                    $em->persist($recruitment);
                    $em->flush();

                    return $this->redirect()->toRoute('recruitment/recruitment', array('action' => 'index'));
                } catch (Exception $ex) {
                    if ($ex instanceof UniqueConstraintViolationException) {
                        return new ViewModel(array(
                            'message' => 'Este processo seletivo já foi cadastrado.',
                            'form' => null,
                        ));
                    }

                    return new ViewModel(array(
                        'message' => $ex->getCode() . ': ' . $ex->getMessage(),
                        'form' => null,
                    ));
                }
            }
        }

        return new ViewModel(array(
            'form' => $form
        ));
    }

    public function deleteAction()
    {
        $id = $this->params('id', false);

        if ($id) {
            try {
                $em = $this->getEntityManager();
                $recruitment = $em->getReference('Recruitment\Entity\Recruitment', $id);
                $currentDate = new DateTime('now');
                if ($currentDate < $recruitment->getRecruitmentBeginDate()) {
                    $em->remove($recruitment);
                    $em->flush();
                    return $this->redirect()->toRoute('recruitment/recruitment', array(
                                'action' => 'index'
                    ));
                }

                return new JsonModel(array(
                    'message' => 'Não é possivel remover processos seletivos em andamento.'
                    . ' Entre em contato com o administrador do sistema.'
                ));
            } catch (Exception $ex) {
                return new JsonModel(array(
                    'message' => $ex->getCode() . ': ' . $ex->getMessage()
                ));
            }
        }

        return new JsonModel(array(
            'message' => 'Nenhum processo seletivo escolhido.'
        ));
    }

}
