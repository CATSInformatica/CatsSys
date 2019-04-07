<?php
/*
 * Copyright (C) 2017 Márcio Dias <marciojr91@gmail.com>
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

namespace AdministrativeStructure\Controller;

use Database\Controller\AbstractEntityActionController;
use Exception;
use Recruitment\Entity\Recruitment;
use Recruitment\Entity\RecruitmentStatus;
use Recruitment\Form\SearchRegistrationsForm;
use Zend\View\Model\ViewModel;

/**
 * Permite criar documentos para voluntarios e ex-voluntarios
 * Ex: Termos de adesao, horas de trabalho e recisao.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class DocumentsController extends AbstractEntityActionController
{

    public function indexAction()
    {
        try {
            $em = $this->getEntityManager();

            $form = new SearchRegistrationsForm($em, Recruitment::VOLUNTEER_RECRUITMENT_TYPE);

            $form
                ->get('recruitment')
                ->setValue(Recruitment::ALL_VOLUNTEER_RECRUITMENTS);
            $form
                ->get('registrationStatus')
                ->setValueOptions([RecruitmentStatus::STATUSTYPE_VOLUNTEER => RecruitmentStatus::STATUSTYPEDESC_VOLUNTEER])
                ->setValue(RecruitmentStatus::STATUSTYPE_VOLUNTEER);

            return new ViewModel(array(
                'message' => null,
                'form' => $form,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema.',
                'form' => null,
            ));
        }
    }
}
