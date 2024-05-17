<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CollectionCustomAttributeUniqueValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var CollectionCustomAttributeUnique $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        function array_has_duplicates($array) {
            return count($array) !== count(array_unique($array));
        }

        $names = [];
        foreach($value as $attribute) {
            $names[] = $attribute->getName();
        }

        if (array_has_duplicates($names)) {
            $this->context->buildViolation($constraint->message)
            ->addViolation();
        }
    }
}
