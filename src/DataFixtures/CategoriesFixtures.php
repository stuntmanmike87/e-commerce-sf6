<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

final class CategoriesFixtures extends Fixture
{
    private int $counter = 1;

    public function __construct(private readonly SluggerInterface $slugger){}

    //public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategory('Informatique', $manager, null);
        
        $this->createCategory('Ordinateurs portables', $manager, $parent);
        $this->createCategory('Ecrans', $manager, $parent);
        $this->createCategory('Souris', $manager, $parent);

        $parent = $this->createCategory('Mode', $manager, null);

        $this->createCategory('Homme', $manager, $parent);
        $this->createCategory('Femme', $manager, $parent);
        $this->createCategory('Enfant', $manager, $parent);
                
        $manager->flush();
    }

    public function createCategory(string $name, ObjectManager $manager, Categories $parent = null): Categories
    {
        $category = new Categories();
        $category->setName($name);
        $category->setSlug((string) $this->slugger->slug((string)$category->getName())->lower());
        $category->setParent($parent);

        $manager->persist($category);

        $this->addReference('cat-'.$this->counter, $category);
        ++$this->counter;//$this->counter++;

        return $category;
    }
}
