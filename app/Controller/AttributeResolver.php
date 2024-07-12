<?php
namespace App\Controller;

use App\Models\Attribute;
use Doctrine\ORM\EntityManagerInterface;

class AttributeResolver {
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function resolveAttributes($product) {
        $attributes = $this->entityManager->getRepository(Attribute::class)
            ->findBy(['product' => $product['product_id']]);
        return array_map(function ($attribute) {
            return [
                'attribute_id' => $attribute->getAttributeId(),
                'id' => $attribute->getId(),
                'name' => $attribute->getName(),
                'type' => $attribute->getType(),
                'items' => $attribute->getItems(),
            ];
        }, $attributes);
    }
}
