<?php
# src/AppBundle/Form/Validator/Constraint/PriceTypeUnique.php

namespace AppBundle\Form\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PriceTypeUnique extends Constraint
{
    public $message = 'A place cannot contain prices with same type';

// The @Annotation annotation is necessary for this new constraint in order to make it available for use in classes
// via annotations. Options for your constraint are represented as public properties on the constraint class.
// => Maintenant, créer le ***validator*** (dans le même dossier) pour gérer cette contrainte.
}

