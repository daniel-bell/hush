<?php

namespace CAD\Bundle\HushBundle\Entity;

use JSONSerializable;
use Doctrine\ORM\Mapping as ORM;
use CAD\Bundle\HushBundle\Entity\Users;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\nnotation\Expose;

/**
 * UserRelationship
 *
 * @ORM\Table(name="relationships")})
 * @ORM\Entity
 */
class UserRelationship implements JSONSerializable
{
    /**
     * @ORM\ManyToMany(targetEntity="Users")
     * @ORM\JoinTable(name="user_relationships")
     */
    private $users;

    /**
     * @var string
     *
     * @ORM\Column(name="relationship_key", type="string", length=256, nullable=false)
     */
    private $relationshipKey;

    /**
     * @var string
     *
     * @ORM\Column(name="creator_user_key", type="string", length=256, nullable=false)
     */
    private $creatorUserKey;

    /**
     * @var string
     *
     * @ORM\Column(name="target_user_key", type="string", length=256, nullable=false)
     */
    private $targetUserKey;

    /**
     * @var string
     *
     * @ORM\Column(name="relationship_type", type="string", length=30, nullable=false)
     */
    private $relationshipType;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Set relationshipKey
     *
     * @param string $relationshipKey
     * @return UserRelationship
     */
    public function setRelationshipKey($relationshipKey)
    {
        $this->relationshipKey = $relationshipKey;

        return $this;
    }

    /**
     * Get relationshipKey
     *
     * @return string 
     */
    public function getRelationshipKey()
    {
        return $this->relationshipKey;
    }

    /**
     * Set creatorUserKey
     *
     * @param string $creatorUserKey
     * @return UserRelationship
     */
    public function setCreatorUserKey($creatorUserKey)
    {
        $this->creatorUserKey = $creatorUserKey;

        return $this;
    }

    /**
     * Get creatorUserKey
     *
     * @return string 
     */
    public function getCreatorUserKey()
    {
        return $this->creatorUserKey;
    }

    /**
     * Set targetUserKey
     *
     * @param string $targetUserKey
     * @return UserRelationship
     */
    public function setTargetUserKey($targetUserKey)
    {
        $this->targetUserKey = $targetUserKey;

        return $this;
    }

    /**
     * Get targetUserKey
     *
     * @return string 
     */
    public function getTargetUserKey()
    {
        return $this->targetUserKey;
    }

    /**
     * Set relationshipType
     *
     * @param string $relationshipType
     * @return UserRelationship
     */
    public function setRelationshipType($relationshipType)
    {
        $this->relationshipType = $relationshipType;

        return $this;
    }

    /**
     * Get relationshipType
     *
     * @return string 
     */
    public function getRelationshipType()
    {
        return $this->relationshipType;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the users in the relationship
     */
    public function getUsers() {
        return $this->users;
    }

    /**
     * Get creator user
     *
     * @return Users
     */
    public function getCreator()
    {
        return $this->creator_user;
    }


    /**
     * Set creator user
     *
     */
    public function setCreator($user)
    {
        $this->creator_user = $user;
    }

    /**
     * Add a user to the relationship
     */
    public function addUser(\CAD\Bundle\HushBundle\Entity\Users $user) {
        return $this->users[] = $user;
    }

    public function JSONSerialize() {
      $users = $this->getUsers()->toArray();

      return array( 
        'id' => $this->getId(),
        'creator_user' => array_pop($users),
        'target_user' => array_pop($users),
        'relationship_type' => $this->getRelationshipType()
      );
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Remove users
     *
     * @param \CAD\Bundle\HushBundle\Entity\Users $users
     */
    public function removeUser(\CAD\Bundle\HushBundle\Entity\Users $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Set creator_user
     *
     * @param \CAD\Bundle\HushBundle\Entity\Users $creatorUser
     * @return UserRelationship
     */
    public function setCreatorUser(\CAD\Bundle\HushBundle\Entity\Users $creatorUser = null)
    {
        $this->creator_user = $creatorUser;

        return $this;
    }

    /**
     * Get creator_user
     *
     * @return \CAD\Bundle\HushBundle\Entity\Users 
     */
    public function getCreatorUser()
    {
        return $this->creator_user;
    }
}
