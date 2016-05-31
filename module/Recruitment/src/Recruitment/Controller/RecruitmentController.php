<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Controller;

use Database\Controller\AbstractEntityActionController;
use DateInterval;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Recruitment\Entity\Recruitment;
use Recruitment\Entity\Repository\RecruitmentRepository;
use Recruitment\Form\RecruitmentFilter;
use Recruitment\Form\RecruitmentForm;
use RuntimeException;
use Zend\File\Transfer\Adapter\Http as HttpAdapter;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Description of RecruitmentController
 *
 * @author marcio
 */
class RecruitmentController extends AbstractEntityActionController
{

    const EDITAL_DIR = './data/edital/';

    public function indexAction()
    {
        $em = $this->getEntityManager();

        $recruitments = $em->getRepository('Recruitment\Entity\Recruitment')->findAll();

        return new ViewModel(array(
            'recruitments' => $recruitments
        ));
    }

    public function publicNoticeAction()
    {
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/pdf');
        $id = $this->params('id', false);

        if ($id) {

            $em = $this->getEntityManager();
            $recruitment = $em->getReference('Recruitment\Entity\Recruitment', $id);
            $edital = self::EDITAL_DIR . $recruitment->getRecruitmentPublicNotice();

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

                    $targetDir = self::EDITAL_DIR;

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

                    $uploadAdapter->addFilter('File\Rename',
                        array(
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
                        'message' => $ex->getMessage(),
                        'form' => null,
                    ));
                }
            }
        }

        return new ViewModel(array(
            'form' => $form
        ));
    }

    /**
     * Remove um processo seletivo cadastrado se ele ainda não tiver iniciado.
     * 
     * @return JsonModel
     */
    public function deleteAction()
    {
        $id = $this->params('id', false);

        if ($id) {
            try {
                $em = $this->getEntityManager();
                $recruitment = $em->getReference('Recruitment\Entity\Recruitment', $id);
                $currentDate = new DateTime('now');
                if ($currentDate < $recruitment->getRecruitmentBeginDate()) {
                    unlink(self::EDITAL_DIR . $recruitment->getRecruitmentPublicNotice());
                    $em->remove($recruitment);
                    $em->flush();

                    return new JsonModel(array(
                        'message' => 'processo seletivo removido com sucesso.'
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

    /**
     * Busca por processos seletivos abertos ou que estão para abrir no intervalo dado por 
     * RecruitmentRepository::RECRUITMENT_DAYOFFSET a partir da data atual.
     * 
     * @return JsonModel
     */
    public function getLastOpenedAction()
    {
        try {

            $em = $this->getEntityManager();

            $date = new \DateTime();
            $interval = new DateInterval('P' . RecruitmentRepository::RECRUITMENT_DAYOFFSET . 'D');

            $studentRecruitment = $em->getRepository('Recruitment\Entity\Recruitment')
                ->findByTypeAndBetweenBeginAndEndDatesAsArray(Recruitment::STUDENT_RECRUITMENT_TYPE, $date);
            $srHasOffset = false;

            $volunteerRecruitment = $em->getRepository('Recruitment\Entity\Recruitment')
                ->findByTypeAndBetweenBeginAndEndDatesAsArray(Recruitment::VOLUNTEER_RECRUITMENT_TYPE, $date);
            $vrHasOffiset = false;

            // nenhum processo seletivo de alunos aberto, buscar por processos seletivos de alunos 
            // abertos dentro dos próximos dias
            if ($studentRecruitment === null) {
                $date->add($interval);

                $studentRecruitment = $em->getRepository('Recruitment\Entity\Recruitment')
                    ->findByTypeAndBetweenBeginAndEndDatesAsArray(Recruitment::STUDENT_RECRUITMENT_TYPE, $date);
                $srHasOffset = true;
            }

            // nenhum processo seletivo de voluntários aberto, buscar por processos seletivos de voluntários 
            // abertos dentro dos próximos dias
            if ($volunteerRecruitment === null) {
                $date->add($interval);

                $volunteerRecruitment = $em->getRepository('Recruitment\Entity\Recruitment')
                    ->findByTypeAndBetweenBeginAndEndDatesAsArray(Recruitment::VOLUNTEER_RECRUITMENT_TYPE, $date);
                $vrHasOffiset = true;
            }

            if ($studentRecruitment === null && $volunteerRecruitment == null) {
                return new JsonModel([
                    'recruitments' => null,
                ]);
            }

            return new JsonModel([
                'recruitments' => [
                    'student' => [
                        'content' => $studentRecruitment,
                        'offset' => $srHasOffset,
                    ],
                    'volunteer' => [
                        'content' => $volunteerRecruitment,
                        'offset' => $vrHasOffiset,
                    ],
                ]
            ]);
        } catch (Exception $ex) {
            return new JsonModel([
                'recruitments' => null,
                'error' => $ex->getMessage(),
            ]);
        }
    }

}
