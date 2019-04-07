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
use Documents\Form\StudentsBoardForm;
use Documents\Form\StudentIdCardForm;
use Documents\Model\StudentIdCardPdf;
use Documents\Model\StudentsBoardPdf;
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
                    ->findBy([], ['classId' => 'DESC']);

            $form = new StudentIdCardForm($bgConfigs);

            // O formulário é submetido
            if ($request->isPost()) {
                $data = $request->getPost();
                $form->setData([
                    'config_id' => $data['bgConfigId'],
                    'expiry_date' => $data['expiryDate'],
                    'studentIds' => $data['studentIds']
                ]);

                // O formulário é validado
                if ($form->isValid()) {
                    $data = $form->getData();
                    // A partir do id (Person) dos estudantes selecionados, suas
                    // informações relevantes para a carteirinha são buscadas e formatadas
                    $selectedStudentsInfo = $this->getFormattedStudentsData($data['studentIds']);

                    // O fundo selecionado é buscado e suas informações formatadas
                    $bgConfig = $em->find('Documents\Entity\StudentBgConfig', $data['config_id']);
                    $studentIdCardsConfig = [
                        'bg_img_url' => $bgConfig->getStudentBgConfigImg(),
                        'phrase' => $bgConfig->getStudentBgConfigPhrase(),
                        'author' => $bgConfig->getStudentBgConfigAuthor(),
                        'expiry' => new \DateTime($data['expiry_date'])
                    ];

                    // Instancia um objeto da classe StudentIdPdf e gera as carteirinhas
                    $pdfHandler = new StudentIdCardPdf($studentIdCardsConfig, $selectedStudentsInfo);
                    $pdf = $pdfHandler->generatePdf();

                    // exibe o pdf
                    return new ViewModel(array(
                        'message' => null,
                        'configs' => [],
                        'classes' => [],
                        'pdf' => $pdf,
                        'form' => null
                    ));
                } else {
                    // exibe o formulário e os erros de validação
                    return new ViewModel(array(
                        'message' => null,
                        'configs' => $bgConfigs,
                        'classes' => $classes,
                        'pdf' => null,
                        'form' => $form
                    ));
                }
            }
            // exibe o formulário
            return new ViewModel(array(
                'message' => null,
                'configs' => $bgConfigs,
                'classes' => $classes,
                'pdf' => null,
                'form' => $form
            ));
        } catch (\Exception $ex) {
            // exibe o formulário e a mensagem de erro
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

    private function getFormattedStudentsData($studentIds)
    {
        $em = $this->getEntityManager();
        $selectedStudentsInfo = [];

        foreach ($studentIds as $studentId) {
            $student = $em->find('Recruitment\Entity\Person', $studentId);
            $selectedStudentsInfo[] = [
                'name' => $student->getPersonName(),
                'rg' => $student->getPersonRg(),
                'img_url' => $student->getPersonPhoto()
            ];
        }

        return $selectedStudentsInfo;
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
        $em = $this->getEntityManager();

        try {
            // Busca todas as turmas cadastradas
            $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')
                    ->findAll();

            $form = new StudentsBoardForm();

            // O formulário é submetido
            if ($request->isPost()) {
                $data = $request->getPost();
                $form->setData([
                    'studentIds' => $data['studentIds'],
                ]);
                // O formulário é validado
                if ($form->isValid()) {
                    $studentIds = $form->getData()['studentIds'];

                    $selectedStudentsInfo = $this->getFormattedStudentsData($studentIds);

                    $pdfHandler = new StudentsBoardPdf($selectedStudentsInfo);
                    $pdf = $pdfHandler->generatePdf();

                    return new ViewModel(array(
                        'message' => null,
                        'classes' => [],
                        'pdf' => $pdf,
                        'form' => null
                    ));
                } else {
                    return new ViewModel(array(
                        'message' => null,
                        'classes' => $classes,
                        'pdf' => null,
                        'form' => $form
                    ));
                }
            }
            return new ViewModel(array(
                'message' => null,
                'classes' => $classes,
                'pdf' => null,
                'form' => $form
            ));
        } catch (\Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Entre com contato com o administrador do sistema.<br>Erro: '
                        . $ex->getMessage(),
                'classes' => [],
                'pdf' => null,
                'form' => null
            ));
        }
   }
}
