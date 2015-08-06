<?php

namespace KPhoen\DoctrineStateMachineBundle\Listener;

use Doctrine\Common\Inflector\Inflector;
use Finite\Event\FiniteEvents;
use Finite\Event\StateMachineEvent;
use Finite\Event\TransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Implements "life callbacks" for stateful entities.
 * The available callbacks are the following (all are optionnal):
 *
 *  * postInitialize:   called after the initial state is set ;
 *
 *  * can{Transition}:  called when the state machine checks if {Transition} can be applied.
 *  * pre{Transition}:  called before the transition {Transition} is applied ; [DEPRECATED]
 *  * post{Transition}: called after the transition {Transition} is applied ;
 *
 * @author alexandre
 */
abstract class AbstractSubscriber implements EventSubscriberInterface
{
    abstract public function supportsObject($object);

    /**
     * List of subscribed events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            FiniteEvents::SET_INITIAL_STATE => 'onSetInitialState',

            FiniteEvents::TEST_TRANSITION => 'onTestTransition',
            FiniteEvents::PRE_TRANSITION => 'onPreTransition',
            FiniteEvents::POST_TRANSITION => 'onPostTransition',
        );
    }

    public function onSetInitialState(StateMachineEvent $event)
    {
        $object = $event->getStateMachine()->getObject();
        $result = $this->callCallback($object, 'can', 'initialize');

        if ($result === false) {
            throw new \Finite\Exception\StateException('State initialization has been rejected.');
        }

        $this->callCallback($object, 'post', 'initialize');
    }

    public function onTestTransition(TransitionEvent $event)
    {
        $object = $event->getStateMachine()->getObject();
        $result = $this->callCallback($object, 'can', $event->getTransition()->getName());

        if ($result === false) {
            $event->reject();
        }
    }

    /**
     * @deprecated Can lead to errors
     */
    public function onPreTransition(TransitionEvent $event)
    {
        $object = $event->getStateMachine()->getObject();
        $this->callCallback($object, 'pre', $event->getTransition()->getName());
    }

    public function onPostTransition(TransitionEvent $event)
    {
        $object = $event->getStateMachine()->getObject();
        $this->callCallback($object, 'post', $event->getTransition()->getName());
    }

    /**
     * Try to call a state lifecycle callback
     *
     * @param \KPhoen\DoctrineStateMachineBehavior\Entity\Stateful $object
     * @param string $callbackPrefix
     * @param string $transitionName
     * @return mixed
     */
    protected function callCallback($object, $callbackPrefix, $transitionName)
    {
        $camelCasedName = Inflector::camelize($transitionName);
        $methodName = $callbackPrefix . $camelCasedName;

        if (!method_exists($this, $methodName)) {
            return;
        }

        if (!$this->supportsObject($object)) {
            return;
        }

        return call_user_func(array($this, $methodName), $object);
    }
}
