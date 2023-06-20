<?php

namespace Framekit\Extensions;

use Framekit\Contracts\BusinessRule;
use Framekit\Exceptions\InvariantViolation;
use Framekit\Exceptions\MultipleInvariantViolation;
use InvalidArgumentException;

/**
 * @author    Łukasz Sitnicki (mr-luke)
 * @package   mr-luke/framekit
 * @link      http://github.com/mr-luke/framekit
 * @licence   MIT
 */
trait HasBusinessRules
{
    /**
     * @param array<string, BusinessRule|BusinessRule[]> $rulesDefinition
     * @return void
     * @throws MultipleInvariantViolation
     */
    public static function checkMultipleRules(array $rulesDefinition): void
    {
        $errors = [];

        foreach ($rulesDefinition as $key => $rules) {
            $ruleErrors = [];
            $rulesArray = is_array($rules) ? $rules : [$rules];

            foreach ($rulesArray as $rule) {
                if (!($rule instanceof BusinessRule)) {
                    throw new InvalidArgumentException(
                        'First argument should contain associative array with key and single BusinessRule or array of BusinessRule\'s'
                    );
                }

                if ($rule->isBroken()) {
                    $ruleErrors[] = $rule->message();
                }
            }
            if (!empty($ruleErrors)) {
                $errors[$key] = $ruleErrors;
            }
        }

        if (!empty($errors)) {
            throw new MultipleInvariantViolation($errors);
        }
    }

    /**
     * Check if the rule is broken.
     *
     * @param BusinessRule $rule
     * @return void
     * @throws InvariantViolation
     */
    public static function checkRule(BusinessRule $rule): void
    {
        if ($rule->isBroken()) {
            throw new InvariantViolation($rule->message());
        }
    }
}
