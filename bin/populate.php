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
use App\Models\Product;

require_once __DIR__ . '/../app/Config/bootstrap.php';

$product_ids = ["huarache-x-stussy-le", "jacket-canada-goosee", "PlayStation 5", "xbox-series-s", "apple-imac-2021", "apple-iphone-12-pro", "apple-airpods-pro", "apple-airtag"];

$product_name = ["Nike Air Huarache Le", "Jacket", "PlayStation 5", "Xbox Series S 512GB", "iMac 2021", "iPhone 12 Pro", "AirPods Pro", "AirTag"];

$product_in_stock = [true, true, false,false,true,true,false,true];

$product_gallery = [[
    "https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_2_720x.jpg?v=1612816087",
    "https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_1_720x.jpg?v=1612816087",
    "https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_3_720x.jpg?v=1612816087",
    "https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_5_720x.jpg?v=1612816087",
    "https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_4_720x.jpg?v=1612816087"
], [
    "https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016105/product-image/2409L_61.jpg",
    "https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016107/product-image/2409L_61_a.jpg",
    "https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016108/product-image/2409L_61_b.jpg",
    "https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016109/product-image/2409L_61_c.jpg",
    "https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016110/product-image/2409L_61_d.jpg",
    "https://images.canadagoose.com/image/upload/w_1333,c_scale,f_auto,q_auto:best/v1634058169/product-image/2409L_61_o.png",
    "https://images.canadagoose.com/image/upload/w_1333,c_scale,f_auto,q_auto:best/v1634058159/product-image/2409L_61_p.png"
],[
    "https://images-na.ssl-images-amazon.com/images/I/510VSJ9mWDL._SL1262_.jpg",
    "https://images-na.ssl-images-amazon.com/images/I/610%2B69ZsKCL._SL1500_.jpg",
    "https://images-na.ssl-images-amazon.com/images/I/51iPoFwQT3L._SL1230_.jpg",
    "https://images-na.ssl-images-amazon.com/images/I/61qbqFcvoNL._SL1500_.jpg",
    "https://images-na.ssl-images-amazon.com/images/I/51HCjA3rqYL._SL1230_.jpg"
],[
    "https://images-na.ssl-images-amazon.com/images/I/71vPCX0bS-L._SL1500_.jpg",
    "https://images-na.ssl-images-amazon.com/images/I/71q7JTbRTpL._SL1500_.jpg",
    "https://images-na.ssl-images-amazon.com/images/I/71iQ4HGHtsL._SL1500_.jpg",
    "https://images-na.ssl-images-amazon.com/images/I/61IYrCrBzxL._SL1500_.jpg",
    "https://images-na.ssl-images-amazon.com/images/I/61RnXmpAmIL._SL1500_.jpg"
],[
    "https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/imac-24-blue-selection-hero-202104?wid=904&hei=840&fmt=jpeg&qlt=80&.v=1617492405000"
],[
    "https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-pro-family-hero?wid=940&amp;hei=1112&amp;fmt=jpeg&amp;qlt=80&amp;.v=1604021663000"
],[
    "https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/MWP22?wid=572&hei=572&fmt=jpeg&qlt=95&.v=1591634795000"
],[
    "https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/airtag-double-select-202104?wid=445&hei=370&fmt=jpeg&qlt=95&.v=1617761672000"
],];

$product_descr = ["<p>Great sneakers for everyday use!</p>", "<p>Awesome winter jacket</p>", "<p>A good gaming console. Plays games of PS4! Enjoy if you can buy it mwahahahaha</p>", "\n<div>\n    <ul>\n        <li><span>Hardware-beschleunigtes Raytracing macht dein Spiel noch realistischer</span></li>\n        <li><span>Spiele Games mit bis zu 120 Bilder pro Sekunde</span></li>\n        <li><span>Minimiere Ladezeiten mit einer speziell entwickelten 512GB NVMe SSD und wechsle mit Quick Resume nahtlos zwischen mehreren Spielen.</span></li>\n        <li><span>Xbox Smart Delivery stellt sicher, dass du die beste Version deines Spiels spielst, egal, auf welcher Konsole du spielst</span></li>\n        <li><span>Spiele deine Xbox One-Spiele auf deiner Xbox Series S weiter. Deine Fortschritte, Erfolge und Freundesliste werden automatisch auf das neue System übertragen.</span></li>\n        <li><span>Erwecke deine Spiele und Filme mit innovativem 3D Raumklang zum Leben</span></li>\n        <li><span>Der brandneue Xbox Wireless Controller zeichnet sich durch höchste Präzision, eine neue Share-Taste und verbesserte Ergonomie aus</span></li>\n        <li><span>Ultra-niedrige Latenz verbessert die Reaktionszeit von Controller zum Fernseher</span></li>\n        <li><span>Verwende dein Xbox One-Gaming-Zubehör -einschließlich Controller, Headsets und mehr</span></li>\n        <li><span>Erweitere deinen Speicher mit der Seagate 1 TB-Erweiterungskarte für Xbox Series X (separat erhältlich) und streame 4K-Videos von Disney+, Netflix, Amazon, Microsoft Movies &amp; TV und mehr</span></li>\n    </ul>\n</div>","The new iMac!","This is iPhone 12. Nothing else to say.","\n<h3>Magic like you’ve never heard</h3>\n<p>AirPods Pro have been designed to deliver Active Noise Cancellation for immersive sound, Transparency mode so you can hear your surroundings, and a customizable fit for all-day comfort. Just like AirPods, AirPods Pro connect magically to your iPhone or Apple Watch. And they’re ready to use right out of the case.\n\n<h3>Active Noise Cancellation</h3>\n<p>Incredibly light noise-cancelling headphones, AirPods Pro block out your environment so you can focus on what you’re listening to. AirPods Pro use two microphones, an outward-facing microphone and an inward-facing microphone, to create superior noise cancellation. By continuously adapting to the geometry of your ear and the fit of the ear tips, Active Noise Cancellation silences the world to keep you fully tuned in to your music, podcasts, and calls.\n\n<h3>Transparency mode</h3>\n<p>Switch to Transparency mode and AirPods Pro let the outside sound in, allowing you to hear and connect to your surroundings. Outward- and inward-facing microphones enable AirPods Pro to undo the sound-isolating effect of the silicone tips so things sound and feel natural, like when you’re talking to people around you.</p>\n\n<h3>All-new design</h3>\n<p>AirPods Pro offer a more customizable fit with three sizes of flexible silicone tips to choose from. With an internal taper, they conform to the shape of your ear, securing your AirPods Pro in place and creating an exceptional seal for superior noise cancellation.</p>\n\n<h3>Amazing audio quality</h3>\n<p>A custom-built high-excursion, low-distortion driver delivers powerful bass. A superefficient high dynamic range amplifier produces pure, incredibly clear sound while also extending battery life. And Adaptive EQ automatically tunes music to suit the shape of your ear for a rich, consistent listening experience.</p>\n\n<h3>Even more magical</h3>\n<p>The Apple-designed H1 chip delivers incredibly low audio latency. A force sensor on the stem makes it easy to control music and calls and switch between Active Noise Cancellation and Transparency mode. Announce Messages with Siri gives you the option to have Siri read your messages through your AirPods. And with Audio Sharing, you and a friend can share the same audio stream on two sets of AirPods — so you can play a game, watch a movie, or listen to a song together.</p>\n","\n<h1>Lose your knack for losing things.</h1>\n<p>AirTag is an easy way to keep track of your stuff. Attach one to your keys, slip another one in your backpack. And just like that, they’re on your radar in the Find My app. AirTag has your back.</p>\n",];

$product_category = ['c','c','t','t','t','t','t','t'];

$product_attributes = [["size"], ["size"], ["color","capacity"],["color","capacity"],["capacity", "With USB 3 ports", "Touch ID in keyboard"],["capacity", "color"],[],[]];

$product_items = [[[[40,40,40], [41,41,41], [42,42,42], [43,43,43]]],[[['Small', 'S', 'Small'],['Medium', 'M', 'Medium'], ['Large', 'L', 'Large'],['Extra Large', 'XL', 'Extra Large']] ], [[['Green', "#44FF03", 'Green'], ['Cyan', '#03FFF7', 'Cyan'], ['Blue', '#030BFF', 'Blue'], ['Black', '#000000', 'Black'], ['White', '#FFFFFF', 'White']],[['512G','512G','512G'], ['1T','1T','1T']]],[[['Green', "#44FF03", 'Green'], ['Cyan', '#03FFF7', 'Cyan'], ['Blue', '#030BFF', 'Blue'], ['Black', '#000000', 'Black'], ['White', '#FFFFFF', 'White']],[['512G','512G','512G'], ['1T','1T','1T']]],[[['256GB', '256GB', '256GB'],['512GB', '512GB', '512GB']], [['Yes', 'Yes','Yes'], ['No', 'No','No']],[['Yes','Yes','Yes'],['No','No','No']]],[[['512G','512G','512G'], ['1T','1T','1T']], [['Green', "#44FF03", 'Green'], ['Cyan', '#03FFF7', 'Cyan'], ['Blue', '#030BFF', 'Blue'], ['Black', '#000000', 'Black'], ['White', '#FFFFFF', 'White']]],[[[]]], [[[]]]];

$product_prices = [144.69,518.47, 844.02, 333.99, 1688.03, 1000.76, 300.23, 120.57];

$clothCategory = $entityManager->getRepository(ClothCategory::class)->findOneBy([]);
$techCategory = $entityManager->getRepository(TechCategory::class)->findOneBy([]);


$product_brand = ["Nike x Stussy", "Canada Goose", "Sony", "Microsoft", "Apple", "Apple", "Apple", "Apple"];

// Create a currency
$USDcurrency = new Currency();
$USDcurrency->setLabel('USD');
$USDcurrency->setSymbol('$');
$entityManager->persist($USDcurrency);
if (!$clothCategory || !$techCategory) {
    throw new Exception("Categories not found in the database.");
}
// Process each product
foreach ($product_ids as $index => $prod_id) {
    // Determine category
    $category = $product_category[$index] === 'c' ? $clothCategory : $techCategory;

    // Create the appropriate product type
    $product = $product_category[$index] === 'c' ? new ClothProduct() : new TechProduct();
    $product->setCategory($category);

    // Basic product setup
    $product->setId($product_ids[$index]);
    $product->setName($product_name[$index]);
    $product->setIn_stock($product_in_stock[$index]);
    $product->setGallery($product_gallery[$index]);
    $product->setDescription($product_descr[$index]);
    $price = new Price();
    $price->setAmount($product_prices[$index]);
    $USDcurrency->addPrice($price);
    $product->addPrice($price);
    $entityManager->persist($price);

    $product->setBrand($product_brand[$index]);

    //Attributes
    if (!empty($product_attributes[$index])) {
        foreach ($product_attributes[$index] as $attr_index => $attribute_type) {
            if ($attribute_type == "size") {
                $sizeAttr = new Size();
                foreach ($product_items[$index][$attr_index] as $size) {
                    $item = new Item();
                    $item->setDisplayValue($size[0]);
                    $item->setValue($size[1]);
                    $item->setId($size[2]);
                    $sizeAttr->addItem($item);
                    $entityManager->persist($item);
                }
                $product->addAttribute($sizeAttr);
                $entityManager->persist($sizeAttr);
            } elseif ($attribute_type == "color") {
                $colorAttr = new Color();
                foreach ($product_items[$index][$attr_index] as $color) {
                    
                    $item = new Item();
                    $item->setDisplayValue($color[0]);
                    $item->setValue($color[1]);
                    $item->setId($color[2]);
                    $colorAttr->addItem($item);
                    $entityManager->persist($item);
                }
                $product->addAttribute($colorAttr);
                $entityManager->persist($colorAttr);
            } elseif ($attribute_type == "capacity") {
                $capacityAttr = new Capacity();
                foreach ($product_items[$index][$attr_index] as $capacity) {
                    $item = new Item();
                    $item->setDisplayValue($capacity[0]);
                    $item->setValue($capacity[1]);
                    $item->setId($capacity[2]);
                    $capacityAttr->addItem($item);
                    $entityManager->persist($item);
                }
                $product->addAttribute($capacityAttr); 
                $entityManager->persist($capacityAttr);
            } else { 
                $otherAttr = new Other();
                $otherAttr->setId($attribute_type); 
                $otherAttr->setName($attribute_type);
                $otherAttr->setType('text');
                foreach ($product_items[$index][$attr_index] as $other) {
                    $item = new Item();
                    $item->setDisplayValue($other[0]); 
                    $item->setValue($other[1]); 
                    $item->setId($other[2]); 
                    $otherAttr->addItem($item); 
                    $entityManager->persist($item);
                }
                $product->addAttribute($otherAttr); 
                $entityManager->persist($otherAttr);
            }
        }
    }

    $entityManager->persist($product);
}

$entityManager->flush();