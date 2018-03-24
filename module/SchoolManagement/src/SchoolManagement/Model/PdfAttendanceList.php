<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Model;

use Documents\Model\DefaultPdf;

/**
 * Gera a lista de chamada da turma selecionada
 *
 * @author Breno Silva <brenog.silva@hotmail.com>
 */
class PdfAttendanceList
{

    const FONT = 'Arial';

    private $students;

    public function __construct($students = [])
    {
        $this->students = $students;
        //$this->dummyStudents();
    }

    protected function dummyStudents()
    {
        for ($i = 0; $i < 103; $i++) {
            $this->students[] = $this->students[0];
        }
    }

    public function generateList()
    {
        $pdf = new DefaultPdf('P', 'mm', 'A4');
        //cor do fundo(listras)
        $pdf->SetFillColor(220);

        $pdf->addPage();
        $pdf->setMargins(5, 5, 5);

        //$pdf->SetXY(0, 0);
        $pdf->SetDrawColor(150);

        $pdf->SetFont(self::FONT, 'B', 16);
        $pdf->Cell(0, 10, utf8_decode("LISTA DE PRESENÇA"), '1', 1, 'C');

        $pdf->SetFont(self::FONT, 'B', 10);
        $pdf->Cell(17, 5, "Matricula", '1', 0, 'C');
        $pdf->Cell(75, 5, "Nome", '1', 0);
        $pdf->Cell(18, 5, "___/___", '1', 0, 'C');
        $pdf->Cell(18, 5, "___/___", '1', 0, 'C');
        $pdf->Cell(18, 5, "___/___", '1', 0, 'C');
        $pdf->Cell(18, 5, "___/___", '1', 0, 'C');
        $pdf->Cell(18, 5, "___/___", '1', 0, 'C');
        $pdf->Cell(18, 5, "___/___", '1', 1, 'C');

        $pdf->SetFont(self::FONT, '', 8.3);
        foreach ($this->students as $idx => $student) {
            if (!($idx % 2)) {
                $pdf->Cell(17, 4, $student["id"], '1', 0, 'C', true);
                $pdf->Cell(75, 4, utf8_decode($student["name"]), '1', 0, 'L', true);
                $pdf->Cell(18, 4, "", '1', 0, 'C', true);
                $pdf->Cell(18, 4, "", '1', 0, 'C', true);
                $pdf->Cell(18, 4, "", '1', 0, 'C', true);
                $pdf->Cell(18, 4, "", '1', 0, 'C', true);
                $pdf->Cell(18, 4, "", '1', 0, 'C', true);
                $pdf->Cell(18, 4, "", '1', 1, 'C', true);
            } else {
                $pdf->Cell(17, 4, $student["id"], '1', 0, 'C');
                $pdf->Cell(75, 4, utf8_decode($student["name"]), '1', 0);
                $pdf->Cell(18, 4, "", '1', 0, 'C');
                $pdf->Cell(18, 4, "", '1', 0, 'C');
                $pdf->Cell(18, 4, "", '1', 0, 'C');
                $pdf->Cell(18, 4, "", '1', 0, 'C');
                $pdf->Cell(18, 4, "", '1', 0, 'C');
                $pdf->Cell(18, 4, "", '1', 1, 'C');
            }
        }

        return $pdf->Output('Chamada.pdf', 'I', true);
    }

}
