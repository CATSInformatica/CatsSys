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

namespace Documents\Model;

use FPDF;

/**
 * Description of StudentsBoardPdf
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class StudentsBoardPdf
{
    const BASE_PATH = __DIR__ . '/../../../../..';    
    
    // Dimensões das margens da página (mm)
    const VERTICAL_MARGIN   = 12;
    const HORIZONTAL_MARGIN = 17;
    
    // Dimensões do cartão (mm)
    const GAP_BETWEEN_CARDS     = 0.5;
    const CARD_PADDING  = 2.5;
    const CARD_WIDTH    = 88;
    const CARD_HEIGHT   = 54;
    const STUDENT_PICTURE_WIDTH = 23.44;
    
    // Fonte
    const FONT                  = 'Arial';
    const PT_TO_MM_MULTIPLIER   = 0.352778; // Valor aproximado para conversão. [mm] = [pt] * self::PT_TO_MM_MULTIPLIER
    
    // Tamanho do texto (pt)
    const TITLE_TEXT_SIZE = 9.5;    // Nome do aluno
    
    
    protected $students;
    
    public function __construct($students)
    {           
        $this->students = $students;
    }

    /**
     * Monta as carteirinhas 
     * @return FPDF
     */
    public function generatePdf()
    {
        $pdf = new FPDF('P', 'mm', 'A4');
        
        // Variáveis auxiliares
        $yCoordinate = self::VERTICAL_MARGIN;
        $cardVerseX = self::HORIZONTAL_MARGIN + self::GAP_BETWEEN_CARDS + self::CARD_WIDTH;
        $cardsPerPage = (int)(2 * (297/*altura do papel A4*/ - 2 * self::VERTICAL_MARGIN) 
                / (self::CARD_HEIGHT + self::GAP_BETWEEN_CARDS));
        
        foreach ($this->students as $i => $student) {            
            if ($i % $cardsPerPage === 0) {
                $pdf->AddPage('P', 'A4');
                $yCoordinate = self::VERTICAL_MARGIN;
            }
            
            if ($i % 2 === 0) {
                $this->buildCard($pdf, self::HORIZONTAL_MARGIN, $yCoordinate, $student);
            } else {
                $this->buildCard($pdf, $cardVerseX, $yCoordinate, $student);

                // coordenada y para construção da próxima carteirinha
                $yCoordinate += self::CARD_HEIGHT + self::GAP_BETWEEN_CARDS;  
            }
        }
        
        return $pdf->Output('Carteirinhas', 'I', true);
    }
    
    private function buildCard($pdf, $x, $y, $student)
    {
        $pdf->SetFillColor(255);    // preenchimento branco
        $pdf->SetDrawColor(0);      // bordas pretas 
        
        // Posiciona o cartão
        $pdf->Rect(
            $x,     
            $y,     
            self::CARD_WIDTH,   // largura
            self::CARD_HEIGHT,  // altura
            'DF'                        // desenha e preenche
        );

        // Posiciona a foto do aluno
        $pdf->Image(
            self::BASE_PATH . '/data/profile/' . $student['img_url'],
            $x + self::CARD_PADDING,    // x
            $y + self::CARD_HEIGHT - self::STUDENT_PICTURE_WIDTH - 2 * self::CARD_PADDING,  // y
            self::STUDENT_PICTURE_WIDTH // largura
            // altura redimensionada automaticamente
        );
        
        // Define a fonte, o tamanho e a cor do texto
        $pdf->SetFont(self::FONT, '', self::TITLE_TEXT_SIZE);
        $pdf->SetTextColor(0);  // Texto preto
        
        // Posiciona o nome do aluno
        $titleWidth = $pdf->GetStringWidth(utf8_decode($student['name']));
        $pdf->Text(
            $x + (self::CARD_WIDTH - $titleWidth) / 2, // x
            $y + self::CARD_PADDING + self::TITLE_TEXT_SIZE * self::PT_TO_MM_MULTIPLIER, 
            utf8_decode($student['name']) 
        );
    }

}
