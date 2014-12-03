<?php

namespace KPhoen\DoctrineStateMachineBundle\StateMachine;

use KPhoen\DoctrineStateMachineBehavior\StateMachine\ExtendedStateMachine as BaseExtendedStateMachine;
use Finite\Exception\StateException;
use Finite\State\StateInterface;

class ExtendedStateMachine extends BaseExtendedStateMachine
{
    /**
     * Find matching transitions between initial and targeted states.
     *
     * @param string $initialState
     * @param string $targetedState
     *
     * @return array
     */
    private function getMatchingTransitions($initialState, $targetedState)
    {
        $matchingTransitions = array();

        foreach($this->transitions as $transition) {
            if ($transition->getState() == $targetedState && in_array($initialState, $transition->getInitialStates())) {
                $matchingTransitions[] = $transition;
            }
        }

        return $matchingTransitions;
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

        $transitions = $this->getMatchingTransitions($this->currentState->getName(), $state);

        return count($transitions) == 1;
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
            throw new StateException(sprintf('Can not jump from state "%s" to "%s".',$this->currentState->getName(), $state->getName()));
        }

        $transitions = $this->getMatchingTransitions($this->currentState->getName(), $state);
        $this->apply($transitions[0]->getName());
    }
}
