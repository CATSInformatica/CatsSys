<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of RecruitmentKnowAbout
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="recruitment_know_about")
 * @ORM\Entity
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
