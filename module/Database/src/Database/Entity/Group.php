<?php

namespace Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Group
 *
 * @ORM\Table(name="group", uniqueConstraints={@ORM\UniqueConstraint(name="groups_name_UNIQUE", columns={"group_name"})}, indexes={@ORM\Index(name="fk_groups__parent_idx", columns={"groups_parent"})})
 * @ORM\Entity
 */
class Group
{
    /**
     * @var integer
     *
     * @ORM\Column(name="group_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $groupId;

    /**
     * @var string
     *
     * @ORM\Column(name="group_name", type="string", length=100, nullable=true)
     */
    private $groupName;

    /**
     * @var \Database\Entity\Group
     *
     * @ORM\ManyToOne(targetEntity="Database\Entity\Group")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groups_parent", referencedColumnName="group_id")
     * })
     */
    private $groupsParent;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Database\Entity\AccessItem", inversedBy="groupGroup")
     * @ORM\JoinTable(name="group_has_access_item",
     *   joinColumns={
     *     @ORM\JoinColumn(name="group_group_id", referencedColumnName="group_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="access_item_access_item_id", referencedColumnName="access_item_id")
     *   }
     * )
     */
    private $accessItemAccessItem;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Database\Entity\User", inversedBy="groupGroup")
     * @ORM\JoinTable(name="group_has_user",
     *   joinColumns={
     *     @ORM\JoinColumn(name="group_group_id", referencedColumnName="group_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_user_id", referencedColumnName="user_id")
     *   }
     * )
     */
    private $userUser;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->accessItemAccessItem = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userUser = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get groupId
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set groupName
     *
     * @param string $groupName
     *
     * @return Group
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;

        return $this;
    }

    /**
     * Get groupName
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * Set groupsParent
     *
     * @param \Database\Entity\Group $groupsParent
     *
     * @return Group
     */
    public function setGroupsParent(\Database\Entity\Group $groupsParent = null)
    {
        $this->groupsParent = $groupsParent;

        return $this;
    }

    /**
     * Get groupsParent
     *
     * @return \Database\Entity\Group
     */
    public function getGroupsParent()
    {
        return $this->groupsParent;
    }

    /**
     * Add accessItemAccessItem
     *
     * @param \Database\Entity\AccessItem $accessItemAccessItem
     *
     * @return Group
     */
    public function addAccessItemAccessItem(\Database\Entity\AccessItem $accessItemAccessItem)
    {
        $this->accessItemAccessItem[] = $accessItemAccessItem;

        return $this;
    }

    /**
     * Remove accessItemAccessItem
     *
     * @param \Database\Entity\AccessItem $accessItemAccessItem
     */
    public function removeAccessItemAccessItem(\Database\Entity\AccessItem $accessItemAccessItem)
    {
        $this->accessItemAccessItem->removeElement($accessItemAccessItem);
    }

    /**
     * Get accessItemAccessItem
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAccessItemAccessItem()
    {
        return $this->accessItemAccessItem;
    }

    /**
     * Add userUser
     *
     * @param \Database\Entity\User $userUser
     *
     * @return Group
     */
    public function addUserUser(\Database\Entity\User $userUser)
    {
        $this->userUser[] = $userUser;

        return $this;
    }

    /**
     * Remove userUser
     *
     * @param \Database\Entity\User $userUser
     */
    public function removeUserUser(\Database\Entity\User $userUser)
    {
        $this->userUser->removeElement($userUser);
    }

    /**
     * Get userUser
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserUser()
    {
        return $this->userUser;
    }
}
