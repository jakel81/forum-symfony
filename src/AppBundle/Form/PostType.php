<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre', 'attr' => ['placeholder' => 'Titre du post']])
            ->add('text', TextareaType::class, ['label' => 'Texte', 'attr' => ['rows' => '10', 'placeholder' => 'Tapez votre texte'], 'required' => true])
            ->add('createdAt', DateType::class, ['label' => 'Date de publication', 'widget' => 'single_text', 'attr' => ['readonly' => 'readonly']])
            ->add('image',FileType::class, ['label' => 'photo', 'required' => false, 'data_class' => 'Symfony\Component\HttpFoundation\File\File', 'property_path' => 'image'])
            //->add('author', EntityType::class, ['class'=>'AppBundle\Entity\Author', 'placeholder' => 'Choisissez un auteur', 'choice_label' => 'fullName'])
            //->add('theme')
            ->add('submit', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-primary']]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Post'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_post';
    }
}