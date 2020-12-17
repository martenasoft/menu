<?php

namespace MartenaSoft\Menu\Form;

use MartenaSoft\Menu\Entity\MenuInterface;
use MartenaSoft\Menu\Repository\MenuRepository;
use MartenaSoft\NestedSets\Entity\NodeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class MenuType extends AbstractType
{
    private MenuRepository $menuRepository;

    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $menu = $this->getMenuDropdownArray($options['menu']);
        if (!empty($menu)) {

            $builder
                ->add('parentId', ChoiceType::class, [
                    'choices' => $menu,
                    'attr' => [
                        'autocomplete' => 'off'
                    ]

                ]);
        }
        $builder
            ->add('name', TextType::class, [
                'required' => false
            ])
            ->add('route', TextType::class, [
                'required' => false
            ])
            ->add('url', TextType::class, [
                'required' => false
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Section' => MenuInterface::TYPE_SECTION,
                    'Page' => MenuInterface::TYPE_PAGE,
                    'External'  => MenuInterface::TYPE_EXTERNAL
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                'menu' => null
            ]
        );
    }

    private function getMenuDropdownArray(NodeInterface $item): ?array
    {
        $returnArray[''] = 0;
        $queryBuilder = $this
            ->menuRepository
            ->getAllQueryBuilder()
        ;

        if (!empty($item->getName())) {
            $queryBuilder
                ->andWhere("m.name<>:name")->setParameter("name", $item->getName());
        }

        $items = $queryBuilder->getQuery()->getArrayResult();

        foreach ($items as $item) {
            $returnArray[str_pad($item['name'], strlen($item['name']) + (int)$item['lvl'], "-", \STR_PAD_LEFT)] =
                $item['id'];
        }

        return $returnArray;
    }
}
