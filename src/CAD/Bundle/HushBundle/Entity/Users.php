<?php

namespace CAD\Bundle\HushBundle\Entity;

use CAD\Bundle\HushBundle\Entity\UserRelationship;
use JSONSerializable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Users
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Users implements UserInterface, JSONSerializable
{
    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=256, nullable=false)
     * @Assert\Length(
     *      min = "3",
     *      max = "24",
     *      minMessage = "Your first name must be at least {{ limit }} in characters length",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} in characters length"
     * )
     * @Assert\Regex("/^[A-Za-z0-9_]+$/")
     * @Expose
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=256, nullable=true)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password_hash", type="string", length=256, nullable=false)
     * @Assert\Length(
     *       min = "10",
     *       minMessage = "Your password must be at least {{ limit }} in characters length"
     * )
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=256, nullable=false)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar_file_path", type="string", length=256, nullable=true)
     * @Expose
     */
    private $avatarFilePath;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="locked", type="datetime", nullable=true)
     */
    private $locked;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_activity", type="datetime", nullable=true)
     * @Expose
     */
    private $lastActivity;

    /**
     * @var \Doctrine\Common\Collections\Collection<\HushBundle\Entity\UserRelationship>
     * @ORM\ManyToMany(targetEntity="UserRelationship", inversedBy="users")
     * @ORM\JoinTable(name="user_relationships",
     * joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="relationship_id", referencedColumnName="id")})
     */
    private $userRelationships;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Expose
     */
    private $id;

    /**
     * Set username
     *
     * @param string $username
     * @return Users
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Users
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get roles
      *
      * @return array
    */
    public function getRoles()
    {
        return array('ROLE_USER');
    } 

    /**
     * Set password
     *
     * @param string $password
     * @return Users
     */
    public function setpassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getpassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Users
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set avatarFilePath
     *
     * @param string $avatarFilePath
     * @return Users
     */
    public function setAvatarFilePath($avatarFilePath)
    {
        $this->avatarFilePath = $avatarFilePath;

        return $this;
    }

    /**
     * Get avatarFilePath
     *
     * @return string 
     */
    public function getAvatarFilePath()
    {
        return $this->avatarFilePath;
    }

    /**
     * Set locked
     *
     * @param \DateTime $locked
     * @return Users
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return \DateTime 
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set lastActivity
     *
     * @param \DateTime $lastActivity
     * @return Users
     */
    public function setLastActivity($lastActivity)
    {
        $this->lastActivity = $lastActivity;

        return $this;
    }

    /**
     * Get lastActivity
     *
     * @return \DateTime 
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * Get the relationships for a user
     */
    public function getRelationships() {

      return $this->userRelationships;
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

    public function eraseCredentials(){
    }

    public function equals(UserInterface $user) {
        if($this->id == $user->id){
            return true;
        }
        else{
            return false;
        }
    }

    public function JSONSerialize() {
      return array( 
        'id' => $this->getId(),
        'username' => $this->getUsername()
      );
	}

    public function __toString(){
        return $this->username;
    }
}