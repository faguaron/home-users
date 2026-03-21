<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Http\Controller;

use App\User\Application\Create\CreateUserCommand;
use App\User\Application\Create\CreateUserCommandHandler;
use App\User\Domain\Exception\UserAlreadyExistsException;
use App\User\Domain\ValueObject\UserId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/users', methods: ['POST'])]
final class CreateUserController
{
    public function __construct(
        private readonly CreateUserCommandHandler $handler,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON body.'], Response::HTTP_BAD_REQUEST);
        }

        $id = isset($data['id']) ? (string) $data['id'] : UserId::generate()->value();

        try {
            ($this->handler)(new CreateUserCommand(
                id: $id,
                name: (string) ($data['name'] ?? ''),
                firstSurname: (string) ($data['first_surname'] ?? ''),
                secondSurname: (string) ($data['second_surname'] ?? ''),
                dni: (string) ($data['dni'] ?? ''),
                email: (string) ($data['email'] ?? ''),
                phoneNumber: (string) ($data['phone_number'] ?? ''),
                bankAccountNumber: (string) ($data['bank_account_number'] ?? ''),
                dateOfBirth: (string) ($data['date_of_birth'] ?? ''),
            ));
        } catch (UserAlreadyExistsException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse(['id' => $id], Response::HTTP_CREATED);
    }
}
