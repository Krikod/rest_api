<?php
# src/AppBundle/Form/Validator/Constraint/PriceTypeUniqueValidator.php

namespace AppBundle\Form\Validator\Constraint;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PriceTypeUniqueValidator extends ConstraintValidator
{
    public function validate($prices, Constraint $constraint)
    {
        if (!($prices instanceof ArrayCollection)) {
            return;
        }

        $pricesType = [];

        foreach ($prices as $price) {
            if (in_array($price->getType(), $pricesType)) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
                return; // S'il y a un doublon, on arrête la recherche
            } else {
                // Sauvegarde des types de prix déjà présents
                $pricesType[] = $price->getType();
            }
            // Maintenant, modifier les règles de validation qui s’applique à un lieu 
        }
    }
//    Vu que la contrainte s’appelle PriceTypeUnique, le validateur a été nommé PriceTypeUniqueValidator
// afin d’utiliser les conventions de nommage de Symfony => Contrainte validée par ce validateur.
// -> Ce comportement par défaut peut être modifié en étendant la méthode validatedBy de la contrainte (voir doc)
}