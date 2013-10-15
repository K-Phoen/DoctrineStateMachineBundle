DoctrineStateMachineBundle
============================

Doctrine2 behavior adding a finite state machine in your entities.

The state machine implementation used is [Finite](https://github.com/yohang/Finite).


## Usage

`Stateful` entities have access to their own state machine. See [Finite's
documentation](https://github.com/yohang/Finite) for more details about it.

All `Stateful` entities must implement the following API:
    * `setStateMachine(StateMachine $stateMachine)` ;
    * `getStateMachine()` ;
    * `getFiniteState()` ;
    * `setFiniteState($state)` ;

Entities using the `StatefulTrait` see the `setStateMachine` and
`getStateMachine` methods implemented and gain access to the following methods:
    * `can($transition)`: indicating if the given transition is allowed ;
    * and magic methods for each available transition (ie: `accept()`, `reject()`, etc)


## Configuration

In your `app/config/config.yml` file, define your state machines:

```yaml
k_phoen_doctrine_state_machine:
    auto_injection:     true    # should we automatically inject state machines into hydrated objects?
    auto_validation:    true    # should we validate any status change before the persistence happens?

    state_machines:
        article_state_machine:
            class:      \Acme\FooBundle\Entity\Article
            property:   state
            states:
                new:        {type: initial}
                reviewed:   ~
                accepted:   ~
                published:  {type: final, properties: {printable: true}}
                rejected:   {type: final}
            transitions:
                review:     {from: [new], to: reviewed}
                accept:     {from: [reviewed], to: accepted}
                publish:    {from: [accepted], to: published}
                reject:     {from: [new, reviewed, accepted, published], to: rejected}
```

The state machines configuration is pretty straightforward. In addition to the
states and transitions, you just have to define the entity class and the state
column to use.

**Note:** The entity has to implement the `Stateful` interface. To ease the
implementation, you can use the `StatefulTrait` that comes bundled with the
behavior.


## Installation

Install the behavior adding `kphoen/doctrine-state-machine-bundle` to your composer.json or from CLI:

```
$ php composer.phar require 'kphoen/doctrine-state-bundle:@stable'
```


## License

This bundle is released under the MIT license.
