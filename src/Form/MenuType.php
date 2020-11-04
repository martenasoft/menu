<?php

namespace MartenaSoft\Menu\Form;

use MartenaSoft\Menu\Repository\MenuRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class MenuType extends AbstractType
{
    private MenuRepository $menuRepository;

    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('parentId', ChoiceType::class, [
                'choices' => $this->getMenuDropdownArray()
            ])
            ->add('withAllSubItems', CheckboxType::class, [
                'mapped' => false
            ])
            ->add('name')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver): void
    {
        $resolver->setDefaults(
            array(
                'allow_extra_fields' => true
            )
        );
    }
    private function getMenuDropdownArray(): array
    {
        $returnArray[''] = 0;
        foreach ($this->menuRepository->getAllQueryBuilder()->getQuery()->getArrayResult() as $item) {
            $returnArray[str_pad($item['name'], strlen($item['name']) + (int)$item['lvl'], "-", \STR_PAD_LEFT)] =
                $item['parentId'];
        }
        return $returnArray;
    }
}
