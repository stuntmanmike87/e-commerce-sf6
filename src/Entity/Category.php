<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
/** @final */
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 100)]
    private string $name; // private ?string $name = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $categoryOrder; // private ?int $categoryOrder = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'category')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Category $parent = null; // private Categories $parent;

    /** @var Collection<int, Category> $category */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $category;

    /** @var Collection<int, Product> $product */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Product::class)]
    private Collection $product;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $slug; // private ?string $slug = null;

    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->product = new ArrayCollection();
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

    public function getCategoryOrder(): ?int
    {
        return $this->categoryOrder;
    }

    public function setCategoryOrder(int $categoryOrder): self
    {
        $this->categoryOrder = $categoryOrder;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /** @return Collection<int, Category> */ // ** @return Collection<int, Category>|self[] */
    public function getCategories(): Collection
    {
        return $this->category;
    }

    public function addCategory(self $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
            $category->setParent($this);
        }

        return $this;
    }

    public function removeCategory(self $category): self
    {
        // set the owning side to null (unless already changed)
        if ($this->category->removeElement($category) && $category->getParent() === $this) {
            $category->setParent(null);
        }

        /* if ($this->category->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getParent() === $this) {
                $category->setParent(null);
            }
        } */

        return $this;
    }

    /** @return Collection<int, Product> */ // ** @return Collection<int, Product>|Product[] */
    public function getProduct(): Collection
    {
        return $this->product;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->product->contains($product)) {
            $this->product[] = $product;
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        // set the owning side to null (unless already changed)
        if ($this->product->removeElement($product) && $product->getCategory() === $this) {
            $product->setCategory(null);
        }

        /* if ($this->product->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCategories() === $this) {
                $product->setCategories(null);
            }
        } */

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
