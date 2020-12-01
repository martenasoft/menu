<?php

namespace MartenaSoft\Menu\Form\Type;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use MartenaSoft\Menu\Entity\Menu;
use MartenaSoft\Menu\Repository\MenuRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuDropdownType extends AbstractType
{
    private MenuRepository $menuRepository;

    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'class' => Menu::class,
                'query_builder' => function (ServiceEntityRepositoryInterface $entityRepository) {
                    return $entityRepository->createQueryBuilder('m')
                        ->orderBy('m.lft', 'ASC');
                },
                'choice_label' => function($data) {

                    $value = str_pad(
                        $data->getName(),
                        strlen($data->getName()) + (int)$data->getLvl(),
                        "-", \STR_PAD_LEFT);
                    return $value;
                },
            ]
        );
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
