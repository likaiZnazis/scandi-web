<?php

namespace App\Controller;

use App\Entity\Price;
use App\Entity\Currency;
use App\Models\Category;
use App\Models\Product;
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
   

    public function mainPage() {
        try {
            
            $currencyType = new ObjectType([
                'name' => 'Currency',
                'fields' => [
                    'currency_id' => Type::int(),
                    'label' => Type::string(),
                    'symbol' => Type::string(),
                ]
                
            ]);

            $priceType = new ObjectType([
                'name' => 'Price',
                'fields' => [
                    'price_id' => Type::int(),
                    'amount' => Type::float(),
                    'currency' => $currencyType,
                        // 'resolve' => function ($price) {
                        //     $priceCurrencys = ($this->entityManager->getRepository(Price::class))-findAll();
                        //     return array_map(function ($currency){
                        //         return [
                        //             'currency_id' => $currency->getCurrencyId(),
                        //             'label' => $currency->getLabel(),
                        //             'symbol' => $currency->getSymbol(),
                        //         ];
                        //     },$priceCurrencys);
                        // }
                    
                ],
            ]);

            //Provide: attribute, 
            $productType = new ObjectType([
                'name' => 'Product',
                'fields' => [
                    'product_id' => Type::int(),
                    'id' => Type::string(),
                    'name' => Type::string(),
                    'in_stock' => Type::boolean(),
                    'gallery' => Type::listOf(Type::string()),
                    'description' => Type::string(),
                    'brand' => Type::string(),
                    'prod_prices' =>[
                        'type' => Type::listOf($priceType),
                        'resolve' => function ($product){
                            $prices = $this->entityManager->getRepository(Price::class)
                                ->findBy(['product' => $product['product_id']]);
                                return array_map(function ($price) {
                                    return [
                                        'price_id' => $price->getPriceId(),
                                        'amount' => $price->getAmount(),
                                        'currency' => [
                                            'currency_id' => $price->getCurrency()->getCurrencyId(),
                                            'label' => $price->getCurrency()->getLabel(),
                                            'symbol' => $price->getCurrency()->getSymbol(),
                                        ],
                                    ];
                                }, $prices);
                            }
                    ] 
                ],
            ]);

            $categoryType = new ObjectType([
                'name' => 'Category',
                'fields' => [
                    'category_id' => Type::int(),
                    'category_name' => Type::string(),
                    'products' => [
                        'type' => Type::listOf($productType),
                        'resolve' => function ($category) {
                            $products = $this->entityManager->getRepository(Product::class)
                                ->findBy(['category' => $category['category_id']]);
                            return array_map(function ($product) {
                                return [
                                    'product_id' => $product->getProductId(),
                                    'id' => $product->getId(),
                                    'name' => $product->getName(),
                                    'in_stock' =>$product->getIn_stock(),
                                    'gallery' => $product->getGallery(),
                                    'description' => $product->getDescription(),
                                    'brand' => $product->getBrand(),
                                    'prices'=> $product->getprod_prices(),
                                ];
                            }, $products);
                        }
                    ]
                ],
            ]);
    
            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'categories' => [
                        'type' => Type::listOf($categoryType),
                        'resolve' => function () {
                            $categories = $this->entityManager->getRepository(Category::class)->findAll();
                            return array_map(function ($category) {
                                return [
                                    'category_id' => $category->getCategoryId(),
                                    'category_name' => $category->getCategory(),
                                ];
                            }, $categories);
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