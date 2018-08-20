<?php

namespace AppBundle\EventListener;


use AppBundle\Entity\Programmer;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\Routing\RouterInterface;

class LinkSerialisationSubscriber implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onPostSerialize(ObjectEvent $event){
        /** @var JsonSerializationVisitor $visitor */
        $visitor = $event->getVisitor();
        /** @var Programmer $programmer */
        $programmer = $event->getObject();
        $visitor->addData('uri', $this->router->generate('api_programmers_show',array('nickname' => $programmer->getNickname())));
    }

    /**
     * Returns the events to which this class has subscribed.
     *
     * Return format:
     *     array(
     *         array('event' => 'the-event-name', 'method' => 'onEventName', 'class' => 'some-class', 'format' => 'json'),
     *         array(...),
     *     )
     *
     * The class may be omitted if the class wants to subscribe to events of all classes.
     * Same goes for the format key.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event' => 'serializer.post_serialize',
                'method' => 'onPostSerialize',
                'format' => 'json',
                'class' => 'AppBundle\Entity\Programmer'
            )
        );
    }
}