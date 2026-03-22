<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Http\Controller;

use App\Shared\Application\Bus\Command\CommandBusInterface;
use App\User\Application\Update\UpdateUserCommand;
use App\User\Domain\Exception\UserAlreadyExistsException;
use App\User\Domain\Exception\UserNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/users/{id}', methods: ['PUT'])]
final class UpdateUserController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(Request $request, string $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON body.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->commandBus->dispatch(new UpdateUserCommand(
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
        } catch (UserNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (UserAlreadyExistsException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
