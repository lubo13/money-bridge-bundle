<?php

/**
 * @package Money\Bridge\MoneyBridgeBundle
 * @author Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

declare(strict_types=1);

namespace Money\Bridge\DependencyInjection\Compiler;

use Symfony\Bridge\Doctrine\DependencyInjection\CompilerPass\RegisterMappingsPass;
use Symfony\Component\DependencyInjection\Definition;

class DoctrineOrmMappingsPass extends RegisterMappingsPass
{
    public function __construct()
    {
        $namespaces = [__DIR__ . '/../../Resources/config/doctrine-mapping' => 'Money'];
        $managerParameters = [];
        $enabledParameter = false;
        $aliasMap = ['MoneyBridgeBundle'];

        $locator = new Definition('Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator', [$namespaces, '.orm.xml']);
        $driver = new Definition('Doctrine\ORM\Mapping\Driver\XmlDriver', [$locator]);

        $managerParameters[] = 'doctrine.default_entity_manager';
        parent::__construct(
            $driver,
            ['Money'],
            $managerParameters,
            'doctrine.orm.%s_metadata_driver',
            $enabledParameter,
            'doctrine.orm.%s_configuration',
            'addEntityNamespace',
            $aliasMap
        );
    }
}
