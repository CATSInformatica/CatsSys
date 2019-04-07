<?php

namespace Recruitment\Service;

use DateTime;
use InvalidArgumentException;
use Recruitment\Entity\RecruitmentStatus;
use Recruitment\Entity\Registration;
use Recruitment\Entity\RegistrationStatus;

/**
 * Description of RegistrationStatusService
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
trait RegistrationStatusService
{
    abstract protected function getEntityManager();

    /**
     *
     * @param Registration $registration
     * @param type $statusType
     * @param DateTime $timestamp
     * @throws InvalidArgumentException
     */
    protected function updateRegistrationStatus(Registration $registration, $statusType, DateTime $timestamp = null)
    {

        if (!RecruitmentStatus::statusTypeExists($statusType)) {
            throw new InvalidArgumentException('Situação inválida');
        }

        if ($timestamp === null) {
            $timestamp = new DateTime('now');
        }

        $em = $this->getEntityManager();

        $recStatus = $em->getRepository('Recruitment\Entity\RecruitmentStatus')->findOneBy(array(
            'statusType' => $statusType
        ));

        if ($recStatus !== null) {

            /**
             * retira o status anterior isCurrent = false
             */
            if ($registration->getRegistrationId() !== null) {
                $registration->getCurrentRegistrationStatus()->setIsCurrent(false);
            }

            // cria um status
            $regStatus = new RegistrationStatus();

            // associa o tipo e a data
            $regStatus->setRecruitmentStatus($recStatus)
                ->setTimestamp($timestamp);

            // associa o status à inscrição
            $registration->addRegistrationStatus($regStatus);
        }
    }

}
