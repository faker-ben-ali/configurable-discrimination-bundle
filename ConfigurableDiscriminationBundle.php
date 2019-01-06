<?php

namespace OJezu\ConfigurableDiscriminationBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use OJezu\ConfigurableDiscriminationBundle\DependencyInjection\Compiler\DiscriminatorEntryCompilerPass;

/**
 * @inheritdoc
 */

class ConfigurableDiscriminationBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DiscriminatorEntryCompilerPass());
    }
}
