DoctrineStateMachineBundle
============================

Doctrine2 behavior adding a finite state machine in your entities.

The state machine implementation used is [Finite](https://github.com/yohang/Finite).


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
states and transitions, you just have to define the entity class and the column
used to store the state.

**Important:** the entity has to **implement the `Stateful` interface**.

To ease the implementation, you can use the `StatefulTrait` that comes bundled
with the behavior.


## Usage

`Stateful` entities have access to their own state machine. See [Finite's
documentation](https://github.com/yohang/Finite) for more details about it.

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
     * define your fields here
     */


    /**
     * @var string
     */
    protected $state = 'new';

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

Entities using the `StatefulTrait` see the `setStateMachine()` and
`getStateMachine()` methods implemented and gain access to the following
methods:
  * `can($transition)`: indicating if the given transition is allowed ;
  * and a few magic methods, based on the transition allowed by the
    state-machine:
      * `{TransitionName}()`: apply the transition {TransitionName} (ie: `accept()`, `reject()`, etc) ;
      * `can{TransitionName}()`: test if the transition {TransitionName} can be applied (ie: `canAccept()`, `canReject()`, etc).
      * `is{StateName}()`: test if the current state is {StatusName} (ie: `isAccepted()`, `isRejected()`, etc).


```php
<?php

$article = new Article();

$article->canAccept();
$article->canReject();
$article->can('accept');

$article->accept();
$article->publish();

$article->isAccepted();
$article->isRejected();
```


## Lifecyle callbacks

If you use the event-aware state-machine (which is the default one used by the
bundle), the extension provides a listener implementing "lifecyle callbacks"
for stateful entities.

For each available transition, three methods can be executed:
  * `pre{TransitionName}()`: called before the transition {TransitionName} is applied ;
  * `post{TransitionName}()`: called after the transition {TransitionName} is applied ;
  * `can{TransitionName}()`: called when the state-machine tests if the transition {TransitionName} can be applied.

```php
<?php

namespace Acme\FooBundle\Entity;

use KPhoen\DoctrineStateMachineBehavior\Entity\Stateful;
use KPhoen\DoctrineStateMachineBehavior\Entity\StatefulTrait;

class Article implements Stateful
{
    // previous code

    public function preAccept()
    {
        // your logic here
    }

    public function postAccept()
    {
        // your logic here
    }

    public function canAccept()
    {
        // your logic here
    }
}
```


## Twig

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

{% if article|isStatus('rejected') %}
    blabla
{% endif %}

{# this is strictly equivalent #}
{% if isStatus(article, 'rejected') %}
    blabla
{% endif %}
```


## Installation

Install the behavior adding `kphoen/doctrine-state-machine-bundle` to your composer.json or from CLI:

```
$ php composer.phar require 'kphoen/doctrine-state-bundle:@stable'
```


## License

This bundle is released under the MIT license.
