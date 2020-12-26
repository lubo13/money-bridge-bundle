<?php

/**
 * @package Money\Bridge\MoneyBridgeBundle
 * @author Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

declare(strict_types=1);

namespace Money\Bridge\Validator\Constraints;

use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NumberConstraintTrait;

/**
 * @Annotation
 */
class Amount extends GreaterThan
{
    use NumberConstraintTrait;

    public $message = 'This value should be positive.';

    public function __construct($options = null)
    {
        parent::__construct($this->configureNumberConstraintOptions($options));
    }
}
