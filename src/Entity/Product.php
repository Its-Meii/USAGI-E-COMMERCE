<?php

namespace App\Entity;

use App\Entity\OrderDetail;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;


#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product implements TranslatableInterface
{
    use TranslatableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\OneToMany(mappedBy: 'products', targetEntity: OrderDetail::class)]
    private Collection $orderDetails;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Tags $tags = null;

    #[ORM\Column(length: 255)]
    private ?string $photo_front = null;

    #[ORM\Column(length: 255)]
    private ?string $photo_back = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\ManyToMany(targetEntity: ProductSize::class, mappedBy: 'products')]
    private Collection $productSizes;

    public function __construct()
    {
        $this->orderDetails = new ArrayCollection();
        $this->productSizes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }
    public function getName(): ?string
    {
        return $this->translate()->getName();
    }

    public function getDescription(): ?string
    {
        return $this->translate()->getDescription();
    }


    /**
     * @return Collection<int, OrderDetail>
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetail $orderDetail): static
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails->add($orderDetail);
            $orderDetail->setProduct($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetail $orderDetail): static
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getProduct() === $this) {
                $orderDetail->setProduct(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return 'Nom: '.$this->getName().' prix: '.($this->price/100).'€';
    }

    public function getTags(): ?Tags
    {
        return $this->tags;
    }

    public function setTags(?Tags $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function getPhotoFront(): ?string
    {
        return $this->photo_front;
    }

    public function setPhotoFront(string $photo_front): static
    {
        $this->photo_front = $photo_front;

        return $this;
    }

    public function getPhotoBack(): ?string
    {
        return $this->photo_back;
    }

    public function setPhotoBack(string $photo_back): static
    {
        $this->photo_back = $photo_back;

        return $this;
    }
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, ProductSize>
     */
    public function getProductSizes(): Collection
    {
        return $this->productSizes;
    }

    public function addProductSize(ProductSize $productSize): static
    {
        if (!$this->productSizes->contains($productSize)) {
            $this->productSizes->add($productSize);
            $productSize->addProduct($this);
        }

        return $this;
    }

    public function removeProductSize(ProductSize $productSize): static
    {
        if ($this->productSizes->removeElement($productSize)) {
            $productSize->removeProduct($this);
        }

        return $this;
    }
}
