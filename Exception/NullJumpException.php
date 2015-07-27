<?php

namespace KPhoen\DoctrineStateMachineBundle\Exception;

use Finite\Exception\StateException;

/**
 * A state jump to the current state generates a NullJumpException
 */
class NullJumpException extends StateException
{
}
