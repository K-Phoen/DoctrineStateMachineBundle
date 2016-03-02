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
            new \Twig_SimpleFunction('current_state', array($this, 'getCurrentState')),
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('can', array($this, 'can')),
            new \Twig_SimpleFilter('isStatus', array($this, 'isStatus')),
            new \Twig_Filter_Method($this, 'property', array('getProperty')),
            new \Twig_Filter_Method($this, 'has_property', array('hasProperty')),
            new \Twig_Filter_Method($this, 'current_state', array('getCurrentState')),
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
