<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Http\Controller;

use App\User\Application\Find\FindUserQuery;
use App\User\Application\Find\FindUserQueryHandler;
use App\User\Domain\Exception\UserNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/users/{id}', methods: ['GET'])]
final class GetUserController
{
    public function __construct(
        private readonly FindUserQueryHandler $handler,
    ) {
    }

    public function __invoke(string $id): JsonResponse
    {
        try {
            $user = ($this->handler)(new FindUserQuery($id));
        } catch (UserNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse($user->toPrimitives(), Response::HTTP_OK);
    }
}
