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

require_once __DIR__ . '/../app/Config/bootstrap.php';
//Let's create the products listed

//There can only be one currency USD
$currency = $entityManager->find(Currency::class, 1);

//Prices
$priceIbone = new Price();
$priceIbone->setAmount(120.57);

//Add price to the currency list
$currency->addPrice($priceIbone);

//Attribute
// "id": "Color",
// "id": "Capacity",
// $colorAttribute = new Color();
// $capacityAttribute = new Capacity();

// $other1Attribute = new Other();
// $other1Attribute->setId("With USB 3 ports");
// $other1Attribute->setName("With USB 3 ports");
// $other1Attribute->setType("text");

// $other2Attribute = new Other();
// $other2Attribute->setId("Touch ID in keyboard");
// $other2Attribute->setName("Touch ID in keyboard");
// $other2Attribute->setType("Touch ID in keyboard");
// $sizeAttribute = new Size();



//Add items to attribute 
//"displayValue"
// "value"
// "id"
// $items2 = [["512G", "512G", "512G"],["1T", "1T", "1T"]];
// foreach($items2 as [$displayValue, $value, $id]){
//     $item = new Item();
//     $item->setDisplayValue($displayValue);
//     $item->setValue($value);
//     $item->setId($id);
//     $capacityAttribute->addItem($item);
//     $entityManager->persist($item);
// }

// $items = [["Green","#44FF03","Green"], ["Cyan","#03FFF7","Cyan"], ["Blue", "#030BFF", "Blue"], ["Black", "#000000", "Black"], ["White", "#FFFFFF", "White"]];
// foreach($items as [$displayValue, $value, $id]){
//     $item = new Item();
//     $item->setDisplayValue($displayValue);
//     $item->setValue($value);
//     $item->setId($id);
//     $colorAttribute->addItem($item);
//     $entityManager->persist($item);
// }

// $items = [["Yes","Yes","Yes"], ["No","No","No"]];
// foreach($items as [$displayValue, $value, $id]){
//     $item = new Item();
//     $item->setDisplayValue($displayValue);
//     $item->setValue($value);
//     $item->setId($id);
//     $other2Attribute->addItem($item);
//     $entityManager->persist($item);
// }



//categorys
$clothCategory = $entityManager->find(ClothCategory::class, 1);
$techCategory = $entityManager->find(TechCategory::class, 3);

//Product attributes
$airpods = new TechProduct();
$airpods->setId("apple-airtag");
$airpods->setName("AirTag");
$airpods->setIn_stock(true);
$airpods->setGallery([
    "https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/airtag-double-select-202104?wid=445&hei=370&fmt=jpeg&qlt=95&.v=1617761672000"
]);
$airpods->setDescription("\n<h1>Lose your knack for losing things.</h1>\n<p>AirTag is an easy way to keep track of your stuff. Attach one to your keys, slip another one in your backpack. And just like that, they’re on your radar in the Find My app. AirTag has your back.</p>\n");
//Add the price
$airpods->addPrice($priceIbone);
$airpods->setBrand("Apple");
//Add attributes
// $airpods->addAttribute($other1Attribute);
// $airpods->addAttribute($other2Attribute);
// $airpods->addAttribute($colorAttribute);
// $airpods->addAttribute($capacityAttribute);
//Set category
$techCategory->addProduct($airpods);

// $entityManager->persist($currency);
$entityManager->persist($priceIbone);
// $entityManager->persist($other1Attribute);
// $entityManager->persist($other2Attribute);
// $entityManager->persist($colorAttribute);
// $entityManager->persist($capacityAttribute);
// $entityManager->persist($techCategory);
$entityManager->persist($airpods);

$entityManager->flush();