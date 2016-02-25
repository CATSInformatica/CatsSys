<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Model;

use fpdf\FPDF;

/**
 * Gera a lista de chamada da turma selecionada
 *
 * @author Breno Silva <brenog.silva@hotmail.com>
 */
class PdfAttendanceList 
{
    const FONT = 'Arial';
    
    private $studentNames;
    
    public function __construct($names) 
    {
        $studentNames = $names;
    }
    
    public function generateList() 
    {
        $pdf = new FPDF('L', 'mm', 'A4');
        
        return $pdf->Output('Chamada', 'I', true);
    }
}
