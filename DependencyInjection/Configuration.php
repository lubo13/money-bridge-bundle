<?php

/**
 * @package Money\Bridge\MoneyBridgeBundle
 * @author Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

declare(strict_types=1);

namespace Money\Bridge\DependencyInjection;

use Money\Bridge\Util\AmountFormatter;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('money_bridge');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('integer_part')
                    ->defaultValue(12)
                ->end()
                ->scalarNode('fractional_part')
                    ->defaultValue(AmountFormatter::MONEY_FRACTIONAL_PART)
                ->end()
                ->scalarNode('amount_validation_rule')
                    ->defaultValue('positive')
                ->end()
                ->scalarNode('default_currency_code')
                    ->defaultValue('EUR')
                ->end()
                ->arrayNode('allowed_currency_code')
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
