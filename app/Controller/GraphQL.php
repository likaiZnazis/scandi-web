<?php

namespace App\Controller;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;

class GraphQL {
    static public function handle() {
        try {
            // Define the Query type with an 'echo' field that takes a 'message' argument
            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'echo' => [
                        'type' => Type::string(),
                        'args' => [
                            'message' => ['type' => Type::string()],
                        ],
                        // Resolver for the 'echo' field, prepends 'prefix' to the message
                        'resolve' => static fn ($rootValue, array $args): string => $rootValue['prefix'] . $args['message'],
                    ],
                ],
            ]);
        
            // Define the Mutation type with a 'sum' field that takes 'x' and 'y' arguments
            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'sum' => [
                        'type' => Type::int(),
                        'args' => [
                            'x' => ['type' => Type::int()],
                            'y' => ['type' => Type::int()],
                        ],
                        // Resolver for the 'sum' field, returns the sum of 'x' and 'y'
                        'resolve' => static fn ($calc, array $args): int => $args['x'] + $args['y'],
                    ],
                ],
            ]);
        
            // Create the schema with the Query and Mutation types
            $schema = new Schema(
                (new SchemaConfig())
                ->setQuery($queryType)
                ->setMutation($mutationType)
            );
        
            // Read the raw input from the request body
            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }
            
            // Decode the JSON input into an array
            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;
        
            // Root value with a prefix for the 'echo' field
            $rootValue = ['prefix' => 'You said: '];
            // Execute the GraphQL query
            $result = GraphQLBase::executeQuery($schema, $query, $rootValue, null, $variableValues);
            // Convert the result to an array
            $output = $result->toArray();
        } catch (Throwable $e) {
            // Handle any exceptions by returning an error message
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        // Set the response header to JSON
        header('Content-Type: application/json; charset=UTF-8');
        // Encode the output as JSON and return it
        return json_encode($output);
    }
}
