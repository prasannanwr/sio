<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * The requested VTU service is not sold through the configured provider yet.
 */
class VtuServiceUnavailableException extends RuntimeException
{
}
