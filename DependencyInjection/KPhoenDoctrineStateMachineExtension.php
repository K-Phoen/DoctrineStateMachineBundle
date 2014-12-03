<?php

namespace KPhoen\DoctrineStateMachineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class KPhoenDoctrineStateMachineExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // parse the configuration
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        // load the services
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('factories.yml');
        $loader->load('listeners.yml');

        $bundles = $container->getParameter('kernel.bundles');
        if (isset($bundles['TwigBundle'])) {
            $loader->load('twig.yml');
        }

        // register state machine loaders
        $this->registerStateMachines($config['state_machines'], $container);

        // disable useless listeners
        if (!$config['auto_injection']) {
            $container->removeDefinition('kphoen.state_machine.listener.injection');
        }

        if (!$config['auto_validation']) {
            $container->removeDefinition('kphoen.state_machine.listener.persistence');
        }
    }

    protected function registerStateMachines(array $machines, ContainerBuilder $container)
    {
        $persistenceListenerDef = $container->getDefinition('kphoen.state_machine.listener.persistence');

        foreach ($machines as $name => $config) {
            $container
                ->setDefinition('kphoen.state_machine.loader.'.$name, new DefinitionDecorator('kphoen.state_machine.array_loader'))
                ->replaceArgument(0, $config)
                ->addTag('state_machine.loader', array('state_machine' => $name))
            ;

            $persistenceListenerDef->addMethodCall('registerClass', array($config['class'], $config['property']));
        }
    }
}
