<?php

namespace AtlanteGroup\CorporateVCardsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('corporate_v_cards');

        $rootNode
            ->children()
                ->arrayNode('config')
                    ->children()
                        ->scalarNode('mails_service')->defaultNull()->end()
                        ->arrayNode('favicons')
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->scalarNode('real_favicon_generator_api_key')->end()
                                ->scalarNode('dir')->end()
                            ->end()
                        ->end()
                        ->arrayNode('backgrounds')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('default')->isRequired()
                    ->children()
                        ->scalarNode('company')->defaultValue('')->end()
                        ->scalarNode('jobTitle')->defaultValue('')->end()
                        ->arrayNode('phone')
                            ->children()
                                ->scalarNode('mobile')->defaultValue('')->end()
                                ->scalarNode('work')->defaultValue('')->end()
                            ->end()
                        ->end()
                        ->arrayNode('address')
                            ->children()
                                ->scalarNode('name')->defaultValue('')->end()
                                ->scalarNode('extended')->defaultValue('')->end()
                                ->scalarNode('street')->defaultValue('')->end()
                                ->scalarNode('city')->defaultValue('')->end()
                                ->scalarNode('region')->defaultValue('')->end()
                                ->scalarNode('zip')->defaultValue('')->end()
                                ->scalarNode('country')->defaultValue('')->end()
                            ->end()
                        ->end()
                        ->scalarNode('url')->defaultValue('')->end()
                    ->end()
                ->end()
                ->arrayNode('profiles')->isRequired()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('firstName')->isRequired()->end()
                            ->scalarNode('lastName')->isRequired()->end()
                            ->scalarNode('company')->end()
                            ->scalarNode('jobTitle')->end()
                            ->scalarNode('email')->defaultValue('')->end()
                            ->arrayNode('phone')
                                ->children()
                                    ->scalarNode('mobile')->end()
                                    ->scalarNode('work')->end()
                                ->end()
                            ->end()
                            ->arrayNode('address')
                                ->children()
                                    ->scalarNode('name')->defaultValue('')->end()
                                    ->scalarNode('extended')->defaultValue('')->end()
                                    ->scalarNode('street')->end()
                                    ->scalarNode('city')->end()
                                    ->scalarNode('region')->end()
                                    ->scalarNode('zip')->end()
                                    ->scalarNode('country')->end()
                                ->end()
                            ->end()
                            ->scalarNode('photo')->defaultNull()->end()
                            ->scalarNode('url')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
