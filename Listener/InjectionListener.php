<?php

namespace KPhoen\DoctrineStateMachineBundle\Listener;

use KPhoen\DoctrineStateMachineBehavior\Listener\InjectionListener as BaseInjectionListener;
use Doctrine\Common\Util\ClassUtils;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use KPhoen\DoctrineStateMachineBehavior\Entity\Stateful;

/**
 * Injects the state machines into stateful entities when they are loaded by
 * Doctrine or JMSSerializer.
 */
class InjectionListener extends BaseInjectionListener
{
    public function onPostDeserialize(ObjectEvent $event)
    {
        $entity = $event->getObject();

        if (!$entity instanceof Stateful) {
            return;
        }

        $this->injectStateMachine($entity);
    }
}
