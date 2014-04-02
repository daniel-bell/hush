<?php

namespace CAD\Bundle\HushBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Users
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class Users implements UserInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=256, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=256, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password_hash", type="string", length=256, nullable=false)
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
     */
    private $lastActivity;

    /**
     * @ORM\OneToMany(targetEntity="CAD\Bundle\HushBundle\Entity\UserRelationship", mappedBy="creatorUser)
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="creator_user_id")
     * })
     */
    private $user_relationships;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    public function __construct() {
        $this->user_relationships = new \Doctrine\Common\Collections\ArrayCollection();
    }

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

      return $this->user_relationships;
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
}
