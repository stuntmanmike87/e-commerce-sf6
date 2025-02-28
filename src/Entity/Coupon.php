<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CouponRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
/** @final */
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 10, unique: true)]
    private string $code;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\Column(type: Types::INTEGER)]
    private int $discount;

    #[ORM\Column(type: Types::INTEGER)]
    private int $max_usage;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $validity;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $is_valid;

    #[ORM\ManyToOne(targetEntity: CouponType::class, inversedBy: 'coupons')]
    #[ORM\JoinColumn(nullable: true/* false */)]
    private ?CouponType $coupon_type = null;

    /** @var Collection<int, Order> $order */
    #[ORM\OneToMany(mappedBy: 'coupons', targetEntity: Order::class)]
    private Collection $order;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $created_at;

    public function __construct()
    {
        $this->order = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(int $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getMaxUsage(): ?int
    {
        return $this->max_usage;
    }

    public function setMaxUsage(int $max_usage): self
    {
        $this->max_usage = $max_usage;

        return $this;
    }

    public function getValidity(): ?\DateTimeInterface
    {
        return $this->validity;
    }

    public function setValidity(\DateTimeInterface $validity): self
    {
        $this->validity = $validity;

        return $this;
    }

    public function getIsValid(): ?bool
    {
        return $this->is_valid;
    }

    public function setIsValid(bool $is_valid): self
    {
        $this->is_valid = $is_valid;

        return $this;
    }

    public function getCouponType(): ?CouponType
    {
        return $this->coupon_type;
    }

    public function setCouponType(?CouponType $coupon_type): self
    {
        $this->coupon_type = $coupon_type;

        return $this;
    }

    /** @return Collection<int, Order> */ // ** @return Collection<int, Order>|Order[] */
    public function getOrder(): Collection
    {
        return $this->order;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->order->contains($order)) {
            $this->order[] = $order;
            $order->setCoupon($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        // set the owning side to null (unless already changed)
        if ($this->order->removeElement($order) && $order->getCoupon() === $this) {
            $order->setCoupon(null);
        }

        /* if ($this->order->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getCoupons() === $this) {
                $order->setCoupons(null);
            }
        } */

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
