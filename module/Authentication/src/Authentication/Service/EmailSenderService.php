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

use InvalidArgumentException;
use RuntimeException;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

/**
 * Implementa a funcionalidade de envio de emails.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class EmailSenderService implements EmailSenderServiceInterface
{

    /**
     *
     * @var SmtpOptions Configurações para o envio do email
     */
    protected $smtpOptions = null;

    /**
     *
     * @var Message Configuração da mensagem.
     */
    protected $message;

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
     * @cofig string Config do email. *criado na bim
     */
    
    protected $config = null;

    /**
     *
     * @var string Endereço de email.
     */
    protected $from = null;

    /**
     *
     * @var array Endereço de email do(s) destinatário(s).
     */
    protected $to = [];

    /**
     *
     * @var bool Define se o corpo do email aceitará conteúdo html ou apenas texto.
     */
    protected $isHtml = false;
    
    /**
     *
     * @name string Nome a ser colocado no envio do email. *criado BIM
     */
    protected $name = false;

    /**
     * {@inheritDoc}
     */
    public function hasConfig()
    {
        return $this->smtpOptions === null;
    }

    /**
     * {@inheritDoc}
     */
    public function send()
    {

        if ($this->smtpOptions === null) {
            throw new RuntimeException('Os parâmetros de configuração do email não foram definidos.');
        }

        if ($this->message === null) {
            throw new RuntimeException('A mensagem do email não foi configurada.');
        }

        if ($this->subject === null) {
            throw new RuntimeException('O assunto da mensagem não foi definido.');
        }

        if (empty($this->to)) {
            throw new RuntimeException('Nenhum destinatário foi definido.');
        }

        if ($this->from === null) {
            throw new RuntimeException('Nenhum remetente foi definido.');
        }

        if ($this->body === null) {
            throw new RuntimeException('O corpo da mensagem não foi definido.');
        }

        foreach ($this->to as $to) {
            $this->message->addTo($to);
        }

        // define se utilizará html ou não no corpo do texto.
        if ($this->isHtml) {
            $html = new MimePart($this->body);
            $html->type = "text/html";
            $body = new MimeMessage();
            $body->addPart($html);
            $this->message->setBody($body);
        } else {
            $this->message->setBody($this->body);
        }

        
        // faz o envio.
            try  {
                //Seta o path e arquivo para escrita
                $path = __DIR__ . '/../../../../../data/';
                $fileOption = $path . 'email/' . microtime(true) . '.option';
                
                //Passa os parametros necessarios para o array do file
                $encoding = json_encode([$this->config, $this->body, $this->subject, $this->to, $this->from, $this->name]);
                
                //Coloca o array no arquivo
                file_put_contents($fileOption, $encoding);
                
                $script = $path . 'script/sendEmail.php';
                
                //Chama a thread de execuçao de email.
                $result = shell_exec("php $script $fileOption  > /dev/null 2>/dev/null & ");
                
                return true;
            } catch (\Exception $ex) {
                echo $ex->getMessage();
                return false;
            }
        
    }

    /**
     * {@inheritDoc}
     */
    public function setConfig(array $config)
    {

        if (!key_exists('host', $config)) {
            throw new InvalidArgumentException('A configuração de email deve possuir a chave [host]');
        }

        if (!key_exists('connection_class', $config)) {
            throw new InvalidArgumentException('A configuração de email deve possuir a chave [connection_class]');
        }
        if (!key_exists('config', $config) || !is_array($config['config'])) {
            throw new InvalidArgumentException('A configuração de email deve possuir a chave [config]');
        }

        if (!key_exists('username', $config['config'])) {
            throw new InvalidArgumentException('A configuração de email deve possuir a chave [config][username]');
        }

        if (!key_exists('password', $config['config'])) {
            throw new InvalidArgumentException('A configuração de email deve possuir a chave [config][password]');
        }

        if (!key_exists('ssl', $config['config'])) {
            throw new InvalidArgumentException('A configuração de email deve possuir a chave [config][ssl]');
        }
        $this->config = $config;
        
        $smtpOptions = new SmtpOptions();
        $smtpOptions
            ->setHost($config['host'])
            ->setConnectionClass($config['connection_class'])
            ->setName($config['host'])
            ->setConnectionConfig(array(
                'username' => $config['config']['username'],
                'password' => $config['config']['password'],
                'ssl' => $config['config']['ssl'],
        ));

        $this->smtpOptions = $smtpOptions;

        return $this;
    }

    public function setSubject($subject)
    {
        if ($this->message === null) {
            $this->message = new Message();
            $this->message->setEncoding('UTF-8');
        }

        $this->message->setSubject($this->subject = $subject);

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

        if ($this->message === null) {
            $this->message = new Message();
            $this->message->setEncoding('UTF-8');
        }

        if ($name !== null) {
            $this->message->setFrom($this->from = $from, $name);
        } else {
            $this->message->setFrom($this->from = $from);
        }
        $this->name = $name;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addTo($to)
    {
        $this->to[] = $to;

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
