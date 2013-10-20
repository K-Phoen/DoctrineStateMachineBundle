<?php

namespace KPhoen\DoctrineStateMachineBundle\Twig;

use KPhoen\DoctrineStateMachineBehavior\Entity\Stateful;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class StateMachineExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('can', array($this, 'can')),
            new \Twig_SimpleFunction('isStatus', array($this, 'isStatus')),
        );
    }

    public function getFilters()
    {
        return array(
            'can'       => new \Twig_Filter_Method($this, 'can'),
            'isStatus'  => new \Twig_Filter_Method($this, 'isStatus'),
        );
    }

    public function can($entity, $transition)
    {
        if (!$entity instanceof Stateful) {
            throw new \RuntimeException(sprintf('Expected Stateful object, %s given', get_class($entity)));
        }

        return $entity->getStateMachine()->can($transition);
    }

    public function isStatus($entity, $state)
    {
        if (!$entity instanceof Stateful) {
            throw new \RuntimeException(sprintf('Expected Stateful object, %s given', get_class($entity)));
        }

        return $entity->getStateMachine()->getCurrentState()->getName() === $state;
    }

    public function getName()
    {
        return 'kphoen_state_machine';
    }
}
