<?php

/*
 * Copyright (C) 2017 Gabriel Pereira <rickardch@gmail.com>
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

namespace Site\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Contact
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 * @ORM\Table(name="contact")
 * @ORM\Entity
 */
class Contact
{
    
    const POSITIONS_DESCRIPTION = [
        'Aluno',
        'Ex-aluno',
        'Voluntário',
        'Ex-voluntário',
        'Visitante do site',
    ];
    
    /**
     * @var integer
     * @ORM\Column(name="contact_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $contactId;
    
    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;
    
    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=50, nullable=true)
     */
    private $email;
    
    /**
     * @var integer
     * @ORM\Column(name="position", type="smallint", nullable=false)
     */
    private $position;
    
    /**
     * @var string
     * @ORM\Column(name="subject", type="string", length=100, nullable=false)
     */
    private $subject;
    
    /**
     * @var string
     * @ORM\Column(name="message", type="string", length=500, nullable=false)
     */
    private $message;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;
    
    /**
     * 
     * @return integer
     */
    function getContactId()
    {
        return $this->contactId;
    }

    /**
     * 
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * 
     * @return string
     */
    function getEmail()
    {
        return $this->email;
    }

    /**
     * 
     * @return integer
     */
    function getPosition()
    {
        return $this->position;
    }

    /**
     * 
     * @return string
     */
    function getSubject()
    {
        return $this->subject;
    }

    /**
     * 
     * @return string
     */
    function getMessage()
    {
        return $this->message;
    }
    

    /**
     * 
     * @return \DateTime
     */
    function getDate()
    {
        return $this->date;
    }

    /**
     * 
     * @param string $name
     * @return Contact
     */
    function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * 
     * @param string $email
     * @return Contact
     */
    function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * 
     * @param string $position
     * @return Contact
     */
    function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * 
     * @param string $subject
     * @return Contact
     */
    function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * 
     * @param string $message
     * @return Contact
     */
    function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
    
    /**
     * 
     * @param \DateTime $date
     * @return Contact
     */
    function setDate(\DateTime $date)
    {
        $this->date = $date;
        return $this;
    }
    
}
