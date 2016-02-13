<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

/**
 * Description of StudyResources
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class StudyResourcesController extends AbstractActionController
{
    
    /**
    * Retorna um array com as informações dos vestibulinhos passados (número, ano, link)
    * 
    * @return JsonModel
    */
    public function getPastExamsAction()
    {   
        $psa = null;
        $message = null;
        $dir = './public/docs';
        if (file_exists($dir) == false) {
            $message = 'Diretório \'' . $dir . '\' não encontrado!';
        } else {
            $dir_contents = implode('-', scandir($dir));
            if (!preg_match_all('/(?P<source>(PSA_(?P<year>\d{4})_(?P<number>\d)(_(?P<part>\d))?)\.pdf)/', $dir_contents, $psa)) {
                $psa = null;
            }
        }
        return new JsonModel(array(
            'message' => $message,
            'psa_dir' => substr($dir, 8),
            'psa' => $psa,
        ));
    }
    
}
