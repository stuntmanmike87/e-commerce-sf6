<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Images;
use App\Entity\Products;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

final class ImagesFixtures extends Fixture implements DependentFixtureInterface
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($img = 1; $img <= 100; ++$img) {// for($img = 1; $img <= 100; $img++){
            $image = new Images();
            $image->setName($faker->image(null, 640, 480));
            /** @var Products|null $product */
            $product = $this->getReference('prod-'.random_int(1, 10)); // ('prod-'.rand(1, 10));
            $image->setProducts($product);
            $manager->persist($image);
        }

        $manager->flush();
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [
            ProductsFixtures::class,
        ];
    }
}
