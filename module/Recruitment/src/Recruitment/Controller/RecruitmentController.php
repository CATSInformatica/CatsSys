<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Controller;

use Recruitment\Entity\Recruitment;
use Recruitment\Form\RecruitmentFilter;
use Recruitment\Form\RecruitmentForm;
use Zend\Mvc\Controller\AbstractActionController;
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

    public function createAction()
    {
        $request = $this->getRequest();


        $form = new RecruitmentForm();

        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            $form->setInputFilter(new RecruitmentFilter());
            if ($form->isValid()) {
                $data = $form->getData();

                try {

                    $em = $this->getEntityManager();

                    $recruitment = new Recruitment();
                    $recruitment->setRecruitmentNumber($data['recruitment_number'])
                            ->setRecruitmentYear($data['recruitment_year'])
                            ->setRecruitmentBeginDate(new \DateTime($data['recruitment_begindate']))
                            ->setRecruitmentEndDate(new \DateTime($data['recruitment_enddate']))
                            ->setRecruitmentPublicNotice($data['recruitment_public_notice'])
                            ->setRecruitmentType($data['recruitment_type']);

                    $em->persist($recruitment);
                    $em->flush();

                    return $this->redirect()->toRoute('recruitment/recruitment', array('action' => 'index'));
                } catch (\Exception $ex) {
                    return new ViewModel(array(
                        'message' => $ex->getCode() . ': ' . $ex->getMessage()
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
        $id = $this->params()->fromRoute('id');

        if ($id !== null) {
            try {
                $em = $this->getEntityManager();
                $recruitment = $em->getReference('Recruitment\Entity\Recruitment', $id);
                $currentDate = new \DateTime('now');
                if ($currentDate < $recruitment->getRecruitmentBeginDate()) {
                    $em->remove($recruitment);
                    $em->flush();
                    return $this->redirect()->toRoute('recruitment/recruitment', array(
                                'action' => 'index'
                    ));
                }

                return new ViewModel(array(
                    'message' => 'Não é possivel remover processos seletivos em andamento.'
                    . 'Entre em contato com o administrador do sistema'
                ));
            } catch (\Exception $ex) {
                return new ViewModel(array(
                    'message' => $ex->getCode() . ': ' . $ex->getMessage()
                ));
            }
        }

        return new ViewModel(array(
            'message' => 'Nenhum processo seletivo escolhido'
        ));
    }

}
