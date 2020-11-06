<?php

namespace MartenaSoft\Menu\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use MartenaSoft\Menu\Repository\ConfigRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class RootManyType extends AbstractType
{
    private ConfigRepository $configRepository;

    public function __construct(ConfigRepository $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('config', CollectionType::class, [
                'entry_type' => ConfigType::class
            ])
            ->add('name');
    }

    private function getChoices(): ?array
    {
        $items = $this->configRepository->getAllQueryBuilder()->getQuery()->getArrayResult();
        if (empty($items)) {
            return null;
        }
        $result = new ArrayCollection($items);
        dump($result);

        $result = [];
        foreach ($items as $item) {
            $result[$item['name']] = $item['id'];
        }
        return $result;
    }
}
