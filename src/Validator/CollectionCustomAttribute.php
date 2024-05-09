<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class CollectionCustomAttribute extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'Custom attribute should not contain more than "{{ max }}" of type "{{ type }}". It contains {{ count }}.';
    public $maxItemsPerType = 3;

    public function __construct(?array $groups = null, $payload = null, array $options = [], $maxItemsPerType = 3) {

        parent::__construct($options, $groups, $payload);

        $this->maxItemsPerType = $maxItemsPerType;
    }
}
