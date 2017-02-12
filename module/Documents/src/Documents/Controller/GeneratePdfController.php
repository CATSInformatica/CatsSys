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
//use Documents\Form\StudentsBoardForm;
use Documents\Form\StudentIdCardForm;
use Documents\Model\StudentIdCardPdf;
use Zend\View\Model\ViewModel;
use DateTime;


/**
 * Description of GeneratePdfController
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class GeneratePdfController extends AbstractEntityActionController
{  
    
    /**
     * Exibe um formulário para geração das carteirinhas dos alunos.
     * Quando o formulário é submetido as carteirinhas são geradas e exibidas no navegador.
     * 
     * @return ViewModel
     */
    public function studentIdCardAction()
    {
        $request = $this->getRequest(); 
        $em = $this->getEntityManager();
        
        try {
            // Busca todas as configurações de fundo cadastradas
            $bgConfigs = $em->getRepository('Documents\Entity\StudentBgConfig')
                    ->findAll();
            
            // Busca todas as turmas cadastradas
            $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')
                    ->findAll();
            
            // O formulário é submetido
            if ($request->isPost()) {
                $data = $request->getPost();
                $bgConfigId = $data['bgConfigId'];
                $expiryDate = new DateTime($data['expiryDate']);
                $studentIds = $data['studentIds'];
                
                $form = new StudentIdCardForm($bgConfigs);
                $form->setData([
                    'config_id' => $bgConfigId,
                    'expiry_date' => $expiryDate,
                    
                ]);
                
                // O formulário é validado
                if ($form->isValid()) {
                    // A partir do id (Person) dos estudantes selecionados, suas
                    // informações relevantes para a carteirinha são buscadas e formatadas
                    $selectedStudentsInfo = [];
                    foreach ($studentIds as $studentId) {
                        if (!is_numeric($studentId)) {
                            return new ViewModel(array(
                                'message' => 'Não foi possível obter os dados de um ou mais alunos.',
                                'configs' => [],
                                'classes' => [],
                                'pdf' => null,
                                'form' => null
                            ));
                        }

                        $student = $em->find('Recruitment\Entity\Person', $studentId);
                        $selectedStudentsInfo[] = [
                            'name' => $student->getPersonName(),
                            'rg' => $student->getPersonRg(),
                            'img_url' => $student->getPersonPhoto()
                        ];
                    }

                    // O fundo selecionado é buscado e suas informações formatadas
                    $bgConfig = $em->find('Documents\Entity\StudentBgConfig', $bgConfigId);
                    $studentIdCardsConfig = [
                        'bg_img_url' => $bgConfig->getStudentBgConfigImg(),
                        'phrase' => $bgConfig->getStudentBgConfigPhrase(),
                        'author' => $bgConfig->getStudentBgConfigAuthor(),
                        'expiry' => $expiryDate
                    ];

                    // Instancia um objeto da classe StudentIdPdf e gera as carteirinhas
                    $pdfHandler = new StudentIdCardPdf($studentIdCardsConfig, $selectedStudentsInfo);
                    $pdf = $pdfHandler->generatePdf();

                    return new ViewModel(array(
                        'message' => null,
                        'configs' => [],
                        'classes' => [],
                        'pdf' => $pdf,
                        'form' => null
                    ));
                } else {
                    return new ViewModel(array(
                        'message' => null,
                        'configs' => $bgConfigs,
                        'classes' => $classes,
                        'pdf' => null,
                        'form' => $form
                    )); 
                }
            }
            return new ViewModel(array(
                'message' => null,
                'configs' => $bgConfigs,
                'classes' => $classes,
                'pdf' => null,
                'form' => null
            )); 
        } catch (\Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Entre com contato com o administrador do sistema.<br>Erro: ' 
                        . $ex->getMessage(),
                'configs' => [],
                'classes' => [],
                'pdf' => null,
                'form' => null
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
        /*$message = null;
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
        )); */
        return new ViewModel(array(
            'message' => null,
            'form' => null,
            'pdf' => null
        )); 
   }
}
