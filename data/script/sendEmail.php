<?php

if (file_exists(__DIR__ . '/../../../vendor/autoload.php')) {
    $loader = include __DIR__ . '/../../../vendor/autoload.php';
}

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

$fileOption = $argv[1];


$contentOption = file_get_contents($fileOption);

$decode = json_decode($contentOption, true);

//Recebendo parametros
$config = $decode[0];
$messageContent = $decode[1];
$messageSubject = $decode[2];
$toArray = $decode[3];
$from = $decode[4];
$name = $decode[5];




try {

    //Colocar no log
    file_put_contents(__DIR__ . '/sendEmail.log', "\n Email sendo enviado para " . $toArray[0], FILE_APPEND);

    $smtpOptions = new SmtpOptions();
    
    //Set configs
    $smtpOptions
            ->setHost($config['host'])
            ->setConnectionClass($config['connection_class'])
            ->setName($config['host'])
            ->setConnectionConfig(array(
                'username' => $config['config']['username'],
                'password' => $config['config']['password'],
                'ssl' => $config['config']['ssl'],
    ));

    $message = new Message();
    $message->setEncoding('UTF-8');

    //Assunto
    $message->setSubject($messageSubject);

    //Corpo da mensagem
    $html = new MimePart($messageContent);
    $html->type = "text/html";
    $body = new MimeMessage();
    $body->addPart($html);
    $message->setBody($body);

    //Destinatarios
    foreach ($toArray as $to) {
        $message->addTo($to);
    }

    //Remetente
    $message->setFrom($from, $name);
    
    //Enviar
    $transport = new Smtp($smtpOptions);
    $isSent = $transport->send($message);

    file_put_contents(__DIR__ . '/sendEmail.log', "Terminado. \n", FILE_APPEND);

    return true;
} catch (\Exception $ex) {

    file_put_contents(__DIR__ . '/sendEmail.log', $ex->getMessage() . "\n", FILE_APPEND);

    return false;
}
       