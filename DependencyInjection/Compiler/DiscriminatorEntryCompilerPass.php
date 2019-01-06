<?php

namespace OJezu\ConfigurableDiscriminationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Finds services tagged with "ojezu.configurable_discrimination" tag, and builds discriminator maps out of that.
 *
 * Discriminator map is then set in this package config parameters
 */
class DiscriminatorEntryCompilerPass implements CompilerPassInterface
{
    /**
     * Map has following format:
     *
     *  [
     *    `parent class name 1` => [
     *      'discriminator value 1' => 'child className 1',
     *      'discriminator value 2' => 'child className 2',
     *      'discriminator value 3' => 'child className 3',
     *      (...)
     *    ],
     *    `parent class name 2` => [
     *      'discriminator value 1' => 'child className 1',
     *      'discriminator value 2' => 'child className 2',
     *    ],
     *    (...)
     *  ]
     *
     * @var string[][]
     */
    private $map = [];

    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        $childrenClassesSpecs = $container->findTaggedServiceIds('ojezu.configurable_discrimination');

        foreach ($childrenClassesSpecs as $serviceName => $tags) {
            if (count($tags) !== 1) {
                throw new \Exception('Service can be tagged with only one »ojezu.configurable_discrimination« tag');
            }

            $childClassSpec = $tags[0];
            $childEntityName = $container->getDefinition($serviceName)->getClass();

            $discriminatorValue = $childClassSpec['discriminator_value'];

            // Doctrine at this point (in compiler pass) does not expose any good way to determine
            // which of parent classes is top of Entity hierarchy. So, at cost of a bigger parameter
            // just specify children for ALL parents.
            foreach (class_parents($childEntityName) as $parentClassName) {
                if (!array_key_exists($parentClassName, $this->map)) {
                    $this->map[$parentClassName] = [];
                }

                $this->map[$parentClassName][$discriminatorValue] = $childEntityName;
            }

        }

        $container->setParameter(
            'ojezu.configurable_discrimination.mapper',
            $this->map
        );
    }
}
