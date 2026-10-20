<?php

declare(strict_types=1);

namespace App\Exception;

final class InvalidGameEventDataException extends \RuntimeException
{
    /**
     * @param list<array{field: string, message: string}> $errors
     */
    public function __construct(
        private readonly array $errors,
        string $message = 'Invalid game event data.',
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}