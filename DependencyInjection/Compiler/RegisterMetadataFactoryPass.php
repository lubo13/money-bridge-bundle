<?php

/**
 * @package Money\Bridge\MoneyBridgeBundle
 * @author Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

declare(strict_types=1);

namespace Money\Bridge\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterMetadataFactoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false !== $container->hasExtension('api_platform')) {
            $definition = new Definition('Money\Bridge\Metadata\Property\Factory\CurrencyPropertyChangeTypeMetadataFactory', [new Reference('Money\Bridge\Metadata\Property\Factory\CurrencyPropertyChangeTypeMetadataFactory.inner')]);
            $definition->setDecoratedService('api_platform.metadata.property.metadata_factory', null, -9);

            $container->setDefinition('Money\Bridge\Metadata\Property\Factory\CurrencyPropertyChangeTypeMetadataFactory', $definition);
        }
    }
}
