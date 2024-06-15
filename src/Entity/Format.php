<?php

namespace App\Entity;

use App\Repository\FormatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FormatRepository::class)]
class Format
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('format.index')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('format.index')]
    private ?string $type = null;

    #[ORM\OneToMany(targetEntity: Resource::class, mappedBy: 'format')]
    private Collection $resource;

    public function __construct()
    {
        $this->resource = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Resource>
     */
    public function getResource(): Collection
    {
        return $this->resource;
    }

    public function addResource(Resource $resource): static
    {
        if (!$this->resource->contains($resource)) {
            $this->resource->add($resource);
            $resource->setFormat($this);
        }

        return $this;
    }

    public function removeResource(Resource $resource): static
    {
        if ($this->resource->removeElement($resource)) {
            if ($resource->getFormat() === $this) {
                $resource->setFormat(null);
            }
        }

        return $this;
    }
}
