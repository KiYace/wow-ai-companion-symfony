<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class GameEventRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(choices: ['player_died'])]
        public string $type,

        #[Assert\NotBlank]
        #[Assert\DateTime(format: 'Y-m-d\TH:i:sP')]
        public string $timestamp,

        #[Assert\NotNull]
        public array $data,
    ) {
    }
}
