<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Controller;

use Recruitment\Form\CsvViewerForm;
use Recruitment\Form\CsvViewerFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Description of CsvViewerController
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class CsvViewerController extends AbstractActionController
{

    public function indexAction()
    {
        $request = $this->getRequest();
        $form = new CsvViewerForm('CSV Viewer Form');
        $message = null;
        $info = null;

        if ($request->isPost()) {

            $post = array_merge_recursive(
                $request->getFiles()->toArray()
            );
            $form->setData($post);
            $form->setInputFilter(new CsvViewerFilter());

            if ($form->isValid()) {
                $data = $form->getData();

                if (($fileHandler = fopen($data['csv_file']['tmp_name'], "r"))
                        !== FALSE) {
                    $title = fgetcsv($fileHandler, 10000, ",");
                    while (($values = fgetcsv($fileHandler, 10000, ",")) !== FALSE) {
                        $content[] = $values;
                    }
                    $info = array(
                        'title' => $title,
                        'content' => $content,
                        'blocks' => array(
                                array(
                                    'boundary' => 3,
                                    'title' => 'Informações Pessoais',
                                    'nextBoundary' => 11,
                                ),
                                array(
                                    'boundary' => 11,
                                    'title' => 'Endereço',
                                    'nextBoundary' => 15,
                                ),
                                array(
                                    'boundary' => 15,
                                    'title' => 'Informações do responsável',
                                    'nextBoundary' => 21,
                                ),
                                array(
                                    'boundary' => 21,
                                    'title' => 'Educação Formal',
                                    'nextBoundary' => 31,
                                ),
                                array(
                                    'boundary' => 31,
                                    'title' => 'Moradia',
                                    'nextBoundary' => 49,
                                ),
                                array(
                                    'boundary' => 49,
                                    'title' => 'Outros Imóveis',
                                    'nextBoundary' => 63,
                                ),
                                array(
                                    'boundary' => 63,
                                    'title' => 'Bens móveis',
                                    'nextBoundary' => 79,
                                ),
                                array(
                                    'boundary' => 79,
                                    'title' => 'Você se considera (Etnia)',
                                    'nextBoundary' => 80,
                                ),
                                array(
                                    'boundary' => 80,
                                    'title' => 'Meio de Transporte',
                                    'nextBoundary' => 83,
                                ),
                                array(
                                    'boundary' => 83,
                                    'title' => 'Despesas',
                                    'nextBoundary' => 90,
                                ),
                                array(
                                    'boundary' => 90,
                                    'title' => 'Grupo Familiar',
                                    'nextBoundary' => 164,
                                ),
                                array(
                                    'boundary' => 164,
                                    'title' => 'Você é ex-aluno do CATS?',
                                    'nextBoundary' => 165,
                                ),
                                array(
                                    'boundary' => 165,
                                    'title' => 'Informe ou esclareça sobre dados '
                                    . 'não contemplados neste formulário ou situações '
                                    . 'especiais que julgar conveniente',
                                    'nextBoundary' => 166,
                                ),
                                array(
                                    'boundary' => 166,
                                    'title' => 'Declaro, para fins de direito, '
                                    . 'sob as penas da lei, e em atendimento ao '
                                    . 'EDITAL do CATS, que as informações constantes '
                                    . 'dos documentos que apresento para a pré-entrevista '
                                    . 'no processo seletivo de alunos são verdadeiras, '
                                    . '(ou são fieis à verdade e condizentes com '
                                    . 'a realidade dos fatos à época). Fico ciente '
                                    . 'através desse documento que a falsidade dessa '
                                    . 'declaração configura crime previsto no Código '
                                    . 'Penal Brasileiro, e passível de apuração na forma da Lei.',
                                    'nextBoundary' => 167,
                                ),
                        ),
                    );
                    fclose($fileHandler);
                } else {
                    $message = "O arquivo não pôde ser lido.";
                }
            }
        }
        return new ViewModel(array(
            'message' => $message,
            'form' => $form,
            'info' => $info,
        ));
    }

}
