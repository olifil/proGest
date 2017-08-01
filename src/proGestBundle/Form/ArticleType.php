<?php

namespace proGestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class ArticleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('descriptif')
            ->add('prixAchat')
        ;

        $factory = $builder->getFormFactory();

        $builder->addEventListener(
          FormEvents::PRE_SET_DATA,
          function(FormEvent $event) use ($factory) {
            $article = $event->getData();
            if (null === $article) {
          return; // On sort de la fonction sans rien faire lorsque $boutique vaut null
        }

        if ( null === $article->getId()) {
          // Si l'annonce n'est pas publiée, ou si elle n'existe pas encore en base (id est null),
          // alors on ajoute le champ published
          $event->getForm()->add('livre');
        } else {
          // Sinon, on le supprime
          $event->getForm()->remove('livre');
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
            'data_class' => 'proGestBundle\Entity\Article'
        ));
    }
}
