<?php

namespace Recruitment\Service;

use Recruitment\Entity\Person;

/**
 * Description of RelativeService
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
trait RelativeService
{

    /**
     * @see Database\Service\EntityManagerService
     */
    abstract protected function getEntityManager();

    /**
     * Faz verificações para evitar violações de restrição unique nos parentes
     * @param Person $person
     */
    protected function adjustRelatives(Person $person)
    {
        $em = $this->getEntityManager();
        $relatives = $person->getRelatives();
        foreach ($relatives as $relative) {

            $relativeId = $relative->getRelativeId();
            // se o responsável já existe então verifica se é o mesmo ou é diferente
            if ($relativeId !== null) {
                $rel = $em->getRepository('Recruitment\Entity\Relative')->findOneBy(array(
                    'person' => $person,
                    'relative' => $relative->getRelative(),
                ));

                // responsável encontrado na banco de dados
                if ($rel !== null) {
                    // responsável é diferente
                    if ($relativeId != $rel->getRelativeId()) {
                        $person->addRelative($rel);
                        $person->removeRelative($relative);
                        $em->detach($relative);
                    }

                    continue;
                }
            }

            // dados do responsável que foi cadastrado
            $rperson = $relative->getRelative();

            // verifica se ele existe no banco de dados
            $pers = $em->getRepository('Recruitment\Entity\Person')->findOneBy(array(
                'personCpf' => $rperson->getPersonCpf(),
            ));

            // se existe define o responsável
            if ($pers !== null) {
                $relative->setRelative($pers);
                $em->detach($rperson);
            }
        }
    }

}
