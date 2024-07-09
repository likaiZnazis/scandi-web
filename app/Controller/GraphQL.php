<?php

namespace App\Controller;


use App\Models\Category;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;
use Doctrine\ORM\EntityManagerInterface;

class GraphQL {

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
   

    public function handle() {
        try {
            //Category: category_id, category_name, products 

            $categoryType = new ObjectType([
                'name' => 'Category',
                'fields' => [
                    'category_id' => Type::int(),
                    'category_name' => Type::string(),
                ],
            ]);
            
            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'category' => [
                        'type' => $categoryType,
                        'args' => [
                            'id' => ['type' => Type::int()],
                        ],
                        'resolve' => function ($rootValue, array $args) {
                            $categoryId = $args['id'];
                            $category = $this->entityManager->getRepository(Category::class)->find($categoryId);
                            
                            if (!$category) {
                                throw new \RuntimeException("Did not find category with ID - $categoryId");
                            }
            
                            return [
                                'category_id' => $category->getCategoryId(),
                                'category_name' => $category->getCategory(),
                            ];
                        },
                    ],
                ],
            ]);
            

            // $queryType = new ObjectType([
            //     'name' => 'Query',
            //     'fields' => [
            //         'echo' => [
            //             'type' => Type::string(),
            //             'args' => [
            //                 'message' => ['type' => Type::string()],
            //             ],
            //             'resolve' => static fn ($rootValue, array $args): string => $rootValue['prefix'] . $args['message'],
            //         ],
            //     ],
            // ]);
        
            // $mutationType = new ObjectType([
            //     'name' => 'Mutation',
            //     'fields' => [
            //         'sum' => [
            //             'type' => Type::int(),
            //             'args' => [
            //                 'x' => ['type' => Type::int()],
            //                 'y' => ['type' => Type::int()],
            //             ],
            //             'resolve' => static fn ($calc, array $args): int => $args['x'] + $args['y'],
            //         ],
            //     ],
            // ]);
        
            // See docs on schema options:
            // https://webonyx.github.io/graphql-php/schema-definition/#configuration-options
            $schema = new Schema(
                (new SchemaConfig())
                ->setQuery($queryType)
                // ->setMutation($mutationType)
            );
        
            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }
        
            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;
        
            // $rootValue = ['prefix' => 'You said: '];
            $result = GraphQLBase::executeQuery($schema, $query, null, null, $variableValues);
            $output = $result->toArray();
        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}