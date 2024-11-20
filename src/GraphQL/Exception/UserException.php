<?php

namespace App\GraphQL\Exception;

use Exception;
use GraphQL\Error\ClientAware;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class UserException extends Exception implements ClientAware
{
    public const CATEGORY = 'userException';

    public const MESSAGE = 'Invalid data set';

    private ConstraintViolationListInterface $violations;

    public function __construct(ConstraintViolationListInterface $violations, ?Throwable $previous = null)
    {
        $this->violations = $violations;

        parent::__construct(self::MESSAGE, 0, $previous);
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return self::CATEGORY;
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
