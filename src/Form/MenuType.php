<?php

namespace SymfonySimpleSite\Menu\Form;

use SymfonySimpleSite\Menu\Entity\Menu;
use SymfonySimpleSite\Menu\Entity\MenuInterface;
use SymfonySimpleSite\Menu\Repository\MenuRepository;
use SymfonySimpleSite\NestedSets\Entity\NodeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, [
                    'required' => false
                ]
            )
            ->add('route')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                'data_class' => Menu::class,
            ]
        );
    }

}
