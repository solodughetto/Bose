<?php

namespace AppBundle\Form;

use AppBundle\Entity\SubFamily;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Repository\SubFamilyRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class GenusFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('name')
//            ->add('subFamily', null, [
//                'placeholder' => 'choose a Sub Family'
//            ])
            ->add('subFamily', EntityType::class, [
                'class' => SubFamily::class,
                'query_builder' => function(SubFamilyRepository $repo) {
                    return $repo->createAlphabeticalQueryBuilder();
                }
            ])
            ->add('speciesCount')
            ->add('funFact')
            ->add('isPublished', ChoiceType::class, [
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ]
            ])
            ->add('firstDiscoveredAt', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'html5' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Genus'
        ]);
    }
}