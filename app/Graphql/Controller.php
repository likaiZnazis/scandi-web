<?php

namespace App\Graphql;

use App\Entity\Price;
use App\Entity\Currency;
use App\Models\Category;
use App\Models\Product;
use App\Entity\Item;
use App\Entity\Order;
use App\Entity\OrderItem;
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
use GraphQL\Type\Definition\ResolveInfo;

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
    private $orderItemType;
    private $orderItemInputType;
    private $orderInputType;

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

        //Objec types create the mutation type
        $this->orderItemType = new ObjectType([
            'name' => 'OrderItem',
            'fields' => [
                'order_item_id' => Type::nonNull(Type::int()),
                'product_id' => Type::nonNull(Type::int()),
                'quantity' => Type::nonNull(Type::int()),
                'selectedAttributes' => Type::listOf(Type::string()),
            ],
        ]);
    
        $this->orderType = new ObjectType([
            'name' => 'Order',
            'fields' => [
                'order_id' => Type::nonNull(Type::int()),
                'total_price' => Type::nonNull(Type::float()),
                'items' => Type::nonNull(Type::listOf($this->orderItemType)),
            ],
        ]);
    
        //Input types for the object types
        $this->orderItemInputType = new InputObjectType([
            'name' => 'OrderItemInput',
            'fields' => [
                'product_id' => Type::nonNull(Type::int()),
                'quantity' => Type::nonNull(Type::int()),
                'selectedAttributes' => Type::listOf(Type::string()),
            ],
        ]);
    
        $this->orderInputType = new InputObjectType([
            'name' => 'OrderInput',
            'fields' => [
                'total_price' => Type::nonNull(Type::float()),
                'items' => Type::nonNull(Type::listOf($this->orderItemInputType)),
            ],
        ]);
    }

    /**
     * Function for handling graphql requests
     */
    private function handleGraphQLRequest($queryType, $mutationType)
    {
        try {
            $schema = new Schema(
                (new SchemaConfig())
                    ->setQuery($queryType)
                    ->setMutation($mutationType)
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
    
            $result = GraphQLBase::executeQuery($schema, $query, null, ['entityManager' => $this->entityManager], $variableValues);
            $output = $result->toArray();
        } catch (Throwable $e) {
            error_log($e->getMessage());
            error_log($e->getFile() . ':' . $e->getLine());
            error_log($e->getTraceAsString());
    
            $output = [
                'errors' => [
                    [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString(),
                    ],
                ],
            ];
        }
    
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($output);
    }
    
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
                'createOrder' => [
                    'type' => $this->orderType,
                    'args' => [
                        'input' => Type::nonNull($this->orderInputType),
                    ],
                    'resolve' => function($root, $args) {
                        $order = new Order();
                        $order->setTotalPrice($args['input']['total_price']);
    
                        foreach ($args['input']['items'] as $itemData) {
                            $product = $this->entityManager->getRepository(Product::class)->find($itemData['product_id']);
                            
                            if (!$product) {
                                throw new \GraphQL\Error\UserError("Product with ID {$itemData['product_id']} not found.");
                            }
    
                            $orderItem = new OrderItem();
                            $orderItem->setProduct($product);
                            $orderItem->setQuantity($itemData['quantity']);
                            
                            $orderItem->setSelectedAttributes($itemData['selectedAttributes']);
                            $this->entityManager->persist($orderItem);
                            $order->addItem($orderItem);
                        }
    
                        $this->entityManager->persist($order);
                        $this->entityManager->flush();
                        
                        //Returns info
                        return [
                            'order_id' => $order->getOrderId(),
                            'total_price' => $order->getTotalPrice(),
                            'items' => array_map(function($item) {
                                return [
                                    'order_item_id' => $item->getOrderItemId(),
                                    'product_id' => $item->getProduct()->getProductId(),
                                    'quantity' => $item->getQuantity(),
                                    'selectedAttributes' => $item->getSelectedAttributes(),
                                ];
                            }, $order->getItems()->toArray()),
                        ];
                    },
                ],
            ],
        ]);
        $this->handleGraphQLRequest($queryType, $mutationType);
    }
}