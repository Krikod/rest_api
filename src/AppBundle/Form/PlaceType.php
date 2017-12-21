<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('address')
        ->add('prices', CollectionType::class, [
            'entry_type' => PriceType::class,
                'allow_add' =>true,
                'error_bubbling' => false,
            // Après ajout de prices, on modifie le controller
            ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Place',
            'csrf_protection' => false
        ));
//        Dans une API, il faut obligatoirement désactiver la protection CSRF (Cross-Site Request Forgery).
//        Nous n’utilisons pas de session et l’utilisateur de l’API peut appeler cette méthode sans se soucier
//        de l’état de l’application : l’API doit rester sans état : stateless.
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_place';
    }


}
