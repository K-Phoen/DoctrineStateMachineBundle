<?php

namespace KPhoen\DoctrineStateMachineBundle\Twig;

use KPhoen\DoctrineStateMachineBehavior\Entity\Stateful;

class StateMachineExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('can', array($this, 'can')),
        );
    }

    public function getFilters()
    {
        return array(
            'can' => new \Twig_Filter_Method($this, 'can'),
        );
    }

    public function can($entity, $transition)
    {
        if (!$entity instanceof Stateful) {
            throw new \RuntimeException(sprintf('Expected Stateful object, %s given', get_class($entity)));
        }

        return $entity->getStateMachine()->can($transition);
    }

    public function getName()
    {
        return 'kphoen_state_machine';
    }
}
