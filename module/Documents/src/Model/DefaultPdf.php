<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Documents\Model;

use FPDF;

/**
 * Rodapé e o cabeçalho dos pdfs
 *
 * @author Breno Silva <brenog.silva@hotmail.com>
 */
class DefaultPdf extends FPDF
{

    const BASE_PATH = __DIR__;

    public function Header()
    {
        $this->SetXY(5, 5);
        // Logo
        $this->Image(self::BASE_PATH . '/../../../..s/public/img'
                . '/logo_prova.png', 10, 5, 28);
        // Arial bold 12
        $this->SetFont('Arial', 'B', 12);
        // Move to the right
        $this->Cell(40);
        // Title
        $this->Cell(100, 6, 'Curso Assistencial Theodomiro Santiago', 0, 1);
        // Arial 10
        $this->SetFont('Arial', '', 10);
        // Move to the right
        $this->Cell(35);
        $this->Cell(100, 6, utf8_decode('Av. BPS, 1303 - UNIFEI '
                . '- Campus Professor J. R. Seabra - Sala I.1.2.47 - Itajubá '
                . '- MG'), 0, 1);
        // Move to the right
        $this->Cell(35);
        $this->Cell(100, 6, utf8_decode('Tel.: (35) 98856-1340 | '
                . 'E-mail: catsensino@gmail.com | '
                . 'Site: http://www.familiacats.com.br'), 0, 1);
        // Line break
        $this->Ln(20);
        $this->Line(5, 23, 205, 23);
        $this->SetXY(5, 26);
    }

    public function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial 12
        $this->SetFont('Arial', '', 12);
        // Page number
        $this->Cell(0, 10, $this->PageNo(), 0, 0, 'R');
    }

}
