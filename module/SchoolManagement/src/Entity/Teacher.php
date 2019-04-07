<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Entity;

/**
 * Description of Teacher
 *

 */
class Teacher
{
    /*
     *  * @author Gabriel Pereira <rickardch@gmail.com>
     * @ORM\Table(name="teacher")
     * @ORM\Entity
     */

    const TEACHER_TYPE_INSTRUCTOR = 0;
    const TEACHER_TYPE_TUTOR = 1;

    private $teacherId;
    private $teacherType;
    private $registration;
    private $subject;
    private $timestamp;
    private $layoff;

}
