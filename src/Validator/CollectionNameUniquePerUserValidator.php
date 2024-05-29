<?php

namespace App\Validator;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CollectionNameUniquePerUserValidator extends ConstraintValidator
{
    public function __construct(private Security $security) {
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var CollectionNameUnique $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $collections = $this->security->getUser()->getItemCollections()->getValues();
        foreach($collections as $collection) {
            if (mb_strtolower($collection->getName()) === mb_strtolower($value)) {
                $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
            }
        }
    }
}
