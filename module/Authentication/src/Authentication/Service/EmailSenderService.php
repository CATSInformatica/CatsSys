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


use Mailgun\Mailgun;

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
     * @name string Nome a ser colocado no envio do email.
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


    public function __construct(Mailgun $mailgun, string $domain)
    {
        $this->mailgun = $mailgun;
        $this->domain = $domain;
    }


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

        try {
            $data = [
                'from' => sprintf("%s <%s>", $this->name, $this->from),
                'to' => sprintf("%s <%s>", $this->to['name'], $this->to['email']),
                'subject' => $this->subject,
                'html' => $this->body,
            ];

            if($this->replyTo) {
                $data['h:Reply-To'] = sprintf("%s <%s>", $this->replyTo['name'], $this->replyTo['email']);
            }

            $res = $this->mailgun->sendMessage($this->domain, $data);
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
    public function setTo($email, $name)
    {
        $this->to = [
            'email' => $email,
            'name' => $name,
        ];

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setReplyTo($email, $name = null)
    {
        $this->replyTo = [
            'email' => $email,
            'name' => $name
        ];

        return $this;
    }
}
