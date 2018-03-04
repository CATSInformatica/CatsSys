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

    /**
     * Cria novos processos seletivos.
     * 
     * @return ViewModel
     * @throws RuntimeException
     * @todo Utilizar o Hydrator também para a coleção de cargos (openJobs), se possível.
     */
    public function createAction()
    {

        try {
            $request = $this->getRequest();
            $em = $this->getEntityManager();

            $form = new RecruitmentForm($em);
            $recruitment = new Recruitment();
            $form->bind($recruitment);
            
            if ($request->isPost()) {
                $fileContainer = $request->getFiles()->toArray();
                $file = $fileContainer['recruitment']['recruitmentPublicNotice'];
                $data = $request->getPost();
                $form->setData($data);
                 
                if ($form->isValid() && !$file['error'] && $file['size']) {

                    try {

                        $targetDir = self::PUBLIC_NOTICE_DIR;

                        $filename = $data['recruitment']['recruitmentYear']
                            . $data['recruitment']['recruitmentNumber']
                            . $data['recruitment']['recruitmentType']
                            . '.pdf';

                        $targetFile = $targetDir . $filename;

                        if (file_exists($targetFile)) {
                            throw new RuntimeException('Arquivo do edital já existe. '
                            . 'Por favor entre em contato com o administrador do sistema.');
                        }

                        $recruitment->setRecruitmentPublicNotice($filename);

                        $em->persist($recruitment);
                        $em->flush();
                        
                        $uploadAdapter = new HttpAdapter();

                        $uploadAdapter->addFilter('File\Rename', array(
                            'target' => $targetFile,
                            'overwrite' => false
                        ));

                        $uploadAdapter->setDestination($targetDir);

                        if (!$uploadAdapter->receive($fileContainer['name'])) {
                            $messages = implode('\n', $uploadAdapter->getMessages());
                            throw new \RuntimeException($messages);
                        }

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

                return new ViewModel(array(
                    'message' => ($file['error'] || !$file['size']) ? 'O upload do edital não pode ser feito. Por favor tente novamente.' : null,
                    'form' => $form
                ));
            }

            return new ViewModel(array(
                'form' => $form
            ));
        } catch (\Throwable $ex) {
            return new ViewModel(array(
                'form' => null,
                'message' => $ex->getMessage(),
            ));
        }
    }

    // public function editAction()
    // {
    //     try {
    //         $id = $this->params('id', false);
    //         $em = $this->getEntityManager();

    //         if ($id) {
    //             $form = new RecruitmentForm($em);
    //             $recruitment = $em->find('Recruitment\Entity\Recruitment', $id);
                
    //             $currentDate = new \DateTime();
    //             $beginDate = \DateTime::createFromFormat('d/m/Y', $recruitment->getRecruitmentBeginDate());
    //             if ($beginDate <= $currentDate) {
    //                 return new ViewModel(array(
    //                     'message' => 'Não é possível editar processos seletivos concluídos ou em andamento. Por favor, consulte o administrador do sistema.',
    //                     'form' => null,
    //                 ));
    //             }
                
    //             $form->bind($recruitment);
            
    //             $openJobsIds = [];
    //             foreach ($recruitment->getOpenJobs() as $openJob) {
    //                 $openJobsIds[] = $openJob->getJobId();
    //             }
    //             $form->get('recruitment')->get('openJobs')->setValue($openJobsIds);
            
    //             $request = $this->getRequest();
    //             if ($request->isPost()) {
    //                 $publicNotice = $recruitment->getRecruitmentPublicNotice();
    //                 $fileContainer = $request->getFiles()->toArray();
    //                 $file = $fileContainer['recruitment']['recruitmentPublicNotice'];
    //                 $data = $request->getPost();
    //                 $form->setData($data);

    //                 if ($form->isValid() && !$file['error'] && $file['size']) {
    //                     try {

    //                         $filename = $data['recruitment']['recruitmentYear']
    //                             . $data['recruitment']['recruitmentNumber']
    //                             . $data['recruitment']['recruitmentType']
    //                             . '.pdf';

    //                         $recruitment->setRecruitmentPublicNotice($filename);

    //                         foreach ($data['recruitment']['openJobs'] as $jobId) {
    //                             $job = $em->find('AdministrativeStructure\Entity\Job', $jobId);
    //                             $recruitment->addOpenJob($job);
    //                         }

    //                         $em->persist($recruitment);
    //                         $em->flush();

    //                         $targetDir = self::PUBLIC_NOTICE_DIR;
    //                         $targetFile = $targetDir . $filename;

    //                         if (file_exists($targetFile)) {
    //                             unlink(self::PUBLIC_NOTICE_DIR . $publicNotice);
    //                         }

    //                         $uploadAdapter = new HttpAdapter();

    //                         $uploadAdapter->addFilter('File\Rename', array(
    //                             'target' => $targetFile,
    //                             'overwrite' => true
    //                         ));

    //                         $uploadAdapter->setDestination($targetDir);

    //                         if (!$uploadAdapter->receive($fileContainer['name'])) {
    //                             $messages = implode('\n', $uploadAdapter->getMessages());
    //                             throw new \RuntimeException($messages);
    //                         }

    //                         return $this->redirect()->toRoute('recruitment/recruitment', array('action' => 'index'));
    //                     } catch (\Thrownable $ex) {
    //                         if ($ex instanceof UniqueConstraintViolationException) {
    //                             return new ViewModel(array(
    //                                 'message' => 'Este processo seletivo já foi cadastrado.',
    //                                 'form' => null,
    //                             ));
    //                         }

    //                         return new ViewModel(array(
    //                             'message' => 'Erro inesperado: ' . $ex->getMessage(),
    //                             'form' => null,
    //                         ));
    //                     }
    //                 }

    //                 return new ViewModel(array(
    //                     'message' => ($file['error'] || !$file['size']) ? 'O upload do edital não pode ser feito. Por favor tente novamente.' : null,
    //                     'form' => $form
    //                 ));
    //             }

    //             return new ViewModel(array(
    //                 'form' => $form,
    //                 'message' => null,
    //             ));
    //         }

    //         return new ViewModel(array(
    //             'form' => null,
    //             'message' => 'Nenhum Processo Seletivo foi escolhido',
    //         ));
    //     } catch (\Exception $ex) {
    //         return new ViewModel(array(
    //             'form' => null,
    //             'message' => $ex->getMessage(),
    //         ));
    //     }
    // }

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
                $currentDate = new \DateTime();
                $beginDate = \DateTime::createFromFormat('d/m/Y', $recruitment->getRecruitmentBeginDate());
                if ($currentDate < $beginDate) {
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
            } catch (\Exception $ex) {
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
            $currentDate = new \DateTime();
            $date = clone $currentDate;
            $date->add(new \DateInterval('P' . RecruitmentRepository::RECRUITMENT_DAYOFFSET . 'D'));

            $studentRecruitment = $em->getRepository('Recruitment\Entity\Recruitment')
                ->findNotEndedByTypeAsArray(Recruitment::STUDENT_RECRUITMENT_TYPE, $currentDate);

            $srHasOffset = false;

            $volunteerRecruitment = $em->getRepository('Recruitment\Entity\Recruitment')
                ->findNotEndedByTypeAsArray(Recruitment::VOLUNTEER_RECRUITMENT_TYPE, $currentDate);
            $vrHasOffset = false;

            // nenhum processo seletivo de alunos aberto, buscar por processos seletivos de alunos 
            // abertos dentro dos próximos dias
            if ($studentRecruitment === null) {
                $studentRecruitment = $em->getRepository('Recruitment\Entity\Recruitment')
                    ->findNotEndedByTypeAsArray(Recruitment::STUDENT_RECRUITMENT_TYPE, $date);
                $srHasOffset = true;
            }

            // nenhum processo seletivo de voluntários aberto, buscar por processos seletivos de voluntários 
            // abertos dentro de self::RECRUITMENT_DAYOFFSET dias
            if ($volunteerRecruitment === null) {
                $volunteerRecruitment = $em->getRepository('Recruitment\Entity\Recruitment')
                    ->findByTypeAndBetweenBeginAndEndDatesAsArray(Recruitment::VOLUNTEER_RECRUITMENT_TYPE, $date);
                $vrHasOffset = true;
            }

            if ($studentRecruitment === null && $volunteerRecruitment == null) {
                return new JsonModel([
                    'recruitments' => null,
                ]);
            }

            $srSubscriptionLink = false;
            if (!$srHasOffset && $currentDate <= $studentRecruitment['recruitmentEndDate']) {
                $srSubscriptionLink = true;
            }

            $vrSubscriptionLink = false;
            if (!$vrHasOffset && $currentDate <= $volunteerRecruitment['recruitmentEndDate']) {
                $vrSubscriptionLink = true;
            }

            return new JsonModel([
                'recruitments' => [
                    'student' => [
                        'content' => $studentRecruitment,
                        'offset' => $srHasOffset,
                        'showSubscriptionLink' => $srSubscriptionLink,
                    ],
                    'volunteer' => [
                        'content' => $volunteerRecruitment,
                        'offset' => $vrHasOffset,
                        'showSubscriptionLink' => $vrSubscriptionLink,
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
