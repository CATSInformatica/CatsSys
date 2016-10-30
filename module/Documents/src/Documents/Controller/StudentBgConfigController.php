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

namespace Documents\Controller;

use Database\Controller\AbstractEntityActionController;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Documents\Entity\StudentBgConfig;
use Documents\Form\StudentBgConfigForm;
use Exception;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Description of StudentBgConfigController
 * CRUD da configuração de fundo
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class StudentBgConfigController extends AbstractEntityActionController
{

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
     * Exibe um formulário para criação de uma configuração de fundo
     * 
     * @return ViewModel
     */
    public function createAction()
    {
        $request = $this->getRequest();
        $em = $this->getEntityManager();

        try {
            $bgConfig = new StudentBgConfig();
            $form = new StudentBgConfigForm($em);
            $form->bind($bgConfig);
            
            if ($request->isPost()) {
                $file = $request->getFiles()->toArray();
                $post = array_merge_recursive(
                    $request->getPost()->toArray(), 
                    $file
                );
                $form->setData($post);
                
                if ($form->isValid()) {
                    $bgImgNewName = 'bg' . time() . '.png';
                    move_uploaded_file($file['bg_img']['tmp_name'], './public/img/' . $bgImgNewName);
                    chmod('./public/img/' . $bgImgNewName, 0775);
                    $bgConfig->setStudentBgConfigImg($bgImgNewName);

                    $em->persist($bgConfig);
                    $em->flush();
                    return $this->redirect()->toRoute('documents/student-bg-config', array('action' => 'index'));
                }
            }
            return new ViewModel(array(
                'message' => null,
                'form' => $form,
            ));
        } catch (Exception $ex) {
            if ($ex instanceof UniqueConstraintViolationException) {
                return new ViewModel(array(
                    'message' => 'Já existe uma configuração com essa frase.',
                    'form' => null,
                ));
            } else {
                return new ViewModel(array(
                    'message' => 'Erro inesperado. Entre com contato com o administrador do sistema.<br> Erro: ' . $ex->getMessage(),
                    'form' => null,
                ));
            }
        }
    } 

    /**
     * Exibe um formulário para edição da configuração de fundo
     * 
     * @return ViewModel
     */
    public function editAction()
    {
        $bgConfigId = $this->params('id', false);
        
        if ($bgConfigId) {
            $request = $this->getRequest();
            $em = $this->getEntityManager();

            try {
                $bgConfig = $em->find('Documents\Entity\StudentBgConfig', $bgConfigId);
                $form = new StudentBgConfigForm($em, false /*img not required*/);
                $form->bind($bgConfig);
                $form->get('submit')->setAttribute('value', 'Editar configuração de fundo');
                $img = $bgConfig->getStudentBgConfigImg();

                if ($request->isPost()) {
                    $file = $request->getFiles()->toArray();
                    $post = array_merge_recursive(
                        $request->getPost()->toArray(), 
                        $file
                    );
                    $form->setData($post);
                    
                    if ($form->isValid()) {
                        // Outra imagem foi carregada
                        if (isset($file['bg_img']) && !empty($file['bg_img']['tmp_name'])) {
                            unlink('./public/img/' . $img);
                            
                            $bgImgNewName = 'bg' . time() . '.png';
                            move_uploaded_file($file['bg_img']['tmp_name'], './public/img/' . $bgImgNewName);
                            chmod('./public/img/' . $bgImgNewName, 0775);
                            $bgConfig->setStudentBgConfigImg($bgImgNewName);
                        }
                        
                        $em->persist($bgConfig);
                        $em->flush();
                        return $this->redirect()->toRoute('documents/student-bg-config', array('action' => 'index'));
                    }
                }
                return new ViewModel(array(
                    'message' => null,
                    'form' => $form,
                ));
            } catch (Exception $ex) {
                if ($ex instanceof UniqueConstraintViolationException) {
                    return new ViewModel(array(
                        'message' => 'Já existe uma configuração com essa frase.',
                        'form' => null,
                    ));
                } else {
                    return new ViewModel(array(
                        'message' => 'Erro inesperado. Entre com contato com o administrador do sistema.<br> Erro: ' . $ex->getMessage(),
                        'form' => null,
                    ));
                }
            }
        }
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
            $message = 'Nenhuma configuração selecionada.';
            return new JsonModel(array(
                'message' => $message
            ));
        }
    }

}
