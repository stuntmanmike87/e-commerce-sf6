<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
/** @final */
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    /* #[ORM\ManyToOne(targetEntity: Products::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private Products $products; */

    #[ORM\ManyToOne(targetEntity: Products::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Products $products = null;

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

    public function getProducts(): ?Products
    {
        return $this->products;
    }

    public function setProducts(?Products $products): self
    {
        $this->products = $products;

        return $this;
    }
}
