<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\GameEventRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class GameEventController
{
    #[Route('/api/events', name: 'api_events_create', methods: ['POST'], format: 'json')]
    public function __invoke(
        #[MapRequestPayload]
        GameEventRequest $event,
    ): JsonResponse {
        return new JsonResponse([
            'status' => 'accepted',
            'event_type' => $event->type,
        ]);
    }
}
