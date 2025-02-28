<?php

namespace App\Factory;

use App\Entity\Coupon;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Coupon>
 */
final class CouponFactory extends PersistentProxyObjectFactory
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
        return Coupon::class;
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
            'code' => self::faker()->text(10),
            'created_at' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'description' => self::faker()->text(),
            'discount' => self::faker()->randomNumber(),
            'is_valid' => self::faker()->boolean(),
            'max_usage' => self::faker()->randomNumber(),
            'validity' => self::faker()->dateTime(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Coupons $coupons): void {})
        ;
    }
}
