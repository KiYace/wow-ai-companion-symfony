<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\GameEventRequest;
use App\Service\GameEventDataResolver;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class GameEventController
{
    public function __construct(
        private readonly GameEventDataResolver $resolver,
    ) {
    }

    #[Route('/api/events', name: 'api_events_create', methods: ['POST'], format: 'json')]
    public function __invoke(
        #[MapRequestPayload]
        GameEventRequest $request,
    ): JsonResponse {
        $this->resolver->resolve(
            $request->type,
            $request->data,
        );

        return new JsonResponse([
            'status' => 'accepted',
            'event_type' => $request->type,
        ]);
    }
}
