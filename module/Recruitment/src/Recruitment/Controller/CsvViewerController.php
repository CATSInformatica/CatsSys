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
                    while (($values = fgetcsv($fileHandler, 1000, ",")) !== FALSE) {
                        $dates[] = $values[0];
                        $names[] = $values[1];
                        $mascots[] = $values[2];
                        $games[] = explode(',', $values[3]);
                        $compulsory[] = $values[4];
                        $optional[] = $values[5];
                    }
                    $info = array(
                        'dates' => $dates,
                        'names' => $names,
                        'mascots' => $mascots,
                        'games' => $games,
                        'compulsory' => $compulsory,
                        'optional' => $optional,
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
