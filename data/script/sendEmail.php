<?php
if (file_exists(__DIR__ . '/../../../vendor/autoload.php')) {
    $loader = include __DIR__ . '/../../../vendor/autoload.php';
}
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

$inputFile = $argv[1];
$logFile = __DIR__ . '/sendEmail.log';
file_put_contents($logFile, "Reading file ".  basename($inputFile)."\n", FILE_APPEND);

$contentOption = file_get_contents($inputFile);
$parameters = json_decode($contentOption, true);



try {

    $mail = new Message();
    $mail->setEncoding('UTF-8');

    //Assunto
    $mail->setSubject($parameters['subject']);

    // se o conteúdo for texto html
    if ($parameters['isHtml']) {
        $html = new MimePart($parameters['body']);
        $html->type = "text/html";
        $body = new MimeMessage();
        $body->addPart($html);
    } else {
        $body = $parameters['body'];
    }
    
    $mail->setBody($body);

    $logTo = "Enviando email para:";
    
    //Destinatarios
    foreach ($parameters['to'] as $to) {
        $logTo .= " " . $to['name'] . "<". $to['email'] . ">";
        $mail->addTo($to['email'], $to['name']);
    }
    
    file_put_contents($logFile, "\t" . $logTo . "\n", FILE_APPEND);

    //Remetente
    $from = $parameters['from'];
    $mail->setFrom($from['email'], $from['name']);

    foreach ($parameters['replyTo'] as $replyTo) {
        $mail->addReplyTo($replyTo['email'], $replyTo['name']);
    }

    //Enviar
    $transport = new Sendmail();
    $transport->send($mail);

    file_put_contents($logFile, "\tOperação concluída \n", FILE_APPEND);

    return true;
} catch (\Exception $ex) {
    file_put_contents($logFile, "\t" . $ex->getMessage() . "\n", FILE_APPEND);
    return false;
}
       