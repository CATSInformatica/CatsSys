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

use fpdf\FPDF;

/**
 * Description of StudentIdCardPdf
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class StudentIdCardPdf
{
    
    const BASE_PATH = __DIR__ . '/../../../../..';    
    const MONTHS = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
    
    /* Configurações da página */
    // Dimensões da margem (mm)
    const VERTICAL_MARGIN   = 12;
    const HORIZONTAL_MARGIN = 17;
    
    /* Configurações da carteirinha */
    // Dimensões da carteirinha (mm)
    const CARD_PADDING      = 3.6;
    const CARD_WIDTH        = 88;
    const CARD_HEIGHT       = 54;
    const GAP_BETWEEN_CARDS = 0.5;
    // Texto
    const TITLE = "Curso Assistencial Theodomiro Santiago";
    const SIGNATURE = "Assinatura do Presidente";
    const FONT  = 'Arial';
    // Tamanho do texto (pt)
    const TITLE_TEXT_SIZE   = 9.5;  // Nome do cursinho
    const LABEL_TEXT_SIZE   = 7.5;  // Campo do nome e do RG
    const PHRASE_TEXT_SIZE  = 7.0;  // Frase e autor
    const REGULAR_TEXT_SIZE = 6.5;  // Nome, RG e assinatura
    const EXPIRY_TEXT_SIZE  = 6.0;  // Data de validade
    // Dimensões do logo (mm)
    const LOGO_WIDTH    = 16;
    const LOGO_HEIGHT   = 0; // 0 = automático
    // Dimensões dos campos da frente da carteirinha (mm)
    const FRONT_FIELDS_WIDTH   = 54.5;
    const FRONT_FIELDS_HEIGHT   = 3.9;
    // Dimensões do campo da frase (mm)
    const PHRASE_FIELD_HEIGHT = 22.12;
    const PHRASE_FIELD_PADDING = 3;
    const PHRASE_FIELD_ROW_HEIGHT = 4;
    
    
    private $config;
    private $students;
    
    public function __construct($config, $students)
    {           
        $this->config = $config;
        $this->students = $students;exit;
    }

    /**
     * Monta as carteirinhas de acordo em $config e $students
     * @return FPDF
     */
    public function generatePdf()
    {
        $pdf = new FPDF('P', 'mm', 'A4');
        
        // Variáveis auxiliares
        $yCoordinate = self::VERTICAL_MARGIN;
        $cardVerseX = self::HORIZONTAL_MARGIN + self::GAP_BETWEEN_CARDS + self::CARD_WIDTH;
        $cardsPerPage = (int)((297/*altura do papel A4*/ - 2 * self::VERTICAL_MARGIN) 
                / (self::CARD_HEIGHT + self::GAP_BETWEEN_CARDS));
        $frontFieldsX = self::HORIZONTAL_MARGIN + self::CARD_WIDTH - self::CARD_PADDING - self::FRONT_FIELDS_WIDTH;
        
        foreach ($this->students as $i => $student) {            
            if ($i % $cardsPerPage === 0) {
                $pdf->AddPage('P', 'A4');
                $yCoordinate = self::VERTICAL_MARGIN;
            }
            
            $pdf->SetDrawColor(255);  // Branco 
            $pdf->SetFillColor(255);  // Branco 

            // Posiciona o background selecionado na frente da carteirinha
            $pdf->Image(
                    self::BASE_PATH . '/public/img/' . $this->config['bg_img_url'],  // url
                    self::HORIZONTAL_MARGIN,// x
                    $yCoordinate,           // y
                    self::CARD_WIDTH,       // largura
                    self::CARD_HEIGHT       // altura
            );
            // Posiciona o background selecionado no verso da carteirinha
            $pdf->Image(
                    self::BASE_PATH . '/public/img/' . $this->config['bg_img_url'],  // url
                    $cardVerseX,        // x
                    $yCoordinate,       // y
                    self::CARD_WIDTH,   // largura
                    self::CARD_HEIGHT   // altura
            );
                
            // Posiciona o logo 
            $pdf->Image(
                    self::BASE_PATH . '/public/img/' . 'logo_branco.png',  // url
                    self::HORIZONTAL_MARGIN + self::CARD_PADDING,   // x
                    $yCoordinate + self::CARD_PADDING,              // y
                    self::LOGO_WIDTH,  // largura 
                    self::LOGO_HEIGHT  // altura 
            );
            $pdf->Image(
                    self::BASE_PATH . '/public/img/' . 'logo_branco.png',  // url
                    $cardVerseX + self::CARD_PADDING,   // x
                    $yCoordinate + self::CARD_PADDING,  // y
                    self::LOGO_WIDTH,  // largura 
                    self::LOGO_HEIGHT  // altura 
            );

            // Define a fonte, o tamanho e a cor do texto
            $pdf->SetFont(self::FONT, '', self::TITLE_TEXT_SIZE);
            $pdf->SetTextColor(255);  // Branco
            
            // Posiciona o nome do cursinho na frente
            $titleWidth = $pdf->GetStringWidth(self::TITLE);
            $pdf->Text(
                    self::HORIZONTAL_MARGIN + self::CARD_WIDTH - $titleWidth - self::CARD_PADDING, // x
                    $yCoordinate + 9.09, self::TITLE  // y
            );
            
            // Posiciona o nome do cursinho no verso
            $pdf->Text(
                    $cardVerseX + self::CARD_WIDTH - $titleWidth - self::CARD_PADDING, // x
                    $yCoordinate + 9.09, self::TITLE  // y
            );

            // Posiciona a foto do aluno
            $pdf->Image(
                    self::BASE_PATH . '/data/profile/' . $student['img_url'], // url
                    self::HORIZONTAL_MARGIN + self::CARD_PADDING,    // x
                    $yCoordinate + 19.40,       // y (mm)
                    23.44                       // largura (mm)
                    // altura redimensionada automaticamente
            );

            // Define a fonte, o tamanho e a cor do texto
            $pdf->SetFont(self::FONT, '', self::LABEL_TEXT_SIZE);
            $pdf->SetTextColor(20);   // Cinza
            
            // Posiciona o rótulo e o campo do NOME do aluno
            $nameFieldY = $yCoordinate + 24; 
            $pdf->Text(
                    $frontFieldsX,      // x
                    $nameFieldY - 1.35, // y (mm)
                    "NOME"  
            );
            $pdf->Rect(
                    $frontFieldsX,      // x
                    $nameFieldY,        // y
                    self::FRONT_FIELDS_WIDTH,  // largura
                    self::FRONT_FIELDS_HEIGHT, // altura
                    'DF'  // draw & fill (desenha e preenche)
            );
            
            // Posiciona o rótulo e o campo do RG do aluno
            $rgFieldY = $yCoordinate + 34.85;
            $pdf->Text(
                    $frontFieldsX,      // x
                    $rgFieldY - 1.35,   // y (mm)
                    "RG"
            );
            $pdf->Rect(
                    $frontFieldsX,      // x
                    $rgFieldY,          // y 
                    self::FRONT_FIELDS_WIDTH,  // largura
                    self::FRONT_FIELDS_HEIGHT, // altura
                    'DF'  // draw & fill (desenha e preenche)
            );
            
            // Define a fonte e o tamanho do texto
            $pdf->SetFont(self::FONT, '', self::REGULAR_TEXT_SIZE);
            
            // Posiciona o NOME do aluno
            $pdf->Text(
                    $frontFieldsX + 1.5,    // x (mm)
                    $nameFieldY + 2.78,     // y (mm)
                    utf8_decode($student['name'])  // texto
            );
            
            // Posiciona o RG do aluno  
            $pdf->Text(
                    $frontFieldsX + 1.5,    // x (mm)
                    $rgFieldY + 2.7,        // y (mm)
                    $student['rg']          // texto
            );

            // Posiciona a assinatura do presidente
            $signatureTxt = self::SIGNATURE;
            $ySignatureTxt = $yCoordinate + self::CARD_HEIGHT - self::CARD_PADDING;
            $pdf->Text(
                    $cardVerseX + (self::CARD_WIDTH - $pdf->GetStringWidth($signatureTxt)) / 2, // x
                    $ySignatureTxt, // y
                    $signatureTxt   // texto
            );
            
            // Posiciona uma linha acima do texto da assinatura
            $pdf->Line(
                    $cardVerseX + self::CARD_WIDTH * 0.2,   // x inicial (20% da largura)
                    $ySignatureTxt - 4,                     // y inicial (4mm acima do texto da assinatura)
                    $cardVerseX + self::CARD_WIDTH * 0.8,   // x final (80% da largura)
                    $ySignatureTxt - 4,                     // y final = y inicial
                    'DF'  // draw & fill (desenha e preenche)
            );

            $phraseFieldY = $yCoordinate + 17;
            // Posiciona o campo da frase
            $pdf->Rect(
                    $cardVerseX + self::CARD_PADDING,   // x 
                    $phraseFieldY,                      // y (mm)
                    self::CARD_WIDTH - 2 * self::CARD_PADDING,  // largura
                    self::PHRASE_FIELD_HEIGHT,          // altura
                    'DF'    // draw & fill (desenha e preenche)
            );
            
            // Define a fonte e o tamanho do texto
            $pdf->SetFont(self::FONT, '', self::PHRASE_TEXT_SIZE);
            
            // Posiciona a frase no verso da carteirinha
            $pdf->SetXY(
                    $cardVerseX + self::CARD_PADDING + self::PHRASE_FIELD_PADDING, // x (mm)
                    $phraseFieldY + self::PHRASE_FIELD_PADDING  // y (mm)
            );
            $pdf->MultiCell(
                    self::CARD_WIDTH - 2 * self::CARD_PADDING - 2 * self::PHRASE_FIELD_PADDING,  // largura de cada celula
                    self::PHRASE_FIELD_ROW_HEIGHT,          // altura de cada celula
                    utf8_decode($this->config['phrase'])    // texto
            );

            // Posiciona o autor
            $pdf->Text(
                    $cardVerseX + self::CARD_WIDTH - self::CARD_PADDING - self::PHRASE_FIELD_PADDING - $pdf->GetStringWidth(utf8_decode($this->config['author'])), 
                    $phraseFieldY + self::PHRASE_FIELD_HEIGHT - self::PHRASE_FIELD_PADDING,
                    utf8_decode($this->config['author'])
            );

            // Define a fonte e o tamanho do texto
            $pdf->SetFont(self::FONT, '', self::EXPIRY_TEXT_SIZE);
            
            // Posiciona a mensagem da data de validade (Válido até <dia> de <mês> de <ano>)
            $str = utf8_decode(
                    'Válido até ' 
                    . $this->config['expiry']->format('d') 
                    . ' de '
                    . self::MONTHS[(int) ($this->config['expiry']->format('n')) - 1] 
                    . ' de '
                    . $this->config['expiry']->format('Y')
            );
            $pdf->Text(
                    self::HORIZONTAL_MARGIN + self::CARD_WIDTH - self::CARD_PADDING - $pdf->GetStringWidth($str), 
                    $yCoordinate + self::CARD_HEIGHT - self::CARD_PADDING, 
                    $str
            );
            
            // coordenada y para construção da próxima carteirinha
            $yCoordinate += self::CARD_HEIGHT + self::GAP_BETWEEN_CARDS;  
        }
        
        return $pdf->Output('Carteirinhas', 'I', true);
    }

}
