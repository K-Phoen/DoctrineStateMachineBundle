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
  * `setFiniteState($state)`.

Entities using the `StatefulTrait` see the `setStateMachine` and
`getStateMachine` methods implemented and gain access to the following methods:
  * `can($transition)`: indicating if the given transition is allowed ;
  * and magic methods for each available transition (ie: `accept()`, `reject()`, etc).

The bundle also exposes a few Twig helpers:

```jinja
{# your template ... #}

{% if article|can('reject') %}
    <a class="btn btn-danger" href="{{ path('article_delete', article) }}">
        <i class="icon-trash"></i>
        {{ 'link_reject'|trans }}
    </a>
{% endif %}

{# this is strictly equivalent #}
{% if can(article, 'reject') %}
    <a class="btn btn-danger" href="{{ path('article_delete', article) }}">
        <i class="icon-trash"></i>
        {{ 'link_reject'|trans }}
    </a>
{% endif %}
```


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

The `Article` entity below is ready to be used as a `Stateful` entity.

```php
<?php

namespace Acme\FooBundle\Entity;

use KPhoen\DoctrineStateMachineBehavior\Entity\Stateful;
use KPhoen\DoctrineStateMachineBehavior\Entity\StatefulTrait;

class Article implements Stateful
{
    use StatefulTrait;

    /**
     * New article, not yet reviewed by anyone.
     */
    const STATE_NEW = 'new';

    /**
     * Article reviewed once, need another review to be validated.
     */
    const STATE_FIRST_REVIEW = 'reviewed';

    /**
     * Article reviewed by at least two person.
     */
    const STATE_ACCEPTED = 'accepted';

    /**
     * Article validated and published.
     */
    const STATE_PUBLISHED = 'published';

    /**
     * Article rejected.
     */
    const STATE_REJECTED = 'rejected';


    /**
     * define your fields here
     */


    /**
     * @var string
     */
    protected $state = self::STATE_NEW;


    /**
     * Set state
     *
     * @param  string  $state
     * @return Article
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Sets the object state.
     * Used by the StateMachine behavior
     *
     * @return string
     */
    public function getFiniteState()
    {
        return $this->getState();
    }

    /**
     * Sets the object state.
     * Used by the StateMachine behavior
     *
     * @param string $state
     */
    public function setFiniteState($state)
    {
        return $this->setState($state);
    }
}
```


## Installation

Install the behavior adding `kphoen/doctrine-state-machine-bundle` to your composer.json or from CLI:

```
$ php composer.phar require 'kphoen/doctrine-state-bundle:@stable'
```


## License

This bundle is released under the MIT license.
