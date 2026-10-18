<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Event\EventDataInterface;
use App\DTO\Event\PlayerDiedData;
use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class GameEventDataResolver
{
    private const array EVENT_MAP = [
        'player_died' => PlayerDiedData::class,
    ];

    public function __construct(
        private DenormalizerInterface $denormalizer,
    ) {
    }

    public function resolve(
        string $type,
        array $data,
    ): EventDataInterface {
        $class = self::EVENT_MAP[$type] ?? null;

        if ($class === null) {
            throw new InvalidArgumentException(
                sprintf('Unsupported event type: %s', $type),
            );
        }

        return $this->denormalizer->denormalize(
            $data,
            $class,
        );
    }
}
