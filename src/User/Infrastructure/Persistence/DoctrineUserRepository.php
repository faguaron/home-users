<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Persistence;

use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\UserDni;
use App\User\Domain\ValueObject\UserEmail;
use App\User\Domain\ValueObject\UserId;
use App\User\Infrastructure\Doctrine\UserDoctrineEntity;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function save(User $user): void
    {
        $primitives = $user->toPrimitives();
        $entity = $this->entityManager->find(UserDoctrineEntity::class, $primitives['id']);

        if (null === $entity) {
            $entity = new UserDoctrineEntity();
        }

        $entity->id = $primitives['id'];
        $entity->name = $primitives['name'];
        $entity->firstSurname = $primitives['first_surname'];
        $entity->secondSurname = $primitives['second_surname'];
        $entity->dni = $primitives['dni'];
        $entity->email = $primitives['email'];
        $entity->phoneNumber = $primitives['phone_number'];
        $entity->bankAccountNumber = $primitives['bank_account_number'];
        $entity->dateOfBirth = $user->dateOfBirth()->value();
        $entity->createdAt = $user->createdAt();
        $entity->updatedAt = $user->updatedAt();

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function findById(UserId $id): ?User
    {
        $entity = $this->entityManager->find(UserDoctrineEntity::class, $id->value());

        if (null === $entity) {
            return null;
        }

        return $this->toDomain($entity);
    }

    public function findAll(): array
    {
        $entities = $this->entityManager->getRepository(UserDoctrineEntity::class)->findAll();

        return array_map(fn (UserDoctrineEntity $entity) => $this->toDomain($entity), $entities);
    }

    public function delete(User $user): void
    {
        $entity = $this->entityManager->find(UserDoctrineEntity::class, $user->id()->value());

        if (null !== $entity) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        }
    }

    public function existsByEmail(UserEmail $email): bool
    {
        $count = $this->entityManager->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(UserDoctrineEntity::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email->value())
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function existsByEmailExcludingId(UserEmail $email, UserId $id): bool
    {
        $count = $this->entityManager->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(UserDoctrineEntity::class, 'u')
            ->where('u.email = :email')
            ->andWhere('u.id != :id')
            ->setParameter('email', $email->value())
            ->setParameter('id', $id->value())
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function existsByDni(UserDni $dni): bool
    {
        $count = $this->entityManager->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(UserDoctrineEntity::class, 'u')
            ->where('u.dni = :dni')
            ->setParameter('dni', $dni->value())
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function existsByDniExcludingId(UserDni $dni, UserId $id): bool
    {
        $count = $this->entityManager->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(UserDoctrineEntity::class, 'u')
            ->where('u.dni = :dni')
            ->andWhere('u.id != :id')
            ->setParameter('dni', $dni->value())
            ->setParameter('id', $id->value())
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    private function toDomain(UserDoctrineEntity $entity): User
    {
        return User::fromPrimitives(
            $entity->id,
            $entity->name,
            $entity->firstSurname,
            $entity->secondSurname,
            $entity->dni,
            $entity->email,
            $entity->phoneNumber,
            $entity->bankAccountNumber,
            $entity->dateOfBirth->format('Y-m-d'),
            $entity->createdAt,
            $entity->updatedAt,
        );
    }
}
