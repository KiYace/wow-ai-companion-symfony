<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GameEventControllerTest extends WebTestCase
{
    public function testValidEventIsAccepted(): void
    {
        $client = static::createClient();

        $client->jsonRequest('POST', '/api/events', [
            'type' => 'player_died',
            'timestamp' => '2026-10-17T10:56:42Z',
            'data' => [
                'character' => 'Мистер Фокс',
                'level' => 14,
                'location' => 'Elwynn Forest',
            ],
        ]);

        self::assertResponseStatusCodeSame(200);
        self::assertResponseFormatSame('json');

        self::assertSame(
            [
                'status' => 'accepted',
                'event_type' => 'player_died',
            ],
            json_decode(
                $client->getResponse()->getContent(),
                true,
                512,
                JSON_THROW_ON_ERROR,
            ),
        );
    }

    public function testExtraAttributeIsIgnored(): void
    {
        $client = static::createClient();

        $client->jsonRequest('POST', '/api/events', [
            'type' => 'player_died',
            'timestamp' => '2026-10-19T12:00:00Z',
            'data' => [
                'character' => 'Мистер Фокс',
                'level' => 80,
                'location' => 'Stormwind',
            ],
            'extra_field' => 'something',
        ]);

        self::assertResponseStatusCodeSame(200);
    }

    #[DataProvider('invalidEvents')]
    public function testInvalidEvent(
        array $payload,
        int $expectedStatus,
    ): void {
        $client = static::createClient();

        $client->jsonRequest('POST', '/api/events', $payload);

        self::assertResponseStatusCodeSame($expectedStatus);
    }

    public static function invalidEvents(): iterable
    {
        yield 'missing type' => [
            [
                'timestamp' => '2026-10-17T10:56:42Z',
                'data' => [],
            ],
            422,
        ];

        yield 'empty type' => [
            [
                'type' => '',
                'timestamp' => '2026-10-17T10:56:42Z',
                'data' => [],
            ],
            422,
        ];

        yield 'unsupported type' => [
            [
                'type' => 'enemy_killed',
                'timestamp' => '2026-10-17T10:56:42Z',
                'data' => [],
            ],
            422,
        ];
    }

    public function testInvalidPlayerDiedDataReturnsViolations(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/events', [
            'type' => 'player_died',
            'timestamp' => '2026-10-19T12:00:00Z',
            'data' => [
                'character' => '',
                'level' => -5,
                'location' => 'Stormwind',
            ],
        ]);

        $response = json_decode(
            $client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $fields = array_column($response['violations'], 'field');

        self::assertResponseStatusCodeSame(422);
        self::assertArrayHasKey('violations', $response);
        self::assertContains('character', $fields);
        self::assertContains('level', $fields);
        self::assertCount(2, $response['violations']);
    }
}
