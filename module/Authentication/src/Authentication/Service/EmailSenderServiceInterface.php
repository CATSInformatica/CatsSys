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
 *
 * Interface para o envio de emails.
 * 
 * 
 * @author Márcio Dias <marciojr91@gmail.com>
 */
interface EmailSenderServiceInterface
{
    
    public function send();

    /**
     * Define o assunto da mensagem.
     * 
     * @param string $subject
     * @return EmailSenderServiceInterface Interface fluente.
     */
    public function setSubject($subject);

    /**
     * Define o remetente da mensagem.
     * 
     * @param string $from Endereço de email do remetente.
     * @param string $name Nome do remetente.
     * @return EmailSenderServiceInterface Interface fluente.
     */
    public function setFrom($from, $name = null);

    /**
     * Adiciona um destinatário.
     * 
     * @param string $email Endereço de email do destinatário.
     * @param string $name Nome do destinatário.
     * @return EmailSenderServiceInterface Interface fluente.
     */
    public function addTo($email, $name);
    
    /**
     * Define os emails de resposta que serão exibidos para o(s) destinatários.
     * 
     * @param string $email Email de resposta
     * @param string $name
     */
    public function addReplyTo($email, $name = null);

    /**
     * Define o corpo da menssagem.
     * 
     * @param string $body Corpo da mensagem.
     * @return EmailSenderServiceInterface Interface fluente.
     */
    public function setBody($body);

    /**
     * 
     * @param type $isHtml
     * @return EmailSenderServiceInterface Interface fluente.
     */
    public function setIsHtml($isHtml);
}
