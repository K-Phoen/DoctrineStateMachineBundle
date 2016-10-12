<?php

namespace KPhoen\DoctrineStateMachineBundle\Exception;

use Finite\Exception\StateException;

/**
 * A state jump using an ambiguous transition generates an AmbiguousJumpException
 */
class AmbiguousJumpException extends StateException
{
}
