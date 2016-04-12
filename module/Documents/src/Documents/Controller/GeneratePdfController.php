<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Documents\Controller;

use Database\Controller\AbstractEntityActionController;
use DateTime;
use Documents\Form\StudentIdCardFilter;
use Documents\Form\StudentIdCardForm;
use Documents\Form\StudentsBoardFilter;
use Documents\Form\StudentsBoardForm;
use Documents\Model\StudentIdCardPdf;
use Exception as Exception2;
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
     * Busca as informações de todas as configurações de fundo e as retorna num array
     * 
     * @return array
     */
    public function getBackgroundConfigs($message) {
        try {
            $em = $this->getEntityManager();
            $configs = $em->getRepository('Documents\Entity\StudentBgConfig')
                    ->findAll();            
        } catch (Exception2 $ex) {
            $message = $ex->getMessage();
        }
        
        $bgConfigs = array();
        foreach ($configs as $config) {
            $bgConfigs[$config->getStudentBgConfigId()] = array(
                'id' => $config->getStudentBgConfigId(),
                'phrase' => $config->getStudentBgConfigPhrase(),
                'author' => $config->getStudentBgConfigAuthor(),
                'img' => $config->getStudentBgConfigImg(),
            );
        }
        return $bgConfigs;
    }
    
    /**
     * Busca as informações de todas as turmas e as retorna num array
     * 
     * @return array
     */
    public function getStudentClasses($message) {
        try {
            $em = $this->getEntityManager();
            $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')
                    ->findAll();
            
        } catch (Exception2 $ex) {
            $message = $ex->getMessage();
        }
        
        $studentClasses = array();
        foreach ($classes as $class) {
            $studentClasses[$class->getClassId()] = array(
                'id' => $class->getClassId(),
                'name' => $class->getClassName(),
                'enrollments' => $class->getEnrollments()->toArray(),
            );
        }
        return $studentClasses;
    }    
    
    /**
     * Exibe um formulário para geração das carteirinhas dos alunos
     * Quando o formulário é submetido as carteirinhas são geradas e exibidas no navegador
     * 
     * @return ViewModel
     */
    public function studentIdCardAction()
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
        $form = new StudentIdCardForm($configsIds, $classNames);               
        if ($request->isPost()) {            
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);
            $form->setInputFilter(new StudentIdCardFilter());
            
            if ($form->isValid()) {
                $data = $form->getData();
                
                try {
                    $people = [];
                    
                    //  Obtém as informações dos alunos da turma selecionada
                    $enrolls = $studentClasses[$data['class_id']]['enrollments'];
                    foreach($enrolls as $enroll) {
                        //  Aluno não encerrou matrícula
                        if ($enroll->getEnrollmentEndDate() === null) {
                            $people[] = $enroll->getRegistration()->getPerson();
                        }
                    } 
                    if (empty($people)) {
                        $message = 'A turma selecionada não possui alunos cadastrados.';
                        return new ViewModel(array(
                            'message' => $message,
                            'form' => $form,
                            'pdf' => $pdf
                        ));                         
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
                        'expiry' => new DateTime($data['expiry_date']),
                        'bg_img_url' => $bgConfigs[$data['config_id']]['img'],
                    );
                    
                    // Instancia um objeto da classe StudentIdPdf e gera as carteirinhas
                    $pdfHandler = new StudentIdCardPdf($config, $students);
                    $pdf = $pdfHandler->generatePdf();
                } catch (Exception2 $ex) {
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
                    
                } catch (Exception2 $ex) {
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
