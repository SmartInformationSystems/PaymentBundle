<?php

namespace SmartInformationSystems\PaymentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('smart_information_systems_payment');

        $rootNode->children()
            ->arrayNode('routes')
                ->children()
                    ->scalarNode('success')->isRequired()->end()
                    ->scalarNode('fail')->isRequired()->end()
                ->end()
            ->end()
            ->arrayNode('gateways')
                ->children()
                    ->arrayNode('yandex_kassa')
                        ->children()
                            ->scalarNode('url')->isRequired()->end()
                            ->scalarNode('shop_id')->isRequired()->end()
                            ->scalarNode('sc_id')->isRequired()->end()
                            ->scalarNode('shop_password')->isRequired()->end()
                        ->end()
                    ->end()
                    ->arrayNode('paymaster')
                        ->children()
                            ->scalarNode('url')->isRequired()->end()
                            ->scalarNode('shop_id')->isRequired()->end()
                            ->scalarNode('shop_password')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
