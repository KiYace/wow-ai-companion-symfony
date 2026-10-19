<?php

declare(strict_types=1);

namespace App\Exception;

final class InvalidGameEventDataException extends \RuntimeException
{
    public function __construct(
        private readonly string $path,
        string $message,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function getPath(): string
    {
        return $this->path;
    }
}