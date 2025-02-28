<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
/** @final */
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank(message: 'Le nom du produit ne peut pas être vide')]
    #[Assert\Length(
        min: 8,
        max: 200,
        minMessage: 'Le titre doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le titre ne doit pas faire plus de {{ limit }} caractères'
    )]
    private string $name;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\Column(type: Types::INTEGER)]
    private int $price;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\PositiveOrZero(message: 'Le stock ne peut pas être négatif')]
    private int $stock;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'product')]
    #[ORM\JoinColumn(nullable: true/* false */)]
    private ?Category $category = null;

    /** @var Collection<int, Image> $image */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Image::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $image;

    /** @var Collection<int, OrderDetail> $orderDetail */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: OrderDetail::class)]
    private Collection $orderDetail;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $created_at;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $slug;

    public function __construct()
    {
        $this->image = new ArrayCollection();
        $this->orderDetail = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /** @return Collection<int, Image> */ // ** @return Collection<int, Image>|Image[] */
    public function getImage(): Collection
    {
        return $this->image;
    }

    public function addImage(Image $image): self
    {
        if (!$this->image->contains($image)) {
            $this->image[] = $image;
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        // set the owning side to null (unless already changed)
        if ($this->image->removeElement($image) && $image->getProduct() === $this) {
            $image->setProduct(null);
        }

        /* if ($this->image->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        } */

        return $this;
    }

    /** @return Collection<int, OrderDetail> */ // ** @return Collection<int, OrderDetail>|OrdersDetail[] */
    public function getOrderDetail(): Collection
    {
        return $this->orderDetail;
    }

    public function addOrderDetail(OrderDetail $orderDetail): self
    {
        if (!$this->orderDetail->contains($orderDetail)) {
            $this->orderDetail[] = $orderDetail;
            $orderDetail->setProduct($this);
        }

        return $this;
    }

    public function removeOrdersDetail(OrderDetail $orderDetail): self
    {
        // set the owning side to null (unless already changed)
        if ($this->orderDetail->removeElement($orderDetail) && $orderDetail->getProduct() === $this) {
            $ordersDetail = null;
            // ->setProducts(null);
        }

        /* if ($this->orderDetail->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getProduct() === $this) {
                $orderDetail == null;//->setProduct(null);
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
