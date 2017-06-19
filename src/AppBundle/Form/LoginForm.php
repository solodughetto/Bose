<?php

namespace AppBundle\Form;

use AppBundle\Entity\SubFamily;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Repository\SubFamilyRepository;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class LoginForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('_username')
            ->add('_password', PasswordType::class);
    }
}