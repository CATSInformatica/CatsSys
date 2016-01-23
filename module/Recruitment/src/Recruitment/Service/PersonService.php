<?php

namespace Recruitment\Service;

use Recruitment\Entity\Person;
use Recruitment\Entity\Registration;

/**
 * Description of PersonService
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
trait PersonService
{

    /**
     * @see Database\Service\EntityManagerService
     */
    abstract protected function getEntityManager();

    /**
     * @see Recruitment\Service\AddressService
     */
    abstract protected function adjustAddresses(Person $person);

    protected function adjustPerson(Registration $registration)
    {
        $em = $this->getEntityManager();

        $newPerson = $registration->getPerson();

        $person = $em->getRepository('Recruitment\Entity\Person')->findOneBy(array(
            'personCpf' => $newPerson->getPersonCpf(),
        ));
        // Se a pessoa já possui cadastro atualiza alguns dos dados
        if ($person !== null) {
            $person->setPersonPhone($newPerson->getPersonPhone());
            $person->setPersonEmail($newPerson->getPersonEmail());
            $person->setPersonSocialMedia($newPerson->getPersonSocialMedia());
            $person->setPersonRg($newPerson->getPersonRg());

            $this->adjustAddresses($newPerson);
            $person->addAddresses($newPerson->getAddresses());

            $registration->setPerson($person);
        } else {
            $this->adjustAddresses($newPerson);
            //imagem padrão do perfil
            $newPerson->setPersonPhoto();
        }
    }

}
