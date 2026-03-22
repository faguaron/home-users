<?php

declare(strict_types=1);

namespace Tests\Behat\Support;

use JsonSchema\Validator;
use Symfony\Component\Yaml\Yaml;

final class SchemaValidator
{
    /** @var array<string, mixed> */
    private array $spec;

    public function __construct(string $projectDir)
    {
        $this->spec = Yaml::parseFile($projectDir . '/doc/openapi.yaml');
    }

    public function validate(string $schemaName, mixed $data): void
    {
        $schema = $this->resolveSchema($schemaName);
        $validator = new Validator();
        $dataObj = json_decode((string) json_encode($data));
        $validator->validate($dataObj, $schema);

        if (!$validator->isValid()) {
            $errors = array_map(
                static fn (array $e) => sprintf('[%s] %s', $e['property'], $e['message']),
                $validator->getErrors()
            );
            throw new \RuntimeException(
                sprintf("Response does not match schema '%s':\n%s", $schemaName, implode("\n", $errors))
            );
        }
    }

    /** @return array<string, mixed> */
    private function resolveSchema(string $name): array
    {
        $schemas = $this->spec['components']['schemas'] ?? [];

        if (!isset($schemas[$name])) {
            throw new \InvalidArgumentException(sprintf("Schema '%s' not found in openapi.yaml", $name));
        }

        $schema = $schemas[$name];

        if (isset($schema['allOf'])) {
            $merged = ['type' => 'object', 'properties' => [], 'required' => []];
            foreach ($schema['allOf'] as $part) {
                if (isset($part['$ref'])) {
                    $refName = basename((string) $part['$ref']);
                    $part = $schemas[$refName] ?? [];
                }
                $merged['properties'] = array_merge($merged['properties'], $part['properties'] ?? []);
                $merged['required'] = array_merge($merged['required'], $part['required'] ?? []);
            }

            return $merged;
        }

        return $schema;
    }
}
