<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Event\EventDataInterface;
use App\DTO\Event\PlayerDiedData;
use App\Exception\InvalidGameEventDataException;
use InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class GameEventDataResolver
{
    private const array EVENT_MAP = [
        'player_died' => PlayerDiedData::class,
    ];

    public function __construct(
        private DenormalizerInterface $denormalizer,
        private ValidatorInterface $validator,
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

        try {
            $event = $this->denormalizer->denormalize(
                $data,
                $class,
            );
        } catch (NotNormalizableValueException $exception) {
            $message = sprintf(
                'Expected type %s, got %s.',
                implode('|', $exception->getExpectedTypes()),
                $exception->getCurrentType(),
            );

            throw new InvalidGameEventDataException(
                path: $exception->getPath(),
                message: $message,
                previous: $exception,
            );
        }


        $errors = $this->validator->validate($event);
        if (count($errors) > 0) {
            throw new ValidationFailedException($event, $errors);
        }

        return $event;
    }
}
