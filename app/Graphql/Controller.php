<?php

namespace App\Graphql;

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
use GraphQL\Type\Definition\InputObjectType;

class Controller {
    private $entityManager;

    //All the types
    private $itemType;
    private $attributeType;
    private $currencyType;
    private $priceType;
    private $categoryType;
    private $productType;
    private $orderType;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->initializeTypes();
    }

    /**
     * Initialize all the types that will be used to create schemas.
     */
    private function initializeTypes()
    {
        $this->itemType = new ObjectType([
            'name' => 'Item',
            'fields' => [
                'item_id' => Type::int(),
                'displayValue' => Type::string(),
                'value' => Type::string(),
                'id' => Type::string(),
            ]
        ]);

        $this->attributeType = new ObjectType([
            'name' => 'Attribute',
            'fields' => [
                'attribute_id' => Type::int(),
                'id' => Type::string(),
                'name' => Type::string(),
                'type' => Type::string(),
                'items' => [
                    'type' => Type::listOf($this->itemType),
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

        $this->currencyType = new ObjectType([
            'name' => 'Currency',
            'fields' => [
                'currency_id' => Type::int(),
                'label' => Type::string(),
                'symbol' => Type::string(),
            ]
        ]);

        $this->priceType = new ObjectType([
            'name' => 'Price',
            'fields' => [
                'price_id' => Type::int(),
                'amount' => Type::float(),
                'currency' => $this->currencyType,
            ],
        ]);



        $this->productType = new ObjectType([
            'name' => 'Product',
            'fields' => [
                'product_id' => Type::int(),
                'id' => Type::string(),
                'name' => Type::string(),
                'in_stock' => Type::boolean(),
                'gallery' => Type::listOf(Type::string()),
                'description' => Type::string(),
                'brand' => Type::string(),
                'attributes' => Type::listOf($this->attributeType),
                'prod_prices' =>[
                    'type' => Type::listOf($this->priceType),
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

        $this->categoryType = new ObjectType([
            'name' => 'Category',
            'fields' => [
                'category_id' => Type::int(),
                'category_name' => Type::string(),
                'products' => [
                    'type' => Type::listOf($this->productType),
                    'resolve' => function ($category) {
                        $products = $this->entityManager->getRepository(Product::class)
                            ->findBy(['category' => $category['category_id']]);

                        $attributeResolver = new AttributeResolver($this->entityManager);
                        return array_map(function ($product) use ($attributeResolver) {
                            return [
                                'product_id' => $product->getProductId(),
                                'id' => $product->getId(),
                                'name' => $product->getName(),
                                'in_stock' =>$product->getIn_stock(),
                                'gallery' => $product->getGallery(),
                                'description' => $product->getDescription(),
                                'brand' => $product->getBrand(),
                                'attributes' => $attributeResolver->resolveAttributes($product),
                                'prices'=> $product->getprod_prices(),
                            ];
                        }, $products);
                    }
                ]
            ],
        ]);

        $this->orderType = new ObjectType([
            'name' => 'Order',
            'fields' => [
                'order_id' => Type::int(),
                'products' => [
                    'type' => Type::listOf($this->productType),
                    'resolve' => function ($order) {
                        $products = $this->entityManager->getRepository(Product::class)
                            ->findBy(['order' => $order['order_id']]);
                        return $products;
                    },
                ],
                'total_price' => Type::float(),
            ]
        ]);
    }

    /**
     * Function for handling graphql requests
     */
    private function handleGraphQLRequest($queryType)
    {
        try {
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

    /**
     * Resolver for a single product.
     * Returns a product based on it's id.
     * Product detail page
     */
    // public function pdp() {
    //         $queryType = new ObjectType([
    //             'name' => 'Query',
    //             'fields' => [
    //                 'product' => [
    //                     'type' => $this->productType,
    //                     'args' => [
    //                         'product_id' => Type::int(),
    //                     ],
    //                     'resolve' => function ($root, $args) {
    //                         $product_id = $args['product_id'] ?? null;
                            
    //                         // Fetch the product
    //                         $product = $this->entityManager->getRepository(Product::class)
    //                             ->findOneBy(['product_id' => $product_id]);
    //                         if (!$product) {
    //                             throw new \Exception("Product not found for ID: $product_id");
    //                         }
    //                         //Create a attribute resolver
    //                         $attributeResolver = new AttributeResolver($this->entityManager);

    //                         // $productCategory = $product->getCategory();
    //                         return [
    //                             'product_id' => $product->getProductId(),
    //                             'id' => $product->getId(),
    //                             'name' => $product->getName(),
    //                             'in_stock' => $product->getIn_stock(),
    //                             'gallery' => $product->getGallery(),
    //                             'description' => $product->getDescription(),
    //                             'brand' => $product->getBrand(),
    //                             'attributes' => $attributeResolver->resolveAttributes($product),
    //                             //Don't need the category for detail page because it does not show up anywhere from the design.
    //                             // 'category' => [
    //                             //     'category_id' => $productCategory->getCategoryId(),
    //                             //     'category_name' => $productCategory->getCategoryName(),
    //                             // ],
    //                             'prices' => $product->getprod_prices(),
    //                         ];
    //                     },
    //                 ],
    //             ],
    //         ]);
            
    //         $this->handleGraphQLRequest($queryType);
    // }

    /**
     * Resolver for category page. 
     */
    public function mainPage() {
        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'category' => [
                    'type' => $this->categoryType,
                    'args' => [
                        'category_name' => Type::string(),
                    ],
                    'resolve' => function ($root, $args) {
                        $categoryName = $args['category_name'] ?? null;
        
                        // Fetch the category based on the name
                        $category = $this->entityManager->getRepository(Category::class)
                            ->findOneBy(['category_name' => $categoryName]);
                        if (!$category) {
                            throw new \Exception("Category not found for name: $categoryName");
                        }
                        return [
                            'category_id' => $category->getCategoryId(),
                            'category_name' => $category->getCategoryName(),
                        ];
                    },
                ],
            ],
        ]);

        $mutationType = new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'placeOrder' => [
                    'type' => $this->orderType,
                    'args' => [
                        'items' => Type::listOf(new InputObjectType([
                            'name' => 'OrderItemInput',
                            'fields' => [
                                'product_id' => Type::nonNull(Type::int()),
                                'quantity' => Type::nonNull(Type::int()),
                                'selectedAttributes' => Type::listOf(new InputObjectType([
                                    'name' => 'SelectedAttributeInput',
                                    'fields' => [
                                        'attribute_id' => Type::nonNull(Type::int()),
                                        'value' => Type::nonNull(Type::string()),
                                    ],
                                ])),
                            ],
                        ])),
                        'total_price' => Type::nonNull(Type::float()),
                    ],
                    'resolve' => function ($root, $args) {
                        $order = new Order();
                        $order->setTotalPrice($args['total_price']);
        
                        $orderItems = [];
                        foreach ($args['items'] as $item) {
                            $product = $this->entityManager->getRepository(Product::class)
                                ->find($item['product_id']);
                            if (!$product) {
                                throw new \Exception("Product not found for ID: {$item['product_id']}");
                            }
        
                            $orderItem = new OrderItem();
                            $orderItem->setProduct($product);
                            $orderItem->setQuantity($item['quantity']);
                            $orderItem->setSelectedAttributes($item['selectedAttributes']);
        
                            $orderItems[] = $orderItem;
                        }
        
                        $order->setItems($orderItems);
                        $this->entityManager->persist($order);
                        $this->entityManager->flush();
        
                        return $order;
                    },
                ],
            ],
        ]);
        
        $this->handleGraphQLRequest($queryType);
    }
}