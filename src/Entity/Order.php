<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
/** @final */
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 20, unique: true)]
    private string $reference;

    #[ORM\ManyToOne(targetEntity: Coupon::class, inversedBy: 'order')]
    private ?Coupon $coupon = null; // private Coupons $coupon;

    /* #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'order')]
    #[ORM\JoinColumn(nullable: false)]
    private Users $users; */

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'order')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    /** @var Collection<int, OrderDetail> $orderDetail */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderDetail::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $orderDetail;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $created_at;

    public function __construct()
    {
        $this->orderDetail = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getCoupon(): ?Coupon
    {
        return $this->coupon;
    }

    public function setCoupon(?Coupon $coupon): self
    {
        $this->coupon = $coupon;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /** @return Collection<int, OrderDetail> */ // ** @return Collection<int, OrderDetail>|OrderDetail[] */
    public function getOrderDetail(): Collection
    {
        return $this->orderDetail;
    }

    public function addOrderDetail(OrderDetail $orderDetail): self
    {
        if (!$this->orderDetail->contains($orderDetail)) {
            $this->orderDetail[] = $orderDetail;
            $orderDetail->setOrder($this);
        }

        return $this;
    }

    public function removeOrdersDetail(OrderDetail $orderDetail): self
    {
        // set the owning side to null (unless already changed)
        if ($this->orderDetail->removeElement($orderDetail) && $orderDetail->getOrder() === $this) {
            $orderDetail = null;
            // ->setOrders(null);
        }

        /* if ($this->orderDetail->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getOrder() === $this) {
                $orderDetail == null;//->setOrder(null);
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
