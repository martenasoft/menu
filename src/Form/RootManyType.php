<?php

namespace MartenaSoft\Menu\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use MartenaSoft\Menu\Entity\Config;
use MartenaSoft\Menu\Repository\ConfigRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RootManyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['isShowConfigDropdown']) {
            $builder->add('config', EntityType::class, [
                'class' => Config::class,
                'choice_label' => 'name'
            ]);
        }
        $builder->add('name');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'isShowConfigDropdown' => false
        ]);
    }
}

