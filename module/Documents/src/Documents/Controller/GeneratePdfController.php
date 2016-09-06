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
use DateTime;
use Documents\Form\StudentIdCardForm;
use Documents\Form\StudentsBoardFilter;
use Documents\Form\StudentsBoardForm;
use Documents\Model\StudentIdCardPdf;
use SebastianBergmann\RecursionContext\Exception;
use Zend\View\Model\ViewModel;


/**
 * Description of GeneratePdfController
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class GeneratePdfController extends AbstractEntityActionController
{ 
        
    /**
     * Retorna um array com dois arrays. Um contendo as configurações de fundo 
     * formatadas e o outro contendo as ids das configurações
     * 
     * @return array
     *  [
     *      'formattedBgConfigs' => [
     *          <bgConfig-id> => [
     *              'phrase' => <bgConfig-phrase>,
     *              'author' => <bgConfig-author>,
     *              'bg_img_url' => <bgConfig-img>,
     *          ], 
     *          ...
     *      ],
     *      'bgConfigIds' => [
     *          <bgConfig-id> => <bgConfig-id>,
     *          ...
     *      ]
     *  ]
     */
    protected function getBgConfigsInfo($configs) {
        $bgConfigIds = [];
        $formattedBgConfigs = [];
        
        foreach ($configs as $config) {
            $bgConfigIds[$config->getStudentBgConfigId()] = $config->getStudentBgConfigId();
            $formattedBgConfigs[$config->getStudentBgConfigId()] = [
                'phrase' => $config->getStudentBgConfigPhrase(),
                'author' => $config->getStudentBgConfigAuthor(),
                'bg_img_url' => $config->getStudentBgConfigImg(),
            ];
        }
        
        return [
            'formattedBgConfigs' => $formattedBgConfigs,
            'bgConfigIds' => $bgConfigIds
        ];
    }
    
    /**
     * Retorna um array com dois arrays. Um contendo os alunos matriculados 
     * na turma e o outro contendo os ids das turmas
     * 
     * @return array
     *  [
     *      'classEnrollments' => [
     *          <class-id> => <class-enrollments-array>
     *          ...
     *      ],
     *      'classNames' => [
     *          <class-id> => <class-name>,
     *          ...
     *      ]
     *  ]
     */
    protected function getClassesInfo($classes) {
        $classNames = [];
        $classEnrollments = [];
        
        foreach ($classes as $class) {
            $classNames[$class->getClassId()] = $class->getClassName();
            $classEnrollments[$class->getClassId()] = $class->getEnrollments()->toArray();
        }
        
        return [
            'classEnrollments' => $classEnrollments,
            'classNames' => $classNames
        ]; 
    }    
    
    /**
     * Exibe um formulário para geração das carteirinhas dos alunos
     * Quando o formulário é submetido as carteirinhas são geradas e exibidas no navegador
     * 
     * @return ViewModel
     */
    public function studentIdCardAction()
    {
        $request = $this->getRequest(); 
        $em = $this->getEntityManager();
        
        try {
            //  Pega todas as configurações de fundo cadastradas e seleciona os id's 
            //  para mandar para o formulário
            $bgConfigs = $em->getRepository('Documents\Entity\StudentBgConfig')
                    ->findAll();
            $bgConfigsInfo = $this->getBgConfigsInfo($bgConfigs);
            $bgConfigIds = $bgConfigsInfo['bgConfigIds'];
            $formattedBgConfigs = $bgConfigsInfo['formattedBgConfigs'];
            
            //  Pega todas as turmas cadastradas e seleciona os nomes para mandar
            //  para o formulário
            $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')
                    ->findAll();
            $classesInfo = $this->getClassesInfo($classes);
            $classNames = $classesInfo['classNames'];
            $classEnrollments = $classesInfo['classEnrollments'];
            
            //  Cria o formulário e o processa
            $form = new StudentIdCardForm($bgConfigIds, $classNames);
            if ($request->isPost()) {
                $form->setData($request->getPost()->toArray());

                if ($form->isValid()) {
                    $data = $form->getData();
                    
                    $people = [];
                    
                    //  Obtém as informações dos alunos da turma selecionada
                    $enrolls = $classEnrollments[$data['class_id']];
                    foreach($enrolls as $enroll) {
                        //  Aluno não encerrou matrícula
                        if ($enroll->getEnrollmentEndDate() === null) {
                            $people[] = $enroll->getRegistration()->getPerson();
                        }
                    } 
                    // Turma vazia
                    if (empty($people)) {
                        return new ViewModel(array(
                            'message' => 'A turma selecionada não possui alunos cadastrados.',
                            'form' => $form,
                            'pdf' => null
                        ));                         
                    }
                    // Agrupa as informações dos alunos da turma
                    foreach($people as $person) {
                        $students[] = [
                            'name' => $person->getPersonName(),
                            'rg' => $person->getPersonRg(),
                            'img_url' => $person->getPersonPhoto()
                        ];
                    }
                    
                    //  Concatena a data de validade da carteirinha às configurações
                    $config = array_merge_recursive(
                        $formattedBgConfigs[$data['config_id']],
                        ['expiry' => new DateTime($data['expiry_date'])]
                    );
                    
                    // Instancia um objeto da classe StudentIdPdf e gera as carteirinhas
                    $pdfHandler = new StudentIdCardPdf($config, $students);
                    $pdf = $pdfHandler->generatePdf();
                    return new ViewModel(array(
                        'message' => null,
                        'form' => null,
                        'pdf' => $pdf
                    ));
                }
            
            }
            return new ViewModel(array(
                'message' => null,
                'form' => $form,
                'pdf' => null
            )); 
        } catch (\Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Entre com contato com o administrador do sistema.<br>Erro: ' 
                        . $ex->getMessage(),
                'form' => null,
                'pdf' => null
            )); 
        }
   }
    
    /**
     * Exibe um formulário para geração do mural de alunos 
     * Quando o formulário é submetido o mural é gerado e exibido no navegador
     * 
     * @return ViewModel
     */
    public function studentsBoardAction()
    {      
        $message = null;
        $pdf = null;
        $form = null;
        $request = $this->getRequest();  
        
        try {
            //  Pega todas as configurações de fundo cadastradas e seleciona os id's 
            //  para mandar para o formulário
            $bgConfigs = $this->getBackgroundConfigs($message);
            $configsIds = array();
            foreach ($bgConfigs as $bgConfig) {
                $configsIds[$bgConfig['id']] = $bgConfig['id'];
            }

            //  Pega todas as turmas cadastradas e seleciona os nomes para mandar
            //  para o formulário
            $studentClasses = $this->getStudentClasses($message);
            $classNames = array();
            foreach ($studentClasses as $studentClass) {
                $classNames[$studentClass['id']] = $studentClass['name'];
            }
        } catch (Exception $ex) {
            $message = $message . '<br>Erro na conexão com o banco de dados.' . 
                    ' Entre com contato com o administrador do sistema.<br>' .
                    'Erro: ' . $ex->getMessage();
            return new ViewModel(array(
                'message' => $message,
                'form' => $form,
                'pdf' => $pdf
            )); 
        }
        
        //  Cria o formulário e o processa
        $form = new StudentsBoardForm($configsIds, $classNames);               
        if ($request->isPost()) {            
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);
            $form->setInputFilter(new StudentsBoardFilter());
            
            if ($form->isValid()) {
                $data = $form->getData();
                
                try {
                    $em = $this->getEntityManager();

                    //  Obtém as informações dos alunos da turma selecionada
                    $enrolls = $studentClasses[$data['class_id']]['enrollments']
                            ->toArray();
                    if (empty($enrolls)) {
                        $message = 'A turma selecionada não possui alunos cadastrados.';
                        return new ViewModel(array(
                            'message' => $message,
                            'form' => $form,
                            'pdf' => $pdf
                        ));                         
                    }
                    foreach($enrolls as $enroll) {
                        $people[] = $enroll->getRegistration()->getPerson();
                    } 
                    
                    // Agrupa as informações dos alunos da turma
                    foreach($people as $person) {
                        $students[] = array(
                                'name' => $person->getPersonName(),
                                'rg' => $person->getPersonRg(),
                                'img_url' => $person->getPersonPhoto()
                        );
                    }
                    
                    //  Agrupa as informações da config de fundo selecionada
                    $config = array(
                        'phrase' => $bgConfigs[$data['config_id']]['phrase'],
                        'author' => $bgConfigs[$data['config_id']]['author'],
                        'bg_img_url' => $bgConfigs[$data['config_id']]['img'],
                    );
                    
                    // Gera PDF...
                    
                } catch (\Exception $ex) {
                    $message = 'Erro inesperado. Entre com contato com o administrador do sistema.<br>' .
                    'Erro: ' . $ex->getMessage();
                } 
            }
        }
        return new ViewModel(array(
            'message' => $message,
            'form' => $form,
            'pdf' => $pdf
        )); 
   }
}
