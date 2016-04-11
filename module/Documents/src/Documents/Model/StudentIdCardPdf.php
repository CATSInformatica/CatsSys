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
    const TOP_MARGIN = 6;
    const LEFT_MARGIN = 17;

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
        $y = self::TOP_MARGIN;
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
            /*
             * Variáveis que controlam a aparência da carteirinha
             */
            $width = 88;
            $height = 54;
            // Distância entre as laterais correspondentes da frente e do verso da carteirinha
            $lateralDistance = $width + 0.5;

            if ($i % 5 == 0) {  // Na configuracao atual cabem apenas 5 carteirinhas em cada página
                $y = self::TOP_MARGIN;
                $pdf->AddPage('P', 'A4');
            }
            $pdf->SetDrawColor(255);
            $pdf->SetFillColor(95, 124, 138);

            // Utiliza o background da configuracao selecionada
            $pdf->Image($this->basePath . '/../../public/img/' . $this->config['bg_img_url'], self::LEFT_MARGIN, $y,
                $width, $height);  // frente
            $pdf->Image($this->basePath . '/../../public/img/' . $this->config['bg_img_url'],
                self::LEFT_MARGIN + $lateralDistance, $y, $width, $height);  // verso
                
            // Logo
            $pdf->Image($this->basePath . '/../../public/img/' . 'logo_branco.png', self::LEFT_MARGIN + 3.6, $y + 3.48,
                15.92, 9.64);
            $pdf->Image($this->basePath . '/../../public/img/' . 'logo_branco.png',
                self::LEFT_MARGIN + 3.6 + $lateralDistance, $y + 3.48, 15.92, 9.64);

            // CATS
            $pdf->SetFont(self::FONT, '', 9.5);
            $pdf->SetTextColor(255);
            $pdf->Text(self::LEFT_MARGIN + ($width - $pdf->GetStringWidth("Curso Assistencial Theodomiro Santiago")) / 2 + 9.76, $y + 9.09, "Curso Assistencial Theodomiro Santiago");
            $pdf->Text(self::LEFT_MARGIN + ($width - $pdf->GetStringWidth("Curso Assistencial Theodomiro Santiago")) / 2 + 9.76 + $lateralDistance, $y + 9.09, "Curso Assistencial Theodomiro Santiago");

            // Foto
            $pdf->Image($this->basePath . '/../../data/profile/' . $student['img_url'], self::LEFT_MARGIN + 3.6, $y + 19.40, 23.44);

            // Campos NOME e RG
            $pdf->SetTextColor(20);
            $pdf->SetFillColor(255);
            $pdf->SetFont(self::FONT, '', 7.5);
            $pdf->Text(self::LEFT_MARGIN + 30.1, $y + 22.65, "NOME");
            $pdf->Rect(self::LEFT_MARGIN + 30.1, $y + 24, 54.43, 3.83, 'DF');
            $pdf->Text(self::LEFT_MARGIN + 30.1, $y + 33.47, "RG");
            $pdf->Rect(self::LEFT_MARGIN + 30.1, $y + 34.80, 54.43, 3.83, 'DF');
            $pdf->SetFont(self::FONT, '', 6.5);
            $pdf->Text(self::LEFT_MARGIN + 31.8, $y + 26.78, utf8_decode($student['name']));
            $pdf->Text(self::LEFT_MARGIN + 31.8, $y + 37.55, $student['rg']);

            // Assinatura
            $pdf->Text(self::LEFT_MARGIN + $lateralDistance + ($width - $pdf->GetStringWidth("Assinatura do Presidente")) / 2, $y + 50, "Assinatura do Presidente");
            $pdf->Line(self::LEFT_MARGIN + $lateralDistance + ($width - 50) / 2, $y + 46, 
                    self::LEFT_MARGIN + $lateralDistance + ($width - 50) / 2 + 50, $y + 46, 'DF');

            // Frase
            $pdf->Rect(self::LEFT_MARGIN + 3.6 + $lateralDistance, $y + 17, 80.93, 22.12, 'DF');
            $pdf->SetFont(self::FONT, '', 7);
            $pdf->SetXY(self::LEFT_MARGIN + 95.2, $y + 20.49);
            $pdf->MultiCell(74.72, 4, utf8_decode($this->config['phrase']));

            // Autor
            $pdf->Text(self::LEFT_MARGIN + $lateralDistance + $width - ($width - 80.93) / 2 - 4 - $pdf->GetStringWidth(utf8_decode($this->config['author'])), $y + 36,
                utf8_decode($this->config['author']));

            // Data de validade
            $pdf->SetFont(self::FONT, '', 6);
            $str = utf8_decode('Válido até ' . $this->config['expiry']->format('d') . ' de '
                . $months[(int) ($this->config['expiry']->format('n')) - 1] . ' de '
                . $this->config['expiry']->format('Y'));
            $pdf->Text(self::LEFT_MARGIN + 84.54 - $pdf->GetStringWidth($str), $y + 50, $str);

            $y += 58;  // Distancia entre a borda superior das carteirinhas
        }
        return $pdf->Output('Carteirinhas', 'I', true);
    }

}
