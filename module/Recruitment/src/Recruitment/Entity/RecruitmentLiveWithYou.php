<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of RecruitmentLiveWithYou
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="recruitment_live_with_you")
 * @ORM\Entity(readOnly=true)
 */
class RecruitmentLiveWithYou
{

    /**
     * @var integer
     * @ORM\Column(name="recruitment_live_with_you_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $recruitmentLiveWithYouId;

    /**
     * @var string
     * @ORM\Column(name="recruitment_live_with_you_description", type="string", length=60, unique=true, nullable=false)
     */
    private $recruitmentLiveWithYouDescription;

    public function getRecruitmentLiveWithYouId()
    {
        return $this->recruitmentLiveWithYouId;
    }

    public function getRecruitmentLiveWithYouDescription()
    {
        return $this->recruitmentLiveWithYouDescription;
    }

}
