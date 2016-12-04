<?php
/*
 * Copyright (C) 2016 Márcio Dias <marciojr91@gmail.com>
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

namespace Authentication\Service;

/**
 * Implementa a funcionalidade de envio de emails.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class EmailSenderService implements EmailSenderServiceInterface
{

    /**
     *
     * @var string Assunto da mensagem.
     */
    protected $subject = null;

    /**
     *
     * @var string Corpo da mensagem.
     */
    protected $body = null;

    /**
     *
     * @var string Endereço de email.
     */
    protected $from = null;
    
    /**
     *
     * @name string Nome a ser colocado no envio do email. *criado BIM
     */
    protected $name = false;

    /**
     *
     * @var array Endereço de email do(s) destinatário(s).
     */
    protected $to = [];

    /**
     *
     * @var array Pares endereço de email e nomes para resposta.
     */
    protected $replyTo = [];

    /**
     *
     * @var bool Define se o corpo do email aceitará conteúdo html ou apenas texto.
     */
    protected $isHtml = false;


    /**
     * {@inheritDoc}
     */
    public function send()
    {

        if ($this->subject === null) {
            throw new \RuntimeException('O assunto da mensagem não foi definido.');
        }

        if ($this->body === null) {
            throw new \RuntimeException('O corpo da mensagem não foi definido.');
        }
        
        if ($this->from === null) {
            throw new \RuntimeException('Nenhum remetente foi definido.');
        }

        if (empty($this->to)) {
            throw new \RuntimeException('Nenhum destinatário foi definido.');
        }

        // faz a requisição de envio.
        try {
            //define o caminho do conteúdo da requisição de email
            $path = __DIR__ . '/../../../../../data/';
            $fileOption = $path . 'email/' . microtime(true) . '.json';
            //
            $encodedContent = json_encode([
                'subject' => $this->subject,
                'from' => [
                    'email' => $this->from,
                    'name' => $this->name,
                ],
                'to' => $this->to,
                'replyTo' => $this->replyTo,
                'isHtml' => $this->isHtml,
                'body' => $this->body,
            ], JSON_PRETTY_PRINT);

            //Coloca o array no arquivo
            file_put_contents($fileOption, $encodedContent);

            $script = $path . 'script/sendEmail.php';

            //Chama a thread de execuçao de email.
            shell_exec("php $script $fileOption  > /dev/null 2>/dev/null & ");

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setFrom($from, $name = null)
    {
        $this->from = $from;
        $this->name = $name;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addTo($email, $name)
    {
        $this->to[] = [
            'email' => $email,
            'name' => $name,
        ];

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addReplyTo($email, $name = null)
    {
        $this->replyTo[] = [
            'email' => $email,
            'name' => $name
        ];

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setIsHtml($isHtml)
    {
        $this->isHtml = $isHtml;

        return $this;
    }
}
