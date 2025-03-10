<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ProductFixtures extends Fixture
{
    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        // use the factory to create a Faker\Generator instance
        $faker = Factory::create('fr_FR');

        for ($prod = 1; $prod <= 10; ++$prod) {// for($prod = 1; $prod <= 10; $prod++){
            $product = new Product();
            $product->setName($faker->text(15));
            $product->setDescription($faker->text());
            $product->setSlug((string) $this->slugger->slug((string) $product->getName())->lower());
            $product->setPrice($faker->numberBetween(900, 150000));
            $product->setStock($faker->numberBetween(0, 10));

            // On va chercher une référence de catégorie
            // ** @var Categories|null $category */
            $category = $this->getReference('cat-'.random_int(1, 8), Category::class); // ('cat-'. rand(1, 8));
            $product->setCategory($category);

            $this->setReference('prod-'.$prod, $product);
            $manager->persist($product);
        }

        $manager->flush();
    }
}
