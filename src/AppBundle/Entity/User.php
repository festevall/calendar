<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;

class User extends BaseUser
{

    protected $id;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $events;


    /**
     * Add events
     *
     * @param \AppBundle\Entity\Events $events
     * @return User
     */
    public function addEvent(\AppBundle\Entity\Events $events)
    {
        $this->events[] = $events;

        return $this;
    }

    /**
     * Remove events
     *
     * @param \AppBundle\Entity\Events $events
     */
    public function removeEvent(\AppBundle\Entity\Events $events)
    {
        $this->events->removeElement($events);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvents()
    {
        return $this->events;
    }
}
