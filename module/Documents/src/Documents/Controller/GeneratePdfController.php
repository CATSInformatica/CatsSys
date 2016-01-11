<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Documents\Controller;

use Documents\Form\StudentIdCardForm;
use Documents\Form\StudentIdCardFilter;
use Documents\Form\StudentsBoardForm;
use Documents\Form\StudentsBoardFilter;
use Documents\Model\StudentIdCardPdf;
use Documents\View\StudentIdCard;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


/**
 * Description of GeneratePdfController
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class GeneratePdfController extends AbstractActionController
{
    
    use \Database\Service\EntityManagerService;    
        
    /**
     * Busca as informações de todas as configurações de fundo e as retorna num array
     * 
     * @return array
     */
    public function getBackgroundConfigs($messages) {
        try {
            $em = $this->getEntityManager();
            $configs = $em->getRepository('Documents\Entity\StudentBgConfig')
                    ->findAll();            
        } catch (\Exception $ex) {
            $messages[] = $ex->getMessage();
        }
        
        $bg_configs = array();
        foreach ($configs as $config) {
            $bg_configs[$config->getStudentBgConfigId()] = array(
                'id' => $config->getStudentBgConfigId(),
                'phrase' => $config->getStudentBgConfigPhrase(),
                'author' => $config->getStudentBgConfigAuthor(),
                'img' => $config->getStudentBgConfigImg(),
            );
        }
        return $bg_configs;
    }
    
    /**
     * Busca as informações de todas as turmas e as retorna num array
     * 
     * @return array
     */
    public function getStudentClasses($messages) {
        try {
            $em = $this->getEntityManager();
            $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')
                    ->findAll();
            
        } catch (\Exception $ex) {
            $messages[] = $ex->getMessage();
        }
        
        $studentClasses = array();
        foreach ($classes as $class) {
            $studentClasses[$class->getClassId()] = array(
                'id' => $class->getClassId(),
                'name' => $class->getClassName(),
                'enrollments' => $class->getEnrollments(),
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
        $request = $this->getRequest();        
        $messages = array();
        $pdf = null;
        
        //  Pega todas as configurações de fundo cadastradas e seleciona os id's 
        //  para mandar para o formulário
        $bg_configs = $this->getBackgroundConfigs($messages);
        $configsIds = array();
        foreach ($bg_configs as $config) {
            $configsIds[$config['id']] = $config['id'];
        }
        
        //  Pega todas as turmas cadastradas e seleciona os nomes para mandar
        //  para o formulário
        $studentClasses = $this->getStudentClasses($messages);
        $classNames = array();
        foreach ($studentClasses as $studentClass) {
            $classNames[$studentClass['id']] = $studentClass['name'];
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
            
            $data = null;
            if ($form->isValid()) {
                $data = $form->getData();
                
                $messages = array();
                try {
                    $em = $this->getEntityManager();

                    //  Obtém as informações dos alunos da turma selecionada
                    $enrolls = $studentClasses[$data['class_id']]['enrollments'];
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
                        'phrase' => $bg_configs[$data['config_id']]['phrase'],
                        'author' => $bg_configs[$data['config_id']]['author'],
                        'expiry' => new \DateTime($data['expiry_date']),
                        'bg_img_url' => $bg_configs[$data['config_id']]['img'],
                    );
                    
                    // Instancia um objeto da classe StudentIdPdf e gera as carteirinhas
                    $pdf = new StudentIdCardPdf($config, $students);
                    $pdf = $pdf->generatePdf();
                             
                } catch (\Exception $ex) {
                    $messages[] = $ex->getMessage();
                } 
                
                if (empty($studentClasses[$data['class_id']]['enrollments'])) {
                    $messages = array('A turma selecionada não possui alunos cadastrados.');
                } else {
                    
                }
            }
        }
        return new ViewModel(array(
            'message' => implode(' - ', $messages),
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
        $request = $this->getRequest();        
        $messages = array();
        $pdf = null;
        
        //  Pega todas as configurações de fundo cadastradas e seleciona os id's 
        //  para mandar para o formulário
        $bg_configs = $this->getBackgroundConfigs($messages);
        $configsIds = array();
        foreach ($bg_configs as $config) {
            $configsIds[$config['id']] = $config['id'];
        }
        
        //  Pega todas as turmas cadastradas e seleciona os nomes para mandar
        //  para o formulário
        $studentClasses = $this->getStudentClasses($messages);
        $classNames = array();
        foreach ($studentClasses as $studentClass) {
            $classNames[$studentClass['id']] = $studentClass['name'];
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
            
            $data = null;
            if ($form->isValid()) {
                $data = $form->getData();
                
                $messages = array();
                try {
                    $em = $this->getEntityManager();

                    //  Obtém as informações dos alunos da turma selecionada
                    $enrolls = $studentClasses[$data['class_id']]['enrollments'];
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
                        'phrase' => $bg_configs[$data['config_id']]['phrase'],
                        'author' => $bg_configs[$data['config_id']]['author'],
                        'bg_img_url' => $bg_configs[$data['config_id']]['img'],
                    );
                    
                    // Instancia um objeto da classe StudentIdPdf e gera as carteirinhas
                    //$pdf = new StudentIdCardPdf($config, $students);
                    //$pdf = $pdf->generatePdf();
                             
                } catch (\Exception $ex) {
                    $messages[] = $ex->getMessage();
                } 
                
                if (empty($studentClasses[$data['class_id']]['enrollments'])) {
                    $messages = array('A turma selecionada não possui alunos cadastrados.');
                } else {
                    
                }
            }
        }
        return new ViewModel(array(
            'message' => implode(' - ', $messages),
            'form' => $form,
            'pdf' => $pdf
        ));
    }
    
}
