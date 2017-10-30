<?php

namespace OJezu\ConfigurableDiscriminationBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events as ORMEvents;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * Listens for "loadClassMetadata" doctrine events, and if loaded class has defined children in map passed to
 * constructor, adds them to discriminatorMap of the loaded entity.
 */
class DiscriminatorListener implements EventSubscriber
{
    /**
     * @var array
     */
    protected $map;

    /**
     * @param array $map
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * @inheritdoc
     */
    public function getSubscribedEvents()
    {
        return [ORMEvents::loadClassMetadata];
    }

    /**
     * Extends loaded class' discriminatorMap
     * @inheritdoc
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        $metadata = $event->getClassMetadata();
        $className = $metadata->name;

        if ($className && $className === $metadata->rootEntityName && array_key_exists($className, $this->map)) {
            $discriminatorMap = $metadata->discriminatorMap;
            $discriminatorMap = $discriminatorMap + $this->map[$className];
            $metadata->setDiscriminatorMap($discriminatorMap);
        }
    }
}
