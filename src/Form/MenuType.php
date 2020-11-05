<?php

namespace MartenaSoft\Menu\Form;

use MartenaSoft\Menu\Repository\MenuRepository;
use MartenaSoft\NestedSets\Entity\NodeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
        $menu = $this->getMenuDropdownArray($options['menu'], $options['isRootNode']);
        if (!empty($menu)) {
            $builder
                ->add('parentId', ChoiceType::class, [
                    'choices' => $menu
                ]);
        }
        $builder->add('name');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
              'isRootNode' => false,
               'menu' => null
           ]
        );
    }
    private function getMenuDropdownArray(NodeInterface $item, bool $isRootNode): ?array
    {
        if ($isRootNode) {
            return null;
        }

        $returnArray[''] = 0;
        $queryBuilder = $this
            ->menuRepository
            ->getAllQueryBuilder()
            ->andWhere('m.tree=:tree')
            ->setParameter('tree', $item->getTree());

        if (!empty($item->getName())) {
            $queryBuilder
                ->andWhere("m.name<>:name")
                ->setParameter("name", $item->getName());
        }

        $items = $queryBuilder->getQuery()->getArrayResult();

        foreach ($items as $item) {
            $returnArray[str_pad($item['name'], strlen($item['name']) + (int)$item['lvl'], "-", \STR_PAD_LEFT)] =
                $item['parentId'];
        }
        if (count($returnArray) == 1) {
            return null;
        }
        return $returnArray;
    }
}