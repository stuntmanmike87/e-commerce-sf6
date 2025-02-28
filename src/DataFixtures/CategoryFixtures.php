<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

final class CategoryFixtures extends Fixture
{
    private int $counter = 1;

    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategory('Informatique', $manager, null);

        $parent->setCategoryOrder(1);
        $this->createCategory('Ordinateurs portables', $manager, $parent);
        $this->createCategory('Ecrans', $manager, $parent);
        $this->createCategory('Souris', $manager, $parent);

        $parent = $this->createCategory('Mode', $manager, null);

        $this->createCategory('Homme', $manager, $parent);
        $this->createCategory('Femme', $manager, $parent);
        $this->createCategory('Enfant', $manager, $parent);

        $manager->flush();
    }

    public function createCategory(string $name, ObjectManager $manager, ?Category $parent = null): Category
    {
        $category = new Category();
        $category->setCategoryOrder(1);
        $category->setName($name);
        $category->setSlug((string) $this->slugger->slug((string) $category->getName())->lower());
        $category->setParent($parent);

        $manager->persist($category);

        $this->addReference('cat-'.$this->counter, $category);
        ++$this->counter; // $this->counter++;

        return $category;
    }
}
