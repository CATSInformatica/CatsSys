<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Documents\Model;

use fpdf\FPDF;

/**
 * Description of StudentIdCardPdf
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class StudentIdCardPdf
{

    const FONT = 'Arial';
    const TOP_BORDER = 6.14;

    private $config;
    private $students;
    private $basePath;

    public function __construct($config, $students)
    {
        $this->config = $config;
        $this->students = $students;
        $this->basePath = __DIR__ . '/../../..';
    }

    /**
     * Monta as carteirinhas de acordo em $config e $students
     * @return FPDF
     */
    
    public function generatePdf()
    {
        $pdf = new FPDF('P', 'mm', 'A4');
        $y = self::TOP_BORDER;
        // Map para imprimir os meses a partir do indice
        $months = array(
            "Janeiro",
            "Fevereiro",
            "Março",
            "Abril",
            "Maio",
            "Junho",
            "Julho",
            "Agosto",
            "Setembro",
            "Outubro",
            "Novembro",
            "Dezembro"
        );

        foreach ($this->students as $i => $student) {
            if ($i % 5 == 0) {  // Na configuracao atual cabem apenas 5 carteirinhas em cada página
                $y = self::TOP_BORDER;
                $pdf->AddPage('P', 'A4');
            }
            $pdf->SetDrawColor(255);
            $pdf->SetFillColor(95, 124, 138); 

            // Utiliza o background da configuracao selecionada
            $pdf->Image($this->basePath . '/../../public/img/' . $this->config['bg_img_url'], 14.37, $y, 88, 54);  // frente
            $pdf->Image($this->basePath . '/../../public/img/' . $this->config['bg_img_url'], 14.37 + 93.45, $y, 88, 54);  // verso
            
            // Logo e CATS
            $pdf->Image($this->basePath . '/../../public/img/' . 'logo_branco.png', 17.97, $y + 3.48, 15.92, 9.64);
            $pdf->Image($this->basePath . '/../../public/img/' . 'logo_branco.png', 17.97 + 93.45, $y + 3.48, 15.92, 9.64);
            $pdf->SetFont(self::FONT, '', 10);
            $pdf->SetTextColor(255);
            $pdf->Text(35.75, $y + 9.09, "Curso Assistencial Theodomiro Santiago");
            $pdf->Text(35.75 + 93.45, $y + 9.09, "Curso Assistencial Theodomiro Santiago");
            $pdf->Image($this->basePath . '/../../data/profile/' . $student['img_url'], 17.97, $y + 19.40, 23.44);

            // Campos NOME e RG
            $pdf->SetTextColor(0);
            $pdf->SetFillColor(255);
            $pdf->Text(44.47, $y + 22.75, "NOME");
            $pdf->Rect(44.47, $y + 24, 54.43, 3.83, 'DF');
            $pdf->Text(44.47, $y + 33.47, "RG");
            $pdf->Rect(44.47, $y + 34.80, 54.43, 3.83, 'DF');
            $pdf->Rect(17.97 + 93.45, $y + 15.46, 80.93, 20.12, 'DF');
            $pdf->Line(126.64, $y + 41.79, 177.13, $y + 41.79, 'DF');
            $pdf->SetFont(self::FONT, '', 8);
            $pdf->Text(46.17, $y + 26.78, utf8_decode($student['name']));
            $pdf->Text(46.17, $y + 37.73, $student['rg']);

            // Assinatura, autor, frase e data de validade
            $pdf->Text(136.18, $y + 46.12, "Assinatura do Presidente");
            $pdf->Text(191 - $pdf->GetStringWidth(
                            utf8_decode($this->config['author'])), $y + 34, utf8_decode($this->config['author'])
            );
            $pdf->SetXY(114.52, $y + 18.95);
            $pdf->MultiCell(74.72, 4, utf8_decode($this->config['phrase']));
            $pdf->SetFont(self::FONT, '', 6);
            $str = utf8_decode('Válido até ' . $this->config['expiry']->format('d') . ' de '
                    . $months[(int) ($this->config['expiry']->format('n')) - 1] . ' de '
                    . $this->config['expiry']->format('Y'));
            $pdf->Text(98.91 - $pdf->GetStringWidth($str), $y + 50, $str);
            $pdf->Text(98.91 - $pdf->GetStringWidth($str) + 93.45, $y + 50, $str);
            
            $y += 58;  // Distancia entre a borda superior das carteirinhas
        }
        return $pdf->Output('Carteirinhas', 'I', true);
    }

}
