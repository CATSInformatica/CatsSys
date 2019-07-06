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
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Permite Manipular processos seletivos de alunos
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class RecruitmentController extends AbstractEntityActionController
{
    const PUBLIC_NOTICE_DIR = __DIR__ . '/../../../../public/docs/';

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
                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );
                $form->setData($data);

                if ($form->isValid()) {

                    $targetDir = self::PUBLIC_NOTICE_DIR;
                    $filename = $data['recruitment']['recruitmentYear']
                        . $data['recruitment']['recruitmentNumber']
                        . $data['recruitment']['recruitmentType']
                        . '.pdf';

                    if (!move_uploaded_file($data['recruitment']['recruitmentPublicNotice']['tmp_name'], $targetDir . $filename)) {
                        throw new \RuntimeException('Não foi possível salvar o arquivo');
                    }

                    $recruitment->setRecruitmentPublicNotice($filename);
                    $em->persist($recruitment);
                    $em->flush();

                    return $this->redirect()->toRoute('recruitment/recruitment', ['action' => 'index']);
                }
            }
            return new ViewModel(['form' => $form]);
        } catch (\Throwable $ex) {
            if ($ex instanceof UniqueConstraintViolationException) {
                return new ViewModel([
                    'message' => 'Este processo seletivo já foi cadastrado.',
                    'form' => null,
                ]);
            }

            return new ViewModel([
                'message' => $ex->getMessage(),
                'form' => null,
            ]);
        }
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
                $recruitment = $em->getReference(Recruitment::class, $id);
                $currentDate = new \DateTime();
                $beginDate = \DateTime::createFromFormat('d/m/Y', $recruitment->getRecruitmentBeginDate());
                if ($currentDate < $beginDate) {
                    unlink(RecruitmentFieldset::PUBLIC_NOTICE_DIR . $recruitment->getRecruitmentPublicNotice());
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
