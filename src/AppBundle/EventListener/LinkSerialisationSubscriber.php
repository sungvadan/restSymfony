<?php

namespace AppBundle\EventListener;


use AppBundle\Annotation\Link;
use AppBundle\Entity\Programmer;
use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Routing\RouterInterface;

class LinkSerialisationSubscriber implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    public function __construct(RouterInterface $router, Reader $annotationReader )
    {
        $this->router = $router;
        $this->annotationReader = $annotationReader;
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public function onPostSerialize(ObjectEvent $event){
        /** @var JsonSerializationVisitor $visitor */
        $visitor = $event->getVisitor();

        $object =  $event->getObject();
        $annotations = $this->annotationReader
            ->getClassAnnotations(new \ReflectionObject($object));
        $links = array();
        foreach ($annotations as $annotation){
            if($annotation instanceof  Link){

                $uri = $this->router->generate(
                    $annotation->route,
                    $this->resolveParams($annotation->params, $object)
                );
                $links[$annotation->name] = $uri;
            }



        }
        if($links){
            $visitor->addData('_links',$links );
        }

    }

    private function resolveParams($params, $object)
    {
        foreach ($params as $key => $val)
        {
            $params[$key] = $this->expressionLanguage
                ->evaluate($val, array('object' => $object));
        }
        return $params;
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
            )
        );
    }
}