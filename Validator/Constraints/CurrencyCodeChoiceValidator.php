<?php

/**
 * @package Money\Bridge\MoneyBridgeBundle
 * @author Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

declare(strict_types=1);

namespace Money\Bridge\Validator\Constraints;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * @Annotation
 */
class CurrencyCodeChoiceValidator extends ChoiceValidator
{
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param mixed $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $alloweCurrencyCodes = $this->parameterBag->get('money_allowed_currency_code');
        $constraint->choices = count($alloweCurrencyCodes) > 0 ? $alloweCurrencyCodes : [$this->parameterBag->get('money_default_currency_code')];

        parent::validate($value, $constraint);

        /** @var \Symfony\Component\Validator\ConstraintViolationList $violations */
        $violations = $this->context->getViolations();

        /** @var \Symfony\Component\Validator\ConstraintViolation $violation */
        foreach ($violations as $offset => $violation) {
            if ($violation->getPropertyPath() === 'price.currency.code') {
                $violations->remove($offset);

                $constraintViolation = new ConstraintViolation($violation->getMessage(), $violation->getMessageTemplate(), $violation->getParameters(), $violation->getRoot(), 'price.currency', $violation->getInvalidValue(),
                    $violation->getPlural(), $violation->getCode(), $violation->getConstraint(), $violation->getCause());
                $violations->add($constraintViolation);
            }
        }
    }
}
