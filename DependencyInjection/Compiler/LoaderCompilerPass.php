<?php

namespace KPhoen\DoctrineStateMachineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class LoaderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('kphoen.state_machine.factory')) {
            return;
        }

        $definition = $container->getDefinition('kphoen.state_machine.factory');

        foreach ($container->findTaggedServiceIds('state_machine.loader') as $id => $attributes) {
            $definition->addMethodCall('addLoader', array(new Reference($id)));
        }
    }
}
