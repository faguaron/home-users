<?php

declare(strict_types=1);

namespace Tests\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\RawMinkContext;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Assert;
use Ramsey\Uuid\Uuid;
use Tests\Behat\Support\SchemaValidator;

final class UserApiContext extends RawMinkContext implements Context
{
    private int $statusCode = 0;
    private mixed $responseBody = null;

    /** @var array<string, string> */
    private array $userIds = [];

    public function __construct(
        private readonly SchemaValidator $schemaValidator,
        private readonly Connection $connection,
    ) {
    }

    // ------------------------------------------------------------------ hooks

    /** @BeforeScenario */
    public function cleanDatabase(BeforeScenarioScope $scope): void
    {
        $this->connection->executeStatement('DELETE FROM users');
        $this->userIds = [];
        $this->statusCode = 0;
        $this->responseBody = null;
    }

    // ------------------------------------------------------------------ step definitions

    /** @Given there are no users in the system */
    public function thereAreNoUsers(): void
    {
        // Already ensured by BeforeScenario hook
    }

    /** @Given a user exists with alias :alias */
    public function aUserExistsWithAlias(string $alias): void
    {
        $id = Uuid::uuid4()->toString();
        $this->userIds[$alias] = $id;

        $suffix = count($this->userIds);
        $payload = $this->defaultUserPayload($id);
        $payload['email'] = "user{$suffix}@example.com";
        $payload['dni'] = $this->generateDni($suffix);

        $this->doRequest('POST', '/api/users', $payload);

        if ($this->statusCode !== 201) {
            throw new \RuntimeException(
                sprintf("Failed to create user '%s': status %d, body: %s", $alias, $this->statusCode, json_encode($this->responseBody))
            );
        }
    }

    /** @When I request GET :path */
    public function iRequestGet(string $path): void
    {
        $this->doRequest('GET', $path);
    }

    /** @When I create a user with valid data and alias :alias */
    public function iCreateUserWithAlias(string $alias): void
    {
        $id = Uuid::uuid4()->toString();
        $this->userIds[$alias] = $id;
        $this->doRequest('POST', '/api/users', $this->defaultUserPayload($id));
    }

    /** @When I create a user with the same email as :alias */
    public function iCreateUserWithSameEmailAs(string $alias): void
    {
        $suffix = count($this->userIds) + 1;
        $newId = Uuid::uuid4()->toString();
        $payload = $this->defaultUserPayload($newId);
        $payload['email'] = "user1@example.com";
        $payload['dni'] = $this->generateDni($suffix + 10);
        $this->doRequest('POST', '/api/users', $payload);
    }

    /** @When I create a user with invalid DNI */
    public function iCreateUserWithInvalidDni(): void
    {
        $payload = $this->defaultUserPayload(Uuid::uuid4()->toString());
        $payload['dni'] = 'INVALID';
        $this->doRequest('POST', '/api/users', $payload);
    }

    /** @When I request GET for user :alias */
    public function iRequestGetForUser(string $alias): void
    {
        $this->doRequest('GET', '/api/users/' . $this->userIds[$alias]);
    }

    /** @When I request GET for a non-existent user id */
    public function iRequestGetNonExistent(): void
    {
        $this->doRequest('GET', '/api/users/00000000-0000-0000-0000-000000000000');
    }

    /** @When I request GET for an invalid UUID */
    public function iRequestGetInvalidUuid(): void
    {
        $this->doRequest('GET', '/api/users/not-a-valid-uuid');
    }

    /** @When I update user :alias with valid data */
    public function iUpdateUser(string $alias): void
    {
        $payload = $this->defaultUserPayload($this->userIds[$alias]);
        $payload['name'] = 'UpdatedName';
        $this->doRequest('PUT', '/api/users/' . $this->userIds[$alias], $payload);
    }

    /** @When I update user :alias with invalid DNI */
    public function iUpdateUserWithInvalidDni(string $alias): void
    {
        $payload = $this->defaultUserPayload($this->userIds[$alias]);
        $payload['dni'] = 'INVALID';
        $this->doRequest('PUT', '/api/users/' . $this->userIds[$alias], $payload);
    }

    /** @When I update user :alias changing email to match :otherAlias */
    public function iUpdateUserEmailToMatchOther(string $alias, string $otherAlias): void
    {
        $suffix = array_search($alias, array_keys($this->userIds), true) + 1;
        $otherSuffix = array_search($otherAlias, array_keys($this->userIds), true) + 1;

        $payload = $this->defaultUserPayload($this->userIds[$alias]);
        $payload['email'] = "user{$otherSuffix}@example.com";
        $payload['dni'] = $this->generateDni($suffix + 20);
        $this->doRequest('PUT', '/api/users/' . $this->userIds[$alias], $payload);
    }

    /** @When I update a non-existent user */
    public function iUpdateNonExistentUser(): void
    {
        $nonExistentId = '00000000-0000-0000-0000-000000000000';
        $payload = $this->defaultUserPayload($nonExistentId);
        $this->doRequest('PUT', '/api/users/' . $nonExistentId, $payload);
    }

    /** @When I delete user :alias */
    public function iDeleteUser(string $alias): void
    {
        $this->doRequest('DELETE', '/api/users/' . $this->userIds[$alias]);
    }

    /** @When I delete a non-existent user */
    public function iDeleteNonExistentUser(): void
    {
        $this->doRequest('DELETE', '/api/users/00000000-0000-0000-0000-000000000000');
    }

    /** @When I delete a user with an invalid UUID */
    public function iDeleteUserWithInvalidUuid(): void
    {
        $this->doRequest('DELETE', '/api/users/not-a-valid-uuid');
    }

    /** @Then the response status code should be :code */
    public function theResponseStatusCodeShouldBe(int $code): void
    {
        Assert::assertSame(
            $code,
            $this->statusCode,
            sprintf("Expected status %d, got %d. Body: %s", $code, $this->statusCode, json_encode($this->responseBody))
        );
    }

    /** @Then the response should be an empty array */
    public function theResponseShouldBeEmptyArray(): void
    {
        Assert::assertIsArray($this->responseBody);
        Assert::assertEmpty($this->responseBody);
    }

    /** @Then the response should be a valid UserResponse */
    public function theResponseShouldBeValidUserResponse(): void
    {
        $this->schemaValidator->validate('UserResponse', $this->responseBody);
    }

    /** @Then the response should be an array of valid UserResponse objects */
    public function theResponseShouldBeArrayOfUserResponses(): void
    {
        Assert::assertIsArray($this->responseBody);
        foreach ($this->responseBody as $item) {
            $this->schemaValidator->validate('UserResponse', $item);
        }
    }

    /** @Then the response should contain the id for user :alias */
    public function theResponseShouldContainIdForAlias(string $alias): void
    {
        Assert::assertIsArray($this->responseBody);
        Assert::assertArrayHasKey('id', $this->responseBody);
        Assert::assertSame($this->userIds[$alias], $this->responseBody['id']);
    }

    /** @Then the response should be a valid error response */
    public function theResponseShouldBeValidErrorResponse(): void
    {
        $this->schemaValidator->validate('ErrorResponse', $this->responseBody);
    }

    /** @Then the response body should be empty */
    public function theResponseBodyShouldBeEmpty(): void
    {
        Assert::assertNull($this->responseBody);
    }

    // ------------------------------------------------------------------ helpers

    private function doRequest(string $method, string $uri, ?array $body = null): void
    {
        $client = $this->getSession()->getDriver()->getClient();
        $content = $body !== null ? (string) json_encode($body) : null;

        $client->request(
            method: $method,
            uri: $uri,
            server: ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            content: $content,
        );

        $response = $client->getResponse();
        $this->statusCode = $response->getStatusCode();
        $raw = $response->getContent();
        $this->responseBody = ($raw !== '' && $raw !== false) ? json_decode($raw, true) : null;
    }

    /** @return array<string, string> */
    private function defaultUserPayload(string $id): array
    {
        return [
            'id' => $id,
            'name' => 'Joan',
            'first_surname' => 'Garcia',
            'second_surname' => 'Pérez',
            'dni' => '12345678Z',
            'email' => 'joan.garcia@example.com',
            'phone_number' => '612345678',
            'bank_account_number' => 'ES9121000418450200051332',
            'date_of_birth' => '1990-06-15',
        ];
    }

    private function generateDni(int $index): string
    {
        $letters = 'TRWAGMYFPDXBNJZSQVHLCKET';
        $number = str_pad((string) (10000000 + $index), 8, '0', STR_PAD_LEFT);
        $letter = $letters[(int) $number % 23];

        return $number . $letter;
    }
}
