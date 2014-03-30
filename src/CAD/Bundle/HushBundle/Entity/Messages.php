<?php

namespace CAD\Bundle\HushBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Messages
 *
 * @ORM\Table(name="messages", indexes={@ORM\Index(name="send_user_id", columns={"send_user_id"}), @ORM\Index(name="target_user_id", columns={"target_user_id"})})
 * @ORM\Entity
 */
class Messages
{
    /**
     * @var string
     *
     * @ORM\Column(name="message_key", type="string", length=256, nullable=false)
     */
    private $messageKey;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent_time", type="datetime", nullable=false)
     */
    private $sentTime;

    /**
     * @var string
     *
     * @ORM\Column(name="message_content", type="text", nullable=false)
     */
    private $messageContent;

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
     *
     * @ORM\ManyToOne(targetEntity="CAD\Bundle\HushBundle\Entity\Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="send_user_id", referencedColumnName="id")
     * })
     */
    private $sendUser;



    /**
     * Set messageKey
     *
     * @param string $messageKey
     * @return Messages
     */
    public function setMessageKey($messageKey)
    {
        $this->messageKey = $messageKey;

        return $this;
    }

    /**
     * Get messageKey
     *
     * @return string 
     */
    public function getMessageKey()
    {
        return $this->messageKey;
    }

    /**
     * Set sentTime
     *
     * @param \DateTime $sentTime
     * @return Messages
     */
    public function setSentTime($sentTime)
    {
        $this->sentTime = $sentTime;

        return $this;
    }

    /**
     * Get sentTime
     *
     * @return \DateTime 
     */
    public function getSentTime()
    {
        return $this->sentTime;
    }

    /**
     * Set messageContent
     *
     * @param string $messageContent
     * @return Messages
     */
    public function setMessageContent($messageContent)
    {
        $this->messageContent = $messageContent;

        return $this;
    }

    /**
     * Get messageContent
     *
     * @return string 
     */
    public function getMessageContent()
    {
        return $this->messageContent;
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
     * @return Messages
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
     * Set sendUser
     *
     * @param \CAD\Bundle\HushBundle\Entity\Users $sendUser
     * @return Messages
     */
    public function setSendUser(\CAD\Bundle\HushBundle\Entity\Users $sendUser = null)
    {
        $this->sendUser = $sendUser;

        return $this;
    }

    /**
     * Get sendUser
     *
     * @return \CAD\Bundle\HushBundle\Entity\Users 
     */
    public function getSendUser()
    {
        return $this->sendUser;
    }
}
