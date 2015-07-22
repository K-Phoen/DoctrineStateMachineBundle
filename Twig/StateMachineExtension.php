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
            new \Twig_SimpleFunction('is_status', array($this, 'isStatus')),
            new \Twig_SimpleFunction('has_property', array($this, 'hasProperty')),
            new \Twig_SimpleFunction('property', array($this, 'getProperty')),
            // undocumented
            new \Twig_SimpleFunction('current_state', array($this, 'getCurrentState')),
        );
    }

    public function getFilters()
    {
        return array(
            'can'           => new \Twig_Filter_Method($this, 'can'),
            'is_status'     => new \Twig_Filter_Method($this, 'isStatus'),
            'has_property'  => new \Twig_Filter_Method($this, 'hasProperty'),
            'property'  => new \Twig_Filter_Method($this, 'getProperty'),
            // undocumented
            'current_state' => new \Twig_Filter_Method($this, 'getCurrentState'),
        );
    }

    public function can($entity, $transition)
    {
        return $this->getCurrentState($entity)->can($transition);
    }

    public function isStatus($entity, $status)
    {
        return $this->getCurrentState($entity)->getName() === $status;
    }

    public function hasProperty($entity, $property)
    {
        return $this->getCurrentState($entity)->has($property);
    }

    public function getProperty($entity, $property)
    {
        return $this->getCurrentState($entity)->get($property);
    }

    public function getCurrentState($entity)
    {
        if (!$entity instanceof Stateful) {
            throw new \RuntimeException(sprintf('Expected Stateful object, %s given', get_class($entity)));
        }

        return $entity->getStateMachine()->getCurrentState();
    }

    public function getName()
    {
        return 'kphoen_state_machine';
    }
}
