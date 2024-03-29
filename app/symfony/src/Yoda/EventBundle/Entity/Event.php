<?php

namespace Yoda\EventBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Yoda\UserBundle\Entity\User;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Event
 *
 * @ORM\Table(name="yoda_event")
 * @ORM\Entity(repositoryClass="Yoda\EventBundle\Repository\EventRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Event
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255)
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="details", type="text")
     */
    private $details;

    /**
     * @ORM\ManyToOne (targetEntity="Yoda\UserBundle\Entity\User", inversedBy="events")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $owner;

    // propriedade para aprendizado
    // o sluggable faz com que o campo nunca seja diferente
    // do primeiro utilizado
    /**
     * @Gedmo\Slug (fields={"name"}, updatable=false)
     * @ORM\Column (type="string", length=255, unique=true)
     */
    protected $slug;

    // created e updated at
    // antes utilizava o @Gedmo\Timestampable (on="create")
    // agora utiuliza o prePersist funcao que roda antes de realizar o persist
    /**
     * @ORM\Column (type="datetime")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable (on="update")
     * @ORM\Column (type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany (targetEntity="Yoda\UserBundle\Entity\User")
     * @ORM\JoinTable (
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     */
    private $attendees;

    // Construtor para receber as relacoes

    public function __construct()
    {
        $this->attendees = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Event
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     *
     * @return Event
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return Event
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set details
     *
     * @param string $details
     *
     * @return Event
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param User $owner
     * @return Event
     */
    public function setOwner(User $owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }


    public function getSlug()
    {
        return $this->slug;
    }


    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getAttendees()
    {
        return $this->attendees;
    }

    public function hasAttendee(User $user)
    {
        return $this->getAttendees()->contains($user);
    }

    /**
     * @ORM\PrePersist
     */
    // funcao que roda antes do persist, porem não da pra usar outras dependencias
    public function prePersist()
    {
        if (!$this->getCreatedAt()) {
            $this->createdAt = new \DateTime();
        }
    }
}

