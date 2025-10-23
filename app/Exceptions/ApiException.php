<?php

namespace App\Exceptions;

use Exception;

enum ApiErrorCode: string
{
    case ErrBadRequest = 'Bad request';
    case ErrForbidden = 'This action is unauthorized';
    case ErrUnauthorized = 'Unauthenticated request';
    case ErrNotFound = 'Resource not found';
    case ErrMethodNotAllowed = 'Request method not allowed';
    case ErrValidation = 'Validation error';
    case ErrUnknown = 'Unknown error';

    case ErrRevalidate = 'Failed to sync storefront';
}

/**
 * Exception for API-related errors.
 */
class ApiException extends Exception
{
    protected ApiErrorCode $errorCode;
    protected array $details;

    public function __construct(ApiErrorCode $errorCode, ?string $message = null, int $statusCode = 500, array $details = [])
    {
        if ($message == null) {
            $message = $errorCode->value;
        }

        parent::__construct($message, $statusCode);
        $this->code = $statusCode;
        $this->details = $details;
        $this->errorCode = $errorCode;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }
}
