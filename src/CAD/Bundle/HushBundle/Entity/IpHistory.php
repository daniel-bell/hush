<?php

namespace CAD\Bundle\HushBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * IpHistory
 *
 * @ORM\Table(name="ip_history", indexes={@ORM\Index(name="user_id", columns={"user_id"})})
 * @ORM\Entity
 */
class IpHistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ip", type="integer", nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Ip
     */
    private $ip;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="access_time", type="datetime", nullable=false)
     */
    private $accessTime;

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
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;



    /**
     * Set ip
     *
     * @param integer $ip
     * @return IpHistory
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return integer 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set accessTime
     *
     * @param \DateTime $accessTime
     * @return IpHistory
     */
    public function setAccessTime($accessTime)
    {
        $this->accessTime = $accessTime;

        return $this;
    }

    /**
     * Get accessTime
     *
     * @return \DateTime 
     */
    public function getAccessTime()
    {
        return $this->accessTime;
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
     * Set user
     *
     * @param \CAD\Bundle\HushBundle\Entity\Users $user
     * @return IpHistory
     */
    public function setUser(\CAD\Bundle\HushBundle\Entity\Users $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \CAD\Bundle\HushBundle\Entity\Users 
     */
    public function getUser()
    {
        return $this->user;
    }
}
