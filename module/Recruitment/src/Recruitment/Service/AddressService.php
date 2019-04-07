<?php

namespace Recruitment\Service;

use Recruitment\Entity\Address;
use Recruitment\Entity\Person;

/**
 * Description of AddressService
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
trait AddressService
{
    abstract protected function getEntityManager();

    /**
     * Faz as verificações para evitar violações de restrição unique nos endereços
     *
     * @param Person $person
     * @return void
     */
    protected function adjustAddresses(Person $person)
    {
        $em = $this->getEntityManager();
        $addresses = $person->getAddresses();
        foreach ($addresses as $address) {
            $addr = $em->getRepository('Recruitment\Entity\Address')->findOneBy(array(
                'addressState' => $address->getAddressState(),
                'addressCity' => $address->getAddressCity(),
                'addressNeighborhood' => $address->getAddressNeighborhood(),
                'addressStreet' => $address->getAddressStreet(),
                'addressNumber' => $address->getAddressNumber(),
                'addressComplement' => $address->getAddressComplement(),
            ));

            $addressId = $address->getAddressId();

            if ($addressId === null) {
                // endereço existe mas não existe um id associado
                if ($addr !== null) {
                    $person->removeAddress($address);
                    $person->addAddress($addr);
                }
            } else {
                if ($addr !== null) {
                    // Endereço é atualizado para um endereço já cadastrado no banco de dados.
                    if ($addressId != $addr->getAddressId()) {
                        $person->addAddress($addr);
                    }
                    continue;
                } else {
                    // endereço é atualizado para um novo endereço não existente no banco de dados.
                    $nAddress = new Address();
                    $nAddress->setAddressPostalCode($address->getAddressPostalCode());
                    $nAddress->setAddressState($address->getAddressState());
                    $nAddress->setAddressCity($address->getAddressCity());
                    $nAddress->setAddressNeighborhood($address->getAddressNeighborhood());
                    $nAddress->setAddressStreet($address->getAddressStreet());
                    $nAddress->setAddressNumber($address->getAddressNumber());
                    $nAddress->setAddressComplement($address->getAddressComplement());
                    $person->addAddress($nAddress);
                }

                $person->removeAddress($address);
                $em->detach($address);
            }
        }
    }

}
