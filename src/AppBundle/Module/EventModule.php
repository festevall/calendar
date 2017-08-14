<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 14.08.17
 * Time: 17:09
 */

namespace AppBundle\Module;


use AppBundle\Entity\Events;
use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EventModule
{

    /**
     * @var ContainerInterface
     */
    private $_container;

    /**
     * @var object
     */
    private $_doctrine;

    /**
     * @var array
     */
    private $_errors = [];

    /**
     * EventModule constructor.
     * @param ContainerInterface|null $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->_container = $container;
        if(!empty($container)) {
            $this->_doctrine = $container->get('doctrine');
        }
    }

    /**
     * @param Events $event
     * @return bool
     */
    public function validateEventRequirements(Events $event) {
        return empty($event->getTitle()) || empty($event->getStart());
    }

    /**
     * @param User $user
     * @return array
     */
    public function getUserEvents(User $user) {
        $events = $result = [];
        if(!empty($user)) {
            $events = $user->getEvents();
        }

        if(!empty($events)) {
            foreach ($events as $key => $row) {
                $result[] = [
                    'id' => $row->getId(),
                    'title' => $row->getTitle(),
                    'start' => date('Y-m-d H:i:s', $row->getStart()->getTimestamp()),
                    'end' => date('Y-m-d H:i:s', $row->getEnd() ? $row->getEnd()->getTimestamp() : ''),
                    'description' => $row->getDescription(),
                    'status' => $row->getStatus(),
                    'color' => $row->getColor(),
                    'allDay' => false,
                    'editable' => true,
                    'resizable' => true
                ];
            }
        }

        return $result;
    }

    /**
     * @param $eventDetails
     * @return bool
     */
    public function saveEventDetails($eventDetails) {
        /** @var User $user */
        $user = $this->_container->get('security.context')->getToken()->getUser();

        $em = $this->_doctrine->getManager();

        $eventId = $eventDetails['event_id'];
        if(!empty($eventId)) {
            $event = $this->_doctrine->getRepository(Events::class)->find($eventId);
        }

        if(empty($event)) {
            $event = new Events();
        }

        $event->setTitle($eventDetails['title'])
            ->setStart(new \DateTime($eventDetails['starts_at']))
            ->setEnd(new \DateTime($eventDetails['ends_at']))
            ->setDescription($eventDetails['description'])
            ->setStatus('new')
            ->setColor($eventDetails['color'] ?: Events::DEFAULT_EVENT_COLOR)
            ->setUser($user)
        ;

        $this->validateEventRequirements($event);

        $em->persist($event);
        $em->flush();

        return true;
    }

    public function getErrors() {
        return $this->_errors;
    }

}