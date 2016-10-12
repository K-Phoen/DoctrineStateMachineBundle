<?php

namespace KPhoen\DoctrineStateMachineBundle\StateMachine;

use Finite\Exception\StateException;
use Finite\State\StateInterface;
use Finite\Transition\TransitionInterface;
use KPhoen\DoctrineStateMachineBehavior\StateMachine\ExtendedStateMachine as BaseExtendedStateMachine;
use KPhoen\DoctrineStateMachineBundle\Exception\AmbiguousJumpException;
use KPhoen\DoctrineStateMachineBundle\Exception\NullJumpException;

class ExtendedStateMachine extends BaseExtendedStateMachine
{
    /**
     * Find matching transitions between initial and targeted states.
     *
     * @param string $initialState
     * @param string $targetedState
     *
     * @return TransitionInterface[]
     */
    private function getMatchingTransitions($initialState, $targetedState)
    {
        $matchingTransitions = array();

        foreach ($this->transitions as $transition) {
            if ($transition->getState() == $targetedState && in_array($initialState, $transition->getInitialStates())) {
                $matchingTransitions[] = $transition;
            }
        }

        return $matchingTransitions;
    }

    /**
     * Find the unique transition between initial and targeted states.
     *
     * @throws StateJumpException
     *
     * @param string $initialState
     * @param string $targetedState
     *
     * @return TransitionInterface
     */
    private function resolveTransition($initialState, $targetedState)
    {
        if ($initialState == $targetedState) {
            throw new NullJumpException();
        }

        $matchingTransitions = $this->getMatchingTransitions($initialState, $targetedState);

        if (count($matchingTransitions) != 1) {
            throw new AmbiguousJumpException();
        }

        return $matchingTransitions[0];
    }

    /**
     * Tells if moving to the given state is allowed.
     *
     * @param string|StateInterface $state
     *
     * @return bool
     */
    public function canJumpToState($state)
    {
        if ($state instanceof StateInterface) {
            $state = $state->getName();
        }

        // assert that the given state exists
        $this->getState($state);

        try {
            $transition = $this->resolveTransition($this->currentState->getName(), $state);
            return $this->can($transition);
        } catch (NullJumpException $ex) {
            // state jumping to current state is allowed as it does nothing
            return true;
        } catch (StateException $ex) {
            return false;
        }
    }

    /**
     * Moves to the given state if allowed.
     *
     * @param string|StateInterface $state
     *
     * @return bool
     */
    public function jumpToState($state)
    {
        if (!$state instanceof StateInterface) {
            $state = $this->getState($state);
        }

        if (!$this->canJumpToState($state)) {
            throw new StateException(sprintf('Can not jump from state "%s" to "%s".', $this->currentState->getName(), $state->getName()));
        }

        try {
            $transition = $this->resolveTransition($this->currentState->getName(), $state->getName());
            return $this->apply($transition->getName());
        } catch (NullJumpException $ex) {
            // do nothing if we try to jump to current state
            return;
        }
    }
}
