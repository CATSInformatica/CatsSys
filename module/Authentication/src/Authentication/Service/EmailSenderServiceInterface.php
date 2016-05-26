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

    /**
     * Verifica se o serviço de email já está configurado.
     * 
     * @return bool Retorna true se estiver configurado, caso contrário, false.
     */
    public function hasConfig();

    /**
     * Configura o serviço de email com os parâmetros em config/autoload/local.php.
     * 
     * $config = [
     *     'host' => 'smtp.gmail.com',
     *     'connection_class' => 'login',
     *     'config' => [
     *          'username' => 'myemail@gmail.com',
     *          'password' => 'mypassword',
     *          'ssl' => 'tls',
     *     ],
     * ]
     * 
     * @param array $config Parâmetros de configuração para o envio de emails.
     * @throws \InvalidArgumentException
     * @return EmailSenderServiceInterface Interface fluente.
     */
    public function setConfig(array $config);

    /**
     * Envia o email.
     * 
     * @return bool Retorna true se o email foi enviado com sucesso, caso contrário, false.
     */
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
     * @param string $to Endereço de email do destinatário.
     * @return EmailSenderServiceInterface Interface fluente.
     */
    public function addTo($to);

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
