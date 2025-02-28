<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CouponTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouponTypeRepository::class)]
/** @final */
class CouponType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 50)]
    private string $name;

    /** @var Collection<int, Coupon> $coupon */
    #[ORM\OneToMany(mappedBy: 'coupons_types', targetEntity: Coupon::class, orphanRemoval: true)]
    private Collection $coupon;

    public function __construct()
    {
        $this->coupon = new ArrayCollection();
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

    /** @return Collection<int, Coupon> */ // ** @return Collection<int, Coupon>|Coupon[] */
    public function getCoupon(): Collection
    {
        return $this->coupon;
    }

    public function addCoupon(Coupon $coupon): self
    {
        if (!$this->coupon->contains($coupon)) {
            $this->coupon[] = $coupon;
            $coupon->setCouponType($this);
        }

        return $this;
    }

    public function removeCoupon(Coupon $coupon): self
    {
        // set the owning side to null (unless already changed)
        if ($this->coupon->removeElement($coupon) && $coupon->getCouponType() === $this) {
            $coupon->setCouponType(null);
        }

        /* if ($this->coupon->removeElement($coupon)) {
            // set the owning side to null (unless already changed)
            if ($coupon->getCouponType() === $this) {
                $coupon->setCouponType(null);
            }
        } */

        return $this;
    }
}
