<?php

namespace Authorization\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Resource
 *
 * @ORM\Table(name="resource")
 * @ORM\Entity
 */
class Resource
{
    /**
     * @var integer
     *
     * @ORM\Column(name="resource_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $resourceId;

    /**
     * @var string
     *
     * @ORM\Column(name="resource_name", type="string", length=100, nullable=false)
     */
    private $resourceName;



    /**
     * Get resourceId
     *
     * @return integer
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * Set resourceName
     *
     * @param string $resourceName
     *
     * @return Resource
     */
    public function setResourceName($resourceName)
    {
        $this->resourceName = $resourceName;

        return $this;
    }

    /**
     * Get resourceName
     *
     * @return string
     */
    public function getResourceName()
    {
        return $this->resourceName;
    }
}
