<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CollectionCustomAttributeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var CollectionCustomAttribute $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $countPerType = [];

        foreach($value as $attribute) {
            $countPerType[$attribute->getType()->value] = isset($countPerType[$attribute->getType()->value]) ? ++$countPerType[$attribute->getType()->value] : 1;
        }

        foreach($countPerType as $type => $count) {
            if ($count > $constraint->maxItemsPerType) {
                $this->context->buildViolation($constraint->message)
                ->setParameter('{{ max }}', $constraint->maxItemsPerType)
                ->setParameter('{{ count }}', $count)
                ->setParameter('{{ type }}', $type)
                ->addViolation();
            }
        }

    }
}
