<?php

namespace Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccessItem
 *
 * @ORM\Table(name="access_item", uniqueConstraints={@ORM\UniqueConstraint(name="access_itens_item_UNIQUE", columns={"access_item_name"})})
 * @ORM\Entity
 */
class AccessItem
{
    /**
     * @var integer
     *
     * @ORM\Column(name="access_item_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $accessItemId;

    /**
     * @var string
     *
     * @ORM\Column(name="access_item_name", type="string", length=100, nullable=false)
     */
    private $accessItemName;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Database\Entity\Group", mappedBy="accessItemAccessItem")
     */
    private $groupGroup;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groupGroup = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get accessItemId
     *
     * @return integer
     */
    public function getAccessItemId()
    {
        return $this->accessItemId;
    }

    /**
     * Set accessItemName
     *
     * @param string $accessItemName
     *
     * @return AccessItem
     */
    public function setAccessItemName($accessItemName)
    {
        $this->accessItemName = $accessItemName;

        return $this;
    }

    /**
     * Get accessItemName
     *
     * @return string
     */
    public function getAccessItemName()
    {
        return $this->accessItemName;
    }

    /**
     * Add groupGroup
     *
     * @param \Database\Entity\Group $groupGroup
     *
     * @return AccessItem
     */
    public function addGroupGroup(\Database\Entity\Group $groupGroup)
    {
        $this->groupGroup[] = $groupGroup;

        return $this;
    }

    /**
     * Remove groupGroup
     *
     * @param \Database\Entity\Group $groupGroup
     */
    public function removeGroupGroup(\Database\Entity\Group $groupGroup)
    {
        $this->groupGroup->removeElement($groupGroup);
    }

    /**
     * Get groupGroup
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroupGroup()
    {
        return $this->groupGroup;
    }
}
