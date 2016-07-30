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
use DateInterval;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Recruitment\Entity\Recruitment;
use Recruitment\Entity\Repository\RecruitmentRepository;
use Recruitment\Form\RecruitmentForm;
use RuntimeException;
use Zend\File\Transfer\Adapter\Http as HttpAdapter;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Permite Manipular processos seletivos de alunos
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class RecruitmentController extends AbstractEntityActionController
{

    const PUBLIC_NOTICE_DIR = './public/docs/';

    public function indexAction()
    {
        $em = $this->getEntityManager();

        $recruitments = $em->getRepository('Recruitment\Entity\Recruitment')->findAll();

        return new ViewModel(array(
            'recruitments' => $recruitments
        ));
    }

    /**
     * Redireciona para o edital do processo seletivo cujo identificador é $id.
     * 
     * @return Zend\Http\Response Redirecionamento para o pdf do edital.
     */
    public function publicNoticeAction()
    {
        $id = $this->params('id', false);

        if ($id) {

            $em = $this->getEntityManager();
            $recruitment = $em->getReference('Recruitment\Entity\Recruitment', $id);
            return $this->redirect()->toUrl('/docs/' . $recruitment->getRecruitmentPublicNotice());
        }
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

            if ($form->isValid()) {
                $data = $form->getData();

                try {

                    $targetDir = self::PUBLIC_NOTICE_DIR;

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
                    
                    if($data['recruitment_type'] == Recruitment::STUDENT_RECRUITMENT_TYPE) {
                        $recruitment
                            ->setRecruitmentSocioeconomicTarget($data['recruitmentSocioeconomicTarget'])
                            ->setRecruitmentVulnerabilityTarget($data['recruitmentVulnerabilityTarget'])
                            ->setRecruitmentStudentTarget($data['recruitmentStudentTarget'])
                            ;
                    }

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
                    unlink(self::PUBLIC_NOTICE_DIR . $recruitment->getRecruitmentPublicNotice());
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
