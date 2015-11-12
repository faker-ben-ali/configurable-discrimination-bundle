<?php

namespace OJezu\ConfigurableDiscriminationBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events as ORMEvents;
use Doctrine\ORM\EntityManager;
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
     * Map has following format:
     *  [
     *    `parent class name 1` => [
     *      'discriminator value 1' => 'child className 1',
     *      'discriminator value 2' => 'child className 2',
     *      'discriminator value 3' => 'child className 3',
     *      (...)
     *    ],
     *    `className2` => [
     *     (...)
     *    ],
     *    (...)
     *  ]
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
        $class = $metadata->getReflectionClass();

        if ($class === null) {
            $class = new \ReflectionClass($metadata->getName());
        }

        $className = $class->getName();

        if (array_key_exists($className, $this->map)) {
            $discriminatorMap = $metadata->discriminatorMap;
            $discriminatorMap = array_merge($discriminatorMap, $this->map[$className]);
            $metadata->setDiscriminatorMap($discriminatorMap);
        }
    }
}
