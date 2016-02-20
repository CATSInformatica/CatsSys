<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Documents\Controller;

use Exception;
use Documents\Entity\StudentBgConfig;
use Documents\Form\StudentBgConfigForm;
use Documents\Form\StudentBgConfigFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Description of StudentBgConfigController
 * CRUD da configuração de fundo
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class StudentBgConfigController extends AbstractActionController
{

    use \Database\Service\EntityManagerService;

    /**
     * Exibe em uma tabela todas as configurações cadastradas
     * 
     * @return ViewModel
     */
    public function indexAction()
    {
        $message = null;
        try {
            $em = $this->getEntityManager();
            $configs = $em->getRepository('Documents\Entity\StudentBgConfig')->findAll();
        } catch (\Exception $ex) {
            $message = $ex->getMessage();
        }

        return new ViewModel([
            'configs' => $configs,
            'message' => $message,
        ]);
    }

    /**
     * Grava no banco dados uma configuração de fundo 
     * 
     * @return ViewModel
     */
    public function createAction()
    {
        $request = $this->getRequest();
        $form = new StudentBgConfigForm('Student Background Configuration Form');
        $message = null;

        if ($request->isPost()) {

            $post = array_merge_recursive(
                    $request->getPost()->toArray(), 
                    $request->getFiles()->toArray()
            );

            $form->setData($post);
            $form->setInputFilter(new StudentBgConfigFilter());

            if ($form->isValid()) {
                $data = $form->getData();

                try {
                    $em = $this->getEntityManager();

                    $bgImgNewName = 'bg' . time() . '.png';
                    move_uploaded_file($data['bg_img']['tmp_name'], './public/img/' . $bgImgNewName);
                    chmod('./public/img/' . $bgImgNewName, 0775);

                    $bgConfig = new StudentBgConfig();
                    $bgConfig->setStudentBgConfigPhrase($data['bg_phrase'])
                            ->setStudentBgConfigAuthor($data['bg_author'])
                            ->setStudentBgConfigImg($bgImgNewName);

                    $em->persist($bgConfig);
                    $em->flush();
                    $this->redirect()->toRoute('documents/student-bg-config', 
                            array('action' => 'index'));
                } catch (Exception $ex) {
                    if ($ex instanceof UniqueConstraintViolationException) {
                        $message = 'Esta configuração já existe.';
                    } else {
                        $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                                'Erro: ' . $ex->getMessage();
                    }
                }
            }
        }
        return new ViewModel(array(
            'message' => $message,
            'form' => $form,
        ));
    }

    /**
     * Remove do banco de dados a configuração de fundo selecionada
     * 
     * @return JsonModel
     */
    public function deleteAction()
    {
        $id = $this->params('id', false);

        if ($id) {
            try {
                $em = $this->getEntityManager();

                $bgConfig = $em->getReference('Documents\Entity\StudentBgConfig', $id);
                unlink('./public/img/' . $bgConfig->getStudentBgConfigImg());
                $em->remove($bgConfig);
                $em->flush();
                $message = 'Configuração removida com sucesso.';
            } catch (Exception $ex) {
                $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                        'Erro: ' . $ex->getMessage();
            }
            return new JsonModel(array(
                'message' => $message,
                'callback' => array(
                    'bgConfigId' => $id,
                ),
            ));
        } else {
            $message = 'Nenhuma configuração selecionda.';
            return new JsonModel(array(
                'message' => $message
            ));
        }
    }

}
