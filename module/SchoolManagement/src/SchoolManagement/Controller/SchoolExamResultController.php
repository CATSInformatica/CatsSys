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

namespace SchoolManagement\Controller;

use Database\Controller\AbstractEntityActionController;
use SchoolManagement\Entity\ExamApplication;
use Zend\View\Model\ViewModel;

/**
 * Correção de simulados a partir das respostas dos alunos e do gabarito oficial
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class SchoolExamResultController extends AbstractEntityActionController
{

    /**
     * Faz dos acertos a partir das planilhas de respostas dos alunos e do
     * gabarito oficial. Esta função está obsoleta
     * 
     * 
     * @return ViewModel
     */
    public function previewAction()
    {
        try {

            $em = $this->getEntityManager();

            $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')
                ->findByEndDateGratherThan(new \DateTime('now'));

            return new ViewModel([
                'classes' => $classes,
                'message' => null,
            ]);
        } catch (\Exception $ex) {
            return new ViewModel([
                'message' => $ex->getMessage(),
                'classes' => null,
            ]);
        }
    }

    public function uploadAnswersByClassAction()
    {

        try {

            $em = $this->getEntityManager();

            $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')
                ->findByEndDateGratherThan(new \DateTime());

            $applications = $em->getRepository('SchoolManagement\Entity\ExamApplication')
                ->findBy([
                'status' => ExamApplication::EXAM_APP_CREATED
            ], [
                'examApplicationId' => 'desc',
            ]);

            return new ViewModel([
                'classes' => $classes,
                'apps' => $applications,
                'message' => null,
            ]);
        } catch (Exception $ex) {
            return new ViewModel([
                'message' => $ex->getMessage(),
                'classes' => null,
            ]);
        }
    }

    public function uploadAnswersByStdRecruitmentAction()
    {
        return new ViewModel([
        ]);
    }
}
