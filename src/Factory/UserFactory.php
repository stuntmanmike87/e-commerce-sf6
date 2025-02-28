<?php

namespace App\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     *
     * @return array<string, mixed>
     */
    protected function defaults(): array // |callable
    {
        return [
            'address' => self::faker()->text(255),
            'city' => self::faker()->text(150),
            'created_at' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'email' => self::faker()->text(180),
            'firstname' => self::faker()->text(100),
            'is_verified' => self::faker()->boolean(),
            'lastname' => self::faker()->text(100),
            'password' => self::faker()->text(),
            'roles' => [],
            'zipcode' => self::faker()->text(5),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Users $users): void {})
        ;
    }
}
