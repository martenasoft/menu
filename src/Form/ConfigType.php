<?php

namespace MartenaSoft\Menu\Form;

use MartenaSoft\Menu\Entity\Config;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add(
                'type',
                ChoiceType::class,
                [
                    'choices' => [
                        'All open' => Config::TYPE_OPEN,
                        'All collapsed' => Config::TYPE_COLLAPSED,
                        'Open only active' => Config::TYPE_ACTIVE_OPEN
                    ]
                ]
            )
            ->add(
                'urlPathType',
                ChoiceType::class,
                [
                    'choices' => [
                        'Path (domain.com/section/sub-section/../page.html)' => Config::URL_TYPE_PATH,
                        'Single (domain.com/page.html | domain.com/section)' => Config::URL_TYPE_SINGLE
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Config::class,
            ]
        );
    }
}