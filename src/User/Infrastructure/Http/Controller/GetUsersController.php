<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Http\Controller;

use App\Shared\Application\Bus\Query\QueryBusInterface;
use App\User\Application\FindAll\FindAllUsersQuery;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/users', methods: ['GET'])]
final class GetUsersController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        /** @var User[] $users */
        $users = $this->queryBus->dispatch(new FindAllUsersQuery());

        return new JsonResponse(
            array_map(fn (User $user) => $user->toPrimitives(), $users),
            Response::HTTP_OK,
        );
    }
}
