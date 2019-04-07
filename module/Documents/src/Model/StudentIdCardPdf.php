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
 * Description of StudentIdCardPdf
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class StudentIdCardPdf extends StudentsBoardPdf
{

    const MONTHS = [
        "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
    ];
    const PAPER_HEIGHT = 297; // mm

    // Dimensões da carteirinha (mm)
    const STUDENT_CARD_PADDING = 3.6;

    // Texto
    const TITLE = "Curso Assistencial Theodomiro Santiago";
    const SIGNATURE = "Assinatura do Presidente";
    const NAME_LABEL = "NOME";
    const RG_LABEL = "RG";

    // Tamanho do texto (pt)
    const LABEL_TEXT_SIZE   = 7.5;  // Campo do nome e do RG
    const PHRASE_TEXT_SIZE  = 7.0;  // Frase e autor
    const REGULAR_TEXT_SIZE = 6.5;  // Nome, RG e assinatura
    const EXPIRY_TEXT_SIZE  = 6.0;  // Data de validade
    // Dimensões do logo (mm)
    const LOGO_WIDTH    = 16;
    const LOGO_HEIGHT   = 0; // 0 = automático
    // Dimensões dos campos da frente da carteirinha (mm)
    const FRONT_FIELDS_WIDTH    = 54.5;
    const FRONT_FIELDS_HEIGHT   = 3.9;
    // Dimensões do campo da frase (mm)
    const PHRASE_FIELD_HEIGHT       = 22.12;
    const PHRASE_FIELD_PADDING      = 3;
    const PHRASE_FIELD_ROW_HEIGHT   = 4;


    private $config;

    public function __construct($config, $students)
    {
        $this->config = $config;
        parent::__construct($students);
    }

    /**
     * Monta as carteirinhas
     * @return FPDF
     */
    public function generatePdf()
    {
        $pdf = new FPDF('P', 'mm', 'A4');

        $y = self::VERTICAL_MARGIN;
        $cardsPerPage = (int)((self::PAPER_HEIGHT - 2 * self::VERTICAL_MARGIN)
                / (self::CARD_HEIGHT + self::GAP_BETWEEN_CARDS));

        foreach ($this->students as $i => $student) {
            if ($i % $cardsPerPage === 0) {
                $pdf->AddPage('P', 'A4');
                $y = self::VERTICAL_MARGIN;
            }

            $this->buildFront(
                $pdf,
                self::HORIZONTAL_MARGIN,
                $y,
                [
                    'bg' => self::BASE_PATH . '/public/img/' . $this->config['bg_img_url'],
                    'logo' => self::BASE_PATH . '/public/img/logo_branco.png',
                    'student-pic' => self::BASE_PATH . '/data/profile/' . $student['img_url']
                ],
                $student
            );

            $this->buildBack(
                $pdf,
                self::HORIZONTAL_MARGIN + self::CARD_WIDTH + self::GAP_BETWEEN_CARDS,
                $y,
                [
                    'bg' => self::BASE_PATH . '/public/img/' . $this->config['bg_img_url'],
                    'logo' => self::BASE_PATH . '/public/img/logo_branco.png'
                ],
                $this->config
            );

            // coordenada y para construção da próxima carteirinha
            $y += self::CARD_HEIGHT + self::GAP_BETWEEN_CARDS;
        }

        return $pdf->Output('Carteirinhas', 'I', true);
    }

    private function buildFront($pdf, $x, $y, $imgUrl, $student)
    {
        $frontFieldsX = self::HORIZONTAL_MARGIN + self::CARD_WIDTH - self::STUDENT_CARD_PADDING - self::FRONT_FIELDS_WIDTH;

        // Posiciona o background selecionado na frente da carteirinha
        $pdf->Image(
            $imgUrl['bg'],
            $x,
            $y,
            self::CARD_WIDTH,
            self::CARD_HEIGHT
        );

        // Posiciona o logo na frente
        $pdf->Image(
            $imgUrl['logo'],
            $x + self::STUDENT_CARD_PADDING,
            $y + self::STUDENT_CARD_PADDING,
            self::LOGO_WIDTH,
            self::LOGO_HEIGHT
        );

        // Define a fonte, o tamanho e a cor do texto
        $pdf->SetFont(self::FONT, '', self::TITLE_TEXT_SIZE);
        $pdf->SetTextColor(255);  // Texto branco

        // Posiciona o nome do cursinho na frente
        $titleWidth = $pdf->GetStringWidth(self::TITLE);
        $pdf->Text(
            $x + self::CARD_WIDTH - $titleWidth - self::STUDENT_CARD_PADDING,   // alinhamento a direita
            $y + 9.09,
            self::TITLE
        );

        // Posiciona a foto do aluno
        $pdf->Image(
            $imgUrl['student-pic'],
            $x + self::CARD_PADDING,
            $y + 19.40,
            self::STUDENT_PICTURE_WIDTH // largura
            // altura dimensionada automaticamente
        );

        $pdf->SetDrawColor(255);  // Borda branca
        $pdf->SetFillColor(255);  // Preenchimento branco

        // Define a fonte, o tamanho e a cor do texto
        $pdf->SetFont(self::FONT, '', self::LABEL_TEXT_SIZE);
        $pdf->SetTextColor(20/*% de preto*/);   // Texto cinza

        // Posiciona o rótulo e o campo do NOME do aluno
        $nameFieldY = $y + 24;
        $pdf->Text(
            $frontFieldsX,
            $nameFieldY - 1.35,
            self::NAME_LABEL
        );
        $pdf->Rect(
            $frontFieldsX,
            $nameFieldY,
            self::FRONT_FIELDS_WIDTH,
            self::FRONT_FIELDS_HEIGHT,
            'DF'    // desenha e preenche
        );

        // Posiciona o rótulo e o campo do RG do aluno
        $rgFieldY = $y + 34.85;
        $pdf->Text(
            $frontFieldsX,
            $rgFieldY - 1.35,
            self::RG_LABEL
        );
        $pdf->Rect(
            $frontFieldsX,
            $rgFieldY,
            self::FRONT_FIELDS_WIDTH,
            self::FRONT_FIELDS_HEIGHT,
            'DF'  // desenha e preenche
        );

        // Define a fonte e o tamanho do texto
        $pdf->SetFont(self::FONT, '', self::REGULAR_TEXT_SIZE);

        // Posiciona o NOME do aluno
        $pdf->Text(
            $frontFieldsX + 1.5,
            $nameFieldY + 2.82,
            utf8_decode($student['name'])
        );

        // Posiciona o RG do aluno
        $pdf->Text(
            $frontFieldsX + 1.5,
            $rgFieldY + 2.82,
            $student['rg']
        );
    }

    private function buildBack($pdf, $x, $y, $imgUrl, $config)
    {
        // Posiciona o background selecionado no verso da carteirinha
        $pdf->Image(
            $imgUrl['bg'],
            $x,
            $y,
            self::CARD_WIDTH,
            self::CARD_HEIGHT
        );
        // Posiciona o logo no verso
        $pdf->Image(
            $imgUrl['logo'],
            $x + self::STUDENT_CARD_PADDING,
            $y + self::STUDENT_CARD_PADDING,
            self::LOGO_WIDTH,
            self::LOGO_HEIGHT
        );

        // Define a fonte, o tamanho e a cor do texto
        $pdf->SetFont(self::FONT, '', self::TITLE_TEXT_SIZE);
        $pdf->SetTextColor(255);  // Texto branco
        //
        // Posiciona o nome do cursinho no verso
        $titleWidth = $pdf->GetStringWidth(self::TITLE);
        $pdf->Text(
            $x + self::CARD_WIDTH - $titleWidth - self::STUDENT_CARD_PADDING,   // alinhamento a direita
            $y + 9.09, self::TITLE
        );

        // Posiciona o campo da frase
        $phraseFieldY = $y + 17;
        $pdf->Rect(
            $x + self::STUDENT_CARD_PADDING,
            $phraseFieldY,
            self::CARD_WIDTH - 2 * self::STUDENT_CARD_PADDING,
            self::PHRASE_FIELD_HEIGHT,
            'DF'    // desenha e preenche
        );

        // Define a fonte e o tamanho do texto
        $pdf->SetFont(self::FONT, '', self::PHRASE_TEXT_SIZE);
        $pdf->SetTextColor(0);  // Texto preto

        // Posiciona a frase no verso da carteirinha
        $pdf->SetXY(
            $x + self::STUDENT_CARD_PADDING + self::PHRASE_FIELD_PADDING,
            $phraseFieldY + self::PHRASE_FIELD_PADDING
        );
        $pdf->MultiCell(
            self::CARD_WIDTH - 2 * self::STUDENT_CARD_PADDING - 2 * self::PHRASE_FIELD_PADDING,  // largura de cada celula
            self::PHRASE_FIELD_ROW_HEIGHT,  // altura de cada celula
            utf8_decode($config['phrase'])  // texto
        );

        // Posiciona o autor
        $authorTextWidth = $pdf->GetStringWidth(utf8_decode($config['author']));
        $pdf->Text(
            $x + self::CARD_WIDTH - self::STUDENT_CARD_PADDING - self::PHRASE_FIELD_PADDING - $authorTextWidth,
            $phraseFieldY + self::PHRASE_FIELD_HEIGHT - self::PHRASE_FIELD_PADDING,
            utf8_decode($config['author'])
        );

        // Posiciona uma linha acima do texto da assinatura
        $ySignatureTxt = $y + self::CARD_HEIGHT - self::STUDENT_CARD_PADDING;
        $pdf->Line(
            $x + self::CARD_WIDTH * 0.2,    // x inicial
            $ySignatureTxt - 4,             // y inicial
            $x + self::CARD_WIDTH * 0.8,    // x final
            $ySignatureTxt - 4,             // y final = y inicial
            'DF'    // desenha e preenche
        );

        // Define a fonte e o tamanho do texto
        $pdf->SetFont(self::FONT, '', self::REGULAR_TEXT_SIZE);

        // Posiciona a assinatura do presidente
        $pdf->Text(
            $x + (self::CARD_WIDTH - $pdf->GetStringWidth(self::SIGNATURE)) / 2,
            $ySignatureTxt,
            self::SIGNATURE
        );

        // Define a fonte e o tamanho do texto
        $pdf->SetFont(self::FONT, '', self::EXPIRY_TEXT_SIZE);

        // Posiciona a mensagem da data de validade (Válido até <dia> de <mês> de <ano>)
        $str = utf8_decode(
                'Válido até '
                . $config['expiry']->format('d')
                . ' de '
                . self::MONTHS[(int) ($config['expiry']->format('n')) - 1]
                . ' de '
                . $config['expiry']->format('Y')
        );
        $pdf->Text(
                self::HORIZONTAL_MARGIN + self::CARD_WIDTH - self::STUDENT_CARD_PADDING - $pdf->GetStringWidth($str),
                $y + self::CARD_HEIGHT - self::STUDENT_CARD_PADDING,
                $str
        );
    }

}
