<?php

/**
 * @package Money\Bridge\MoneyBridgeBundle
 * @author Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

declare(strict_types=1);

namespace Money\Bridge\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MoneyBridgeExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(\dirname(__DIR__) . '/Resources/config'));
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader->load('services.xml');
        $loader->load('serializer.xml');

        $container->setParameter('money_integer_part', $config['integer_part']);
        $container->setParameter('money_fractional_part', $config['fractional_part']);
        $container->setParameter('money_default_currency_code', $config['default_currency_code']);
        $container->setParameter('money_allowed_currency_code', $config['allowed_currency_code']);
        $container->setParameter('money_amount_validation_rule', $config['amount_validation_rule']);
    }
}
