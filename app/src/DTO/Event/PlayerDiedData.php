<?php

declare(strict_types=1);

namespace App\DTO\Event;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class PlayerDiedData implements EventDataInterface
{
    public function __construct(
        #[Assert\NotBlank]
        public string $character,

        #[Assert\Positive]
        public int $level,

        #[Assert\NotBlank]
        public string $location,
    ) {
    }
}