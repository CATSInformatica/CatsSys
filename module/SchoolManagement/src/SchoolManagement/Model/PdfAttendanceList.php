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
class PdfAttendanceList {

    const FONT = 'Arial';

    private $students;

    public function __construct($students = []) {
        $this->students = $students;
        //$this->dummyStudents();
    }

    protected function dummyStudents() {
        for($i = 0; $i < 103; $i++){
            $this->students[] = $this->students[0];
        }
    }

    public function generateList() {
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->addPage();
        $pdf->setMargins(5, 5, 5);

        $pdf->SetXY(5, 20);
        $pdf->SetDrawColor(150);
        
        $pdf->SetFont(self::FONT, 'B', 16);
        $pdf->Cell(0, 10, utf8_decode("LISTA DE PRESENÇA"), '1', 1, 'C');
        
        $pdf->SetFont(self::FONT, 'B', 11);
        $pdf->Cell(30, 5, "Matricula", '1', 0, 'C');
        $pdf->Cell(80, 5, "Nome", '1', 0);
        $pdf->Cell(22.5, 5, "__/__", '1', 0, 'C');
        $pdf->Cell(22.5, 5, "__/__", '1', 0, 'C');
        $pdf->Cell(22.5, 5, "__/__", '1', 0, 'C');
        $pdf->Cell(22.5, 5, "__/__", '1', 1, 'C');

        $pdf->SetFont(self::FONT,'', 9);
        foreach ($this->students as $student) {
            $pdf->Cell(30, 4, $student["id"], '1', 0, 'C');
            $pdf->Cell(80, 4, utf8_decode($student["name"]), '1', 0);
            $pdf->Cell(22.5, 4, "", '1', 0, 'C');
            $pdf->Cell(22.5, 4, "", '1', 0, 'C');
            $pdf->Cell(22.5, 4, "", '1', 0, 'C');
            $pdf->Cell(22.5, 4, "", '1', 1, 'C');
        }

        return $pdf->Output('Chamada', 'I', true);
    }

}
