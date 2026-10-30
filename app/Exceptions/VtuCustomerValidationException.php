<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * The provider rejected the purchase details (network, number, product or amount)
 * before anything was bought. The message is safe to show to the customer.
 */
class VtuCustomerValidationException extends RuntimeException
{
}
