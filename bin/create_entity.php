<?php

use App\Entity\Currency;
use App\Entity\Price;
use App\Entity\Color;
use App\Entity\Capacity;
use App\Entity\Other;
use App\Entity\ClothProduct;
use App\Entity\ClothCategory;
use App\Entity\TechProduct;
use App\Entity\TechCategory;
use App\Entity\Size;
use App\Entity\Item;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Models\Product;


require_once __DIR__ . '/../app/Config/bootstrap.php';

// Create a new Order
$order = new Order();
$order->setTotalPrice(123.45); // Set the total price of the order

// Define the product IDs and quantities for the order items
$productIdsAndQuantities = [
    ['product_id' => 1, 'quantity' => 2, 'attributes' => [['size' => 'L'], ['color' => 'red']]],
    ['product_id' => 2, 'quantity' => 1, 'attributes' => [['size' => 'M'], ['color' => 'blue']]]
];

// Create OrderItems and add them to the Order
foreach ($productIdsAndQuantities as $data) {
    // Find the product by ID
    $product = $entityManager->getRepository(Product::class)->find($data['product_id']);
    
    if (!$product) {
        throw new \Exception("Product with ID {$data['product_id']} not found.");
    }
    
    // Create a new OrderItem
    $orderItem = new OrderItem();
    $orderItem->setProduct($product); // Set the product for the order item
    $orderItem->setQuantity($data['quantity']); // Set the quantity
    $orderItem->setSelectedAttributes($data['attributes']); // Set the selected attributes
    
    // Add the OrderItem to the Order
    $order->addItem($orderItem);
}

// Persist the Order and all associated OrderItems
$entityManager->persist($order);
$entityManager->flush(); // Save changes to the database

echo "Order created with ID " . $order->getOrderId();

