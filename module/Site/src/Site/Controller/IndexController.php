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

namespace Site\Controller;

use Database\Controller\AbstractEntityActionController;
use Authentication\Service\EmailSenderServiceInterface;
use Zend\View\Model\ViewModel;
use Site\Form\ContactForm;
use Site\Entity\Contact;
use DateTime;

class IndexController extends AbstractEntityActionController
{
    /**
     *
     * @var EmailSenderServiceInterface Permite acessar o serviço de envio de emails.
     */
    protected $emailService;

    public function __construct(EmailSenderServiceInterface $emailService)
    {
        $this->emailService = $emailService;
    }


    /**
     * Página inicial do site
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $message = null;

        $contact = new Contact();
        $form = new ContactForm($em);
        $form->bind($contact);

        if ($request->isPost()) {

            $form->setData($request->getPost()->toArray());

            if ($form->isValid()) {
                $contact->setDate(new DateTime('now'));

                $bodyHeader =
                          'Nome: ' . ($contact->getName() ? $contact->getName() : 'Anônimo') . "\n"
                        . 'Email: ' . ($contact->getEmail() ? $contact->getEmail() : '-') . "\n"
                        . 'Data: ' . $contact->getDate()->format("d/m/Y") . "\n"
                        . "\n";

                $this
                    ->emailService
                    ->setSubject($contact->getSubject() . '[' . $contact->getPosition() . ']')
                    ->setBody($bodyHeader . $contact->getMessage())
                    ->send();

                $em->persist($contact);
                $em->flush();

                return new ViewModel(array(
                    'message' => $message,
                    'contactForm' => null
                ));
            }
        }
        return new ViewModel(array(
            'message' => $message,
            'contactForm' => $form,
        ));
    }

}
