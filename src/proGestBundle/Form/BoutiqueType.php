<?php

namespace proGestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class BoutiqueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('adresse')
            ->add('codepostal')
            ->add('ville')
            ->add('telephone')
            ->add('email')
        ;

        $factory = $builder->getFormFactory();

        $builder->addEventListener(
          FormEvents::PRE_SET_DATA,
          function(FormEvent $event) use ($factory) {
            $boutique = $event->getData();
            if (null === $boutique) {
          return; // On sort de la fonction sans rien faire lorsque $boutique vaut null
        }

        if (!$boutique->getMarge() || null === $boutique->getId()) {
          // Si l'annonce n'est pas publiée, ou si elle n'existe pas encore en base (id est null),
          // alors on ajoute le champ published
          $event->getForm()->add('marge');
        } else {
          // Sinon, on le supprime
          $event->getForm()->remove('marge');
        }
          }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'proGestBundle\Entity\Boutique'
        ));
    }
}
