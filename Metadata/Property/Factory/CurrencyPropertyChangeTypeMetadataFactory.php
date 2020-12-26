<?php

/**
 * @package Money\Bridge\MoneyBridgeBundle
 * @author Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

declare(strict_types=1);

namespace Money\Bridge\Metadata\Property\Factory;

use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\PropertyMetadata;
use Money\Currency;
use Symfony\Component\PropertyInfo\Type;

if (interface_exists(PropertyMetadataFactoryInterface::class)) {
    final class CurrencyPropertyChangeTypeMetadataFactory implements PropertyMetadataFactoryInterface
    {
        private PropertyMetadataFactoryInterface $decorated;

        public function __construct(PropertyMetadataFactoryInterface $decorated)
        {
            $this->decorated = $decorated;
        }

        public function create(string $resourceClass, string $name, array $options = []): PropertyMetadata
        {
            $propertyMetadata = $this->decorated->create($resourceClass, $name, $options);
            return $this->updatePropertyMetadataType($propertyMetadata, $name);
        }

        private function updatePropertyMetadataType(PropertyMetadata $propertyMetadata, $name): PropertyMetadata
        {
            /** @var \Symfony\Component\PropertyInfo\Type */
            $type = $propertyMetadata->getType();

            if ('currency' === $name && $type !== null && $type->getClassName() === Currency::class) {
                $concreteType = new Type(Type::BUILTIN_TYPE_STRING, $type->isNullable());
                $propertyMetadata = $propertyMetadata->withType($concreteType);
            }

            return $propertyMetadata;
        }
    }
}
