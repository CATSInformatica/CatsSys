<?php

namespace Authentication\Factory\Controller\Helper;

use Authentication\Service\EmailSenderService;
use Authentication\Service\EmailSenderServiceInterface;

use Mailgun\Mailgun;

trait CreateEmailSenderService
{
    /**
     * Cria uma instância do serviço de email
     *
     * @param array $mailgunOptions configuração do Mailgun
     * @return EmailSenderServiceInterface
     */
    private function createEmailSenderService(array $mailgunOptions)
    {
        $mailgun = new Mailgun($mailgunOptions['api_key']);
        $emailService = new EmailSenderService($mailgun, $mailgunOptions['domain']);

        return $emailService;
    }
}