<?php

namespace CAD\Bundle\HushBundle\Entity;

use JSONSerializable;
use Doctrine\ORM\Mapping as ORM;

/**
 * UserRelationship
 *
 * @ORM\Table(name="user_relationship", indexes={@ORM\Index(name="creator_user_id", columns={"creator_user_id"}), @ORM\Index(name="target_user_id", columns={"target_user_id"})})
 * @ORM\Entity
 */
class UserRelationship implements JSONSerializable
{
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
     * @var \CAD\Bundle\HushBundle\Entity\Users
     *
     * @ORM\ManyToOne(targetEntity="CAD\Bundle\HushBundle\Entity\Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="target_user_id", referencedColumnName="id")
     * })
     */
    private $targetUser;

    /**
     * @var \CAD\Bundle\HushBundle\Entity\Users
     */
    private $creatorUser;

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
     * Set targetUser
     *
     * @param \CAD\Bundle\HushBundle\Entity\Users $targetUser
     * @return UserRelationship
     */
    public function setTargetUser(\CAD\Bundle\HushBundle\Entity\Users $targetUser = null)
    {
        $this->targetUser = $targetUser;

        return $this;
    }

    /**
     * Get targetUser
     *
     * @return \CAD\Bundle\HushBundle\Entity\Users 
     */
    public function getTargetUser()
    {
        return $this->targetUser;
    }

    /**
     * Set creatorUser
     *
     * @param \CAD\Bundle\HushBundle\Entity\Users $creatorUser
     * @return UserRelationship
     */
    public function setCreatorUser(\CAD\Bundle\HushBundle\Entity\Users $creatorUser = null)
    {
        $this->creatorUser = $creatorUser;

        return $this;
    }

    /**
     * Get creatorUser
     *
     * @return \CAD\Bundle\HushBundle\Entity\Users 
     */
    public function getCreatorUser()
    {
        return $this->creatorUser;
    }

    public function JSONSerialize() {
      return array( 
        'id' => $this->getId(),
        'creator_user_id' => $this->getCreatorUser(),
        'target_user_id' => $this->getTargetUser(),
        'relationship_type' => $this->getRelationshipType()
      );
    }
}
