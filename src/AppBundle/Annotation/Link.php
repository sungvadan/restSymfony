<?php

namespace AppBundle\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Link
{
    /**
     * @Required
     */
    public $name;

    /**
     * @Required
     */
    public $route;

    public $params = array();

}