<?php

namespace KPhoen\DoctrineStateMachineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('k_phoen_doctrine_state_machine');

        $rootNode
            ->children()
                ->booleanNode('auto_injection')->defaultTrue()->end()
                ->booleanNode('auto_validation')->defaultTrue()->end()

                ->arrayNode('state_machines')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')
                                ->cannotBeEmpty()
                                ->isRequired()
                            ->end()
                            ->scalarNode('property')
                                ->cannotBeEmpty()
                                ->isRequired()
                            ->end()
                            ->arrayNode('states')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('type')
                                            ->cannotBeEmpty()
                                            ->defaultValue('normal')
                                            ->validate()
                                            ->ifNotInArray(array('initial', 'normal', 'final'))
                                                ->thenInvalid('Invalid state type "%s"')
                                            ->end()
                                        ->end()
                                        ->variableNode('properties')
                                            ->treatNullLike(array())
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()

                            ->arrayNode('transitions')
                                ->prototype('array')
                                    ->children()
                                        ->arrayNode('from')->prototype('variable')->end()->requiresAtLeastOneElement()->end()
                                        ->scalarNode('to')->cannotBeEmpty()->isRequired()->end()
                                    ->end()
                                ->end()
                            ->end()

                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
