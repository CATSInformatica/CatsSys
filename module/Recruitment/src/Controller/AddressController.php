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

namespace Recruitment\Controller;


use Database\Controller\AbstractEntityActionController;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Exception;
use Zend\View\Model\JsonModel;

/**
 * Description of AddressController
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class AddressController extends AbstractEntityActionController
{

    public function searchByZipcodeAction()
    {

        $request = $this->getRequest();

        try {

            if ($request->isPost()) {

                $data = $request->getPost();

                $em = $this->getEntityManager();

                $address = $em->getRepository('Recruitment\Entity\Address')->findOneBy(array(
                    'addressPostalCode' => $data['zipcode'],
                ));

                $hydrator = new DoctrineHydrator($em);

                $whitelist = array('addressState', 'addressCity', 'addressNeighborhood', 'addressStreet');
                $result = $address !== null ? array_intersect_key(
                        $hydrator->extract($address), array_flip($whitelist)
                    ) : null;


                return new JsonModel(array(
                    'response' => true,
                    'data' => $result,
                ));
            }

            return new JsonModel(array(
                'response' => false,
                'msg' => 'Requisição sem dados',
            ));
        } catch (Exception $ex) {

            return new JsonModel([
                'response' => false,
                'msg' => $ex->getMessage(),
            ]);
        }
    }

}
