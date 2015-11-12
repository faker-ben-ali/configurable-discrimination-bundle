<?php

namespace OJezu\ConfigurableDiscriminationBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use OJezu\ConfigurableDiscriminationBundle\DependencyInjection\Compiler\DiscriminatorEntryCompilerPass;
use OJezu\ConfigurableDiscriminationBundle\DependencyInjection\ConfigurableDiscriminationExtension;

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
