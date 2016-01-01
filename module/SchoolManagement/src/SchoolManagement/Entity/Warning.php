<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Warning
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="warning")
 * @ORM\Entity
 */
class Warning
{

    /**
     * ManyToOne Unidirectional
     * 
     * @var SchoolManagement\Entity\Enrollment
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="SchoolManagement\Entity\Enrollment")
     * @ORM\JoinColumn(name="enrollment_id", referencedColumnName="enrollment_id", nullable=false)
     */
    private $enrollment;

    /**
     *
     * @var SchoolManagement\Entity\WarningType
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="SchoolManagement\Entity\WarningType", inversedBy="warnings")
     * @ORM\JoinColumn(name="warning_type_id", referencedColumnName="warning_type_id", nullable=false)
     */
    private $warningType;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="warning_date", type="date", nullable=false)
     */
    private $warningDate;

    /**
     *
     * @var string
     * @ORM\Column(name="warning_comment", type="text", nullable=false)
     */
    private $warningComment;
    
    

}
