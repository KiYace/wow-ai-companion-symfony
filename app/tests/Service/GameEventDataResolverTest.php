<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\DTO\Event\PlayerDiedData;
use App\Service\GameEventDataResolver;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class GameEventDataResolverTest extends TestCase
{
    public function testResolvesPlayerDiedEvent(): void
    {
        $data = [
            'character' => 'Мистер Фокс',
            'level' => 14,
            'location' => 'Elwynn Forest',
        ];

        $expected = new PlayerDiedData(
            character: 'Мистер Фокс',
            level: 14,
            location: 'Elwynn Forest',
        );

        $denormalizer = $this->createMock(
            DenormalizerInterface::class,
        );

        $denormalizer
            ->expects(self::once())
            ->method('denormalize')
            ->with($data, PlayerDiedData::class)
            ->willReturn($expected);

        $resolver = new GameEventDataResolver($denormalizer);

        $result = $resolver->resolve('player_died', $data);

        self::assertSame($expected, $result);
    }

    public function testRejectsUnsupportedEventType(): void
    {
        $denormalizer = $this->createMock(
            DenormalizerInterface::class,
        );

        $denormalizer
            ->expects(self::never())
            ->method('denormalize');

        $resolver = new GameEventDataResolver($denormalizer);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs(
            'Unsupported event type: unknown_event',
        );

        $resolver->resolve('unknown_event', []);
    }
}
