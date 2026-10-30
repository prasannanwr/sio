<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * A Monnify Bills Payment call that Monnify answered with a failure.
 */
class MonnifyBillsException extends RuntimeException
{
    public function __construct(string $message, protected int $httpStatus, protected string $responseCode = '')
    {
        parent::__construct($message, $httpStatus);
    }

    public function httpStatus(): int
    {
        return $this->httpStatus;
    }

    public function responseCode(): string
    {
        return $this->responseCode;
    }

    /**
     * A 5xx means Monnify may still have processed the request, so the result has
     * to be confirmed with a requery instead of being treated as a rejection.
     */
    public function outcomeUnknown(): bool
    {
        return $this->httpStatus >= 500;
    }
}
