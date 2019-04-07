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

namespace Recruitment\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ORM da tabela `recruitment_know_about`.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="recruitment_know_about")
 * @ORM\Entity(readOnly=true)
 */
class RecruitmentKnowAbout
{

    /**
     * @var integer
     * @ORM\Column(name="recruitment_know_about_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $recruitmentKnowAboutId;

    /**
     *
     * @var string
     * @ORM\Column(name="recruitment_know_about_description", type="string", length=60, unique=true, nullable=false)
     */
    private $recruitmentKnowAboutDescription;

    public function getRecruitmentKnowAboutId()
    {
        return $this->recruitmentKnowAboutId;
    }

    public function getRecruitmentKnowAboutDescription()
    {
        return $this->recruitmentKnowAboutDescription;
    }

}
