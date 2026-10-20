<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\InvalidGameEventDataException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener(event: KernelEvents::EXCEPTION)]
final class ApiExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ValidationFailedException) {
            $errors = [];
            $violations = $exception->getViolations();

            foreach ($violations as $violation) {
                $errors[] = [
                    'field' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }

            $response = new JsonResponse([
                'violations' => $errors,
            ], 422);

            $event->setResponse($response);
            return;
        }
        if ($exception instanceof InvalidGameEventDataException) {
            $event->setResponse(new JsonResponse([
                'violations' => $exception->getErrors(),
            ], 422));

            return;
        }
    }
}
