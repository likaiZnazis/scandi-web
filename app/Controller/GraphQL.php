<?php

namespace App\Controller;

use App\Entity\Price;
use App\Entity\Currency;
use App\Models\Category;
use App\Models\Product;
use App\Entity\Item;
use App\Models\Attribute;
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

            //Need to create items and attributes
            
            $itemType = new ObjectType([
                'name' => 'Item',
                'fields' => [
                    'item_id' => Type::int(),
                    'displayValue' => Type::string(),
                    'value' => Type::string(),
                    'id' => Type::string(),
                ]
            ]);
            
            $attributeType = new ObjectType([
                'name' => 'Attribute',
                'fields' => [
                    'attribute_id' => Type::int(),
                    'id' => Type::string(),
                    'name' => Type::string(),
                    'type' => Type::string(),
                    'items' => [
                        'type' => Type::listOf($itemType),
                        'resolve' => function ($attribute) {
                            $items = $this->entityManager->getRepository(Item::class)
                                ->findBy(['attribute' => $attribute['attribute_id']]);
                            return array_map(function ($item) {
                                return [
                                    'item_id' => $item->getItem_id(),
                                    'displayValue' => $item->getDisplayValue(),
                                    'value' => $item->getValue(),
                                    'id' => $item->getId(),
                                ];
                            }, $items);
                        }
                    ]
                ]
            ]);
            
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
                ],
            ]);

            //Need for attributes and their items to show up
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
                    'attributes' => [
                        'type' => Type::listOf($attributeType),
                        'resolve' => function ($product) {
                            $attributeResolver = new AttributeResolver($this->entityManager);
                            return $attributeResolver->resolveAttributes($product);
                        }
                    ],
                    'prod_prices' =>[
                        'type' => Type::listOf($priceType),
                        'resolve' => function ($product){
                            $prices = $this->entityManager->getRepository(Price::class)
                                ->findBy(['product' => $product['product_id']]);
                                return array_map(function ($price) {
                                    return [
                                        'price_id' => $price->getPriceId(),
                                        'amount' => $price->getAmount(),
                                        $priceCurrency = $price->getCurrency(),
                                        'currency' => [
                                            'currency_id' => $priceCurrency->getCurrencyId(),
                                            'label' => $priceCurrency->getLabel(),
                                            'symbol' => $priceCurrency->getSymbol(),
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
                                    'attributes' => $product->getAttributes(),
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
                    'attributes' => [
                        'type' => Type::listOf($attributeType),
                        'resolve' => function () {
                            $attributes = $this->entityManager->getRepository(Attribute::class)->findAll();
                            return array_map(function ($attribute) {
                                return [
                                    'attribute_id' => $attribute->getAttributeId(),
                                    'name' => $attribute->getName(),
                                    'items' => $attribute->getItems(),
                                ];
                            }, $attributes);
                        }
                    ],
                ],
            ]);
        
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
        
            $schema = new Schema(
                (new SchemaConfig())
                ->setQuery($queryType)
            );

            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }

            if (empty($rawInput)) {
                throw new RuntimeException('Empty query input');
            }

            $input = json_decode($rawInput, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException('Invalid JSON input: ' . json_last_error_msg());
            }

            $query = $input['query'] ?? null;
            if ($query === null) {
                throw new RuntimeException('Query not provided');
            }

            $variableValues = $input['variables'] ?? null;

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
        echo json_encode($output);
    }
}