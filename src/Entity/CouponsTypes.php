<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CouponsTypesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouponsTypesRepository::class)]
/** @final */
class CouponsTypes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 50)]
    private string $name;

    /** @var Collection<int, Coupons> $coupons */
    #[ORM\OneToMany(mappedBy: 'coupons_types', targetEntity: Coupons::class, orphanRemoval: true)]
    private Collection $coupons;

    public function __construct()
    {
        $this->coupons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Coupons>|Coupons[]
     */
    public function getCoupons(): Collection
    {
        return $this->coupons;
    }

    public function addCoupon(Coupons $coupon): self
    {
        if (!$this->coupons->contains($coupon)) {
            $this->coupons[] = $coupon;
            $coupon->setCouponsTypes($this);
        }

        return $this;
    }

    public function removeCoupon(Coupons $coupon): self
    {
        // set the owning side to null (unless already changed)
        if ($this->coupons->removeElement($coupon) && $coupon->getCouponsTypes() === $this) {
            $coupon->setCouponsTypes(null);
        }

        /* if ($this->coupons->removeElement($coupon)) {
            // set the owning side to null (unless already changed)
            if ($coupon->getCouponsTypes() === $this) {
                $coupon->setCouponsTypes(null);
            }
        } */

        return $this;
    }
}
