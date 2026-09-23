<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown by an event listener to cancel the action that fired the event.
 * The message is shown to the user.
 */
class ActionBlocked extends RuntimeException
{
}
