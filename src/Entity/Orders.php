<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OrdersRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdersRepository::class)]
/** @final */
class Orders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 20, unique: true)]
    private string $reference;

    #[ORM\ManyToOne(targetEntity: Coupons::class, inversedBy: 'orders')]
    private ?Coupons $coupons = null;//private Coupons $coupons;

    /* #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private Users $users; */

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Users $users = null;

    /** @var Collection<OrdersDetails> $ordersDetails */
    #[ORM\OneToMany(mappedBy: 'orders', targetEntity: OrdersDetails::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $ordersDetails;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $created_at;

    public function __construct()
    {
        $this->ordersDetails = new ArrayCollection();
        $this->created_at = new DateTimeImmutable();
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

    public function getCoupons(): ?Coupons
    {
        return $this->coupons;
    }

    public function setCoupons(?Coupons $coupons): self
    {
        $this->coupons = $coupons;

        return $this;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): self
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return Collection|OrdersDetails[]
     */
    public function getOrdersDetails(): Collection
    {
        return $this->ordersDetails;
    }

    public function addOrdersDetail(OrdersDetails $ordersDetail): self
    {
        if (!$this->ordersDetails->contains($ordersDetail)) {
            $this->ordersDetails[] = $ordersDetail;
            $ordersDetail->setOrders($this);
        }

        return $this;
    }

    public function removeOrdersDetail(OrdersDetails $ordersDetail): self
    {
        // set the owning side to null (unless already changed)
        if ($this->ordersDetails->removeElement($ordersDetail) && $ordersDetail->getOrders() === $this) {
                $ordersDetail == null;
                //->setOrders(null);
        }

        /* if ($this->ordersDetails->removeElement($ordersDetail)) {
            // set the owning side to null (unless already changed)
            if ($ordersDetail->getOrders() === $this) {
                $ordersDetail == null;//->setOrders(null);
            }
        } */

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
