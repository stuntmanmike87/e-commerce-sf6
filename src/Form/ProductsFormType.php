<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Products;
use App\Repository\CategoriesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Positive;

final class ProductsFormType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var array<Constraint>|array<string, mixed>|null $constraints */
        $constraints = new Image([
            'maxWidth' => 1280,
            'maxWidthMessage' => "L'image doit faire {{ max_width }} pixels de large au maximum",
        ]);
        $builder
            ->add('name', options: [
                'label' => 'Nom',
            ])
            ->add('description')
            ->add('price', MoneyType::class, options: [
                'label' => 'Prix',
                'divisor' => 100,
                'constraints' => [
                    new Positive(
                        message: 'Le prix ne peut être négatif'
                    ),
                ],
            ])
            ->add('stock', options: [
                'label' => 'Unités en stock',
            ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'group_by' => 'parent.name',
                'query_builder' => static fn (CategoriesRepository $cr) => $cr->createQueryBuilder('c')
                    ->where('c.parent IS NOT NULL')
                    ->orderBy('c.name', 'ASC'),
                /* 'query_builder' => function(CategoriesRepository $cr){
                    return $cr->createQueryBuilder('c')
                        ->where('c.parent IS NOT NULL')
                        ->orderBy('c.name', 'ASC');
                } */
            ])
            ->add('images', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'constraints' => [new All($constraints)],
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
