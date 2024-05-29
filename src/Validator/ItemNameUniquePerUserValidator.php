<?php

namespace App\Validator;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ItemNameUniquePerUserValidator extends ConstraintValidator
{
    public function __construct(private Security $security) {
    }
    public function validate($value, Constraint $constraint)
    {
        /* @var ItemNameUniquePerUser $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $items = $this->security->getUser()->getItems()->getValues();
        // dd($items);
        foreach($items as $item) {
            if (mb_strtolower($item->getName()) === mb_strtolower($value)) {
                $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
            }
        }
    }
}
