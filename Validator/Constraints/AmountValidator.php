<?php

/**
 * @package Money\Bridge\MoneyBridgeBundle
 * @author Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

declare(strict_types=1);

namespace Money\Bridge\Validator\Constraints;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\AbstractComparisonValidator;
use Symfony\Component\Validator\Constraints\GreaterThan;

/**
 * @Annotation
 */
class AmountValidator extends AbstractComparisonValidator
{
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag, PropertyAccessorInterface $propertyAccessor = null)
    {
        parent::__construct($propertyAccessor);
        $this->parameterBag = $parameterBag;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if ($this->parameterBag->get('money_amount_validation_rule') !== 'positive') {
            $constraint->message = 'This value should be either positive or zero.';
        }

        return parent::validate($value, $constraint);
    }

    /**
     * {@inheritdoc}
     */
    protected function compareValues($value1, $value2)
    {
        if ($this->parameterBag->get('money_amount_validation_rule') === 'positive') {
            return null === $value2 || $value1 > $value2;
        }

        return null === $value2 || $value1 >= $value2;
    }

    /**
     * {@inheritdoc}
     */
    protected function getErrorCode()
    {
        return GreaterThan::TOO_LOW_ERROR;
    }
}
