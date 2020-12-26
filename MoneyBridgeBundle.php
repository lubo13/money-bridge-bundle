<?php

/**
 * @package Money\Bridge\MoneyBridgeBundle
 * @author Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

declare(strict_types=1);

namespace Money\Bridge;

use Money\Bridge\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Money\Bridge\DependencyInjection\Compiler\RegisterMetadataFactoryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @package Money\Bridge\MoneyBridgeBundle
 * @author Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */
class MoneyBridgeBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        if (false !== $container->hasExtension('doctrine')) {
            $container->addCompilerPass(new DoctrineOrmMappingsPass());
        }

        $container->addCompilerPass(new RegisterMetadataFactoryPass());
    }
}
