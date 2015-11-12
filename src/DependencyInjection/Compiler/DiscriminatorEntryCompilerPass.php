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

            if (array_key_exists('parent_class', $childClassSpec) && $childClassSpec['parnet_class']) {
                $parentEntityName = $childClassSpec['parent_class'];
            } else {
                $parentEntityName = get_parent_class($childEntityName);
            }

            $discriminatorValue = $childClassSpec['discriminator_value'];

            if (!array_key_exists($parentEntityName, $this->map)) {
                $this->map[$parentEntityName] = [];
            }

            $this->map[$parentEntityName][$discriminatorValue] = $childEntityName;
        }

        $container->setParameter(
            'ojezu.configurable_discrimination.mapper',
            $this->map
        );
    }
}
