<?php

namespace KPhoen\DoctrineStateMachineBundle\Factory;

use Finite\Loader\LoaderInterface as Loader;
use Finite\Factory\SymfonyDependencyInjectionFactory as Basefactory;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class Factory extends Basefactory
{
    /**
     * @param string $name   The loader's name.
     * @param Loader $loader
     */
    public function addNamedLoader($name, Loader $loader)
    {
        $this->loaders[$name] = $loader;
    }

    /**
     * Returns an uninitialized StateMachine instance.
     *
     * @param string $stateMachineName The state machine name
     *
     * @return \Finite\StateMachine\StateMachineInterface
     */
    public function getNamed($stateMachineName)
    {
        if (!isset($this->loaders[$stateMachineName])) {
            throw new \RuntimeException(sprintf('Loader %s not found', $stateMachineName));
        }

        $loader = $this->loaders[$stateMachineName];
        $stateMachine = $this->createStateMachine();

        $loader->load($stateMachine);

        return $stateMachine;
    }
}
