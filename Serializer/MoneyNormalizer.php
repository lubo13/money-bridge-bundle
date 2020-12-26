<?php

/**
 * @package Money\Bridge\MoneyBridgeBundle
 * @author Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

declare(strict_types=1);

namespace Money\Bridge\Serializer;

use Money\Bridge\Util\AmountFormatter;
use Money\Money;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class MoneyNormalizer extends ObjectNormalizer
{
    private ParameterBagInterface $parameterBag;

    public function __construct(
        ParameterBagInterface $parameterBag,
        ClassMetadataFactoryInterface $classMetadataFactory = null,
        NameConverterInterface $nameConverter = null,
        PropertyAccessorInterface $propertyAccessor = null,
        PropertyTypeExtractorInterface $propertyTypeExtractor = null,
        ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null,
        callable $objectClassResolver = null,
        array $defaultContext = []
    ) {
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor, $classDiscriminatorResolver, $objectClassResolver, $defaultContext);
        $this->parameterBag = $parameterBag;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Money;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return $type === Money::class;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $data = parent::normalize($object, $format, $context);
        if (! isset($data['amount'])) {
            return $data;
        }
        $amount = $data['amount'];
        $fractionalParameter = $this->parameterBag->get('money_fractional_part');

        if (strlen($amount) < $fractionalParameter + 1) {
            $amount = str_pad($amount, $fractionalParameter + 1, '0', STR_PAD_LEFT);
        }

        $int = substr($amount, 0, -$fractionalParameter);
        $fractional = substr($amount, -$fractionalParameter);
        $amount = $int . '.' . $fractional;

        $data['amount'] = $amount;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if ((isset($context['groups']) && ! in_array('money:input', $context['groups'])) || ! isset($data['amount'])) {
            return null;
        }

        if (! is_string($data['amount'])) {
            throw new InvalidArgumentException('The amount data should be string.');
        }

        $integerParameter = $this->parameterBag->get('money_integer_part');
        $fractionalParameter = $this->parameterBag->get('money_fractional_part');

        $explode = explode('.', ltrim($data['amount'], '-'));

        if ((isset($explode[0]) && strlen($explode[0]) > $integerParameter)
            || (isset($explode[1]) && strlen($explode[1]) > $fractionalParameter)
            || preg_match("/^(\d{1,$integerParameter}|\d{0,$integerParameter}\.\d{0,$fractionalParameter})$/", $data['amount']) == false) {
            throw new InvalidArgumentException(sprintf('The amount can have a max %s digit and %s of them should be fractional digit.', $integerParameter, $fractionalParameter));
        }

        $amount = AmountFormatter::getAmountWithoutFractional($data['amount'], $fractionalParameter);

        $currencyCode = $this->parameterBag->get('money_default_currency_code');

        if (isset($context['groups']) && in_array('currency:input', $context['groups'])) {
            $currencyCode = isset($data['currency']) && is_string($data['currency']) ? $data['currency'] : $currencyCode;
        } else {
            $context['groups'][] = 'currency:input';
        }

        $data['amount'] = $amount;
        $data['currency'] = ['code' => $currencyCode];

        return parent::denormalize($data, $class, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === static::class;
    }
}
