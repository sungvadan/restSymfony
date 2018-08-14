<?php

namespace AppBundle\Entity;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * Programmer
 *
 * @ORM\Table(name="battle_programmer")
 * @Serializer\ExclusionPolicy("all")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProgrammerRepository")
 */
class Programmer
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Serializer\Expose()
     * @ORM\Column(name="nickname", type="string", length=100, unique=true)
     * @Assert\NotBlank(message="Please enter a clever nickname")
     */
    private $nickname;

    /**
     * @var integer
     * @Serializer\Expose()
     * @ORM\Column(name="avatarNumber", type="integer")
     */
    private $avatarNumber;

    /**
     * @var string
     * @Serializer\Expose()
     * @ORM\Column(name="tagLine", type="string", length=255, nullable=true)
     */
    private $tagLine;

    /**
     * @var integer
     * @Serializer\Expose()
     * @ORM\Column(name="powerLevel", type="integer")
     */
    private $powerLevel = 0;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct($nickname = null, $avatarNumber = null)
    {
        $this->nickname = $nickname;
        $this->avatarNumber = $avatarNumber;
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
     * Set nickname
     *
     * @param string $nickname
     * @return Programmer
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Get nickname
     *
     * @return string 
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set avatarNumber
     *
     * @param integer $avatarNumber
     * @return Programmer
     */
    public function setAvatarNumber($avatarNumber)
    {
        $this->avatarNumber = $avatarNumber;

        return $this;
    }

    /**
     * Get avatarNumber
     *
     * @return integer 
     */
    public function getAvatarNumber()
    {
        return $this->avatarNumber;
    }

    /**
     * Set tagLine
     *
     * @param string $tagLine
     * @return Programmer
     */
    public function setTagLine($tagLine)
    {
        $this->tagLine = $tagLine;

        return $this;
    }

    /**
     * Get tagLine
     *
     * @return string 
     */
    public function getTagLine()
    {
        return $this->tagLine;
    }

    /**
     * Set powerLevel
     *
     * @param integer $powerLevel
     * @return Programmer
     */
    public function setPowerLevel($powerLevel)
    {
        $this->powerLevel = $powerLevel;

        return $this;
    }

    /**
     * Get powerLevel
     *
     * @return integer 
     */
    public function getPowerLevel()
    {
        return $this->powerLevel;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param USer $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }
}
