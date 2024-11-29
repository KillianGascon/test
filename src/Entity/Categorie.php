<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $Titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Description = null;

    /**
     * @var Collection<int, Chaton>
     */
    #[ORM\OneToMany(targetEntity: Chaton::class, mappedBy: 'Categorie', orphanRemoval: true)]
    private Collection $Chatons;

    public function __construct()
    {
        $this->Chatons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->Titre;
    }

    public function setTitre(string $Titre): static
    {
        $this->Titre = $Titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    /**
     * @return Collection<int, Chaton>
     */
    public function getChatons(): Collection
    {
        return $this->Chatons;
    }

    public function addChaton(Chaton $chaton): static
    {
        if (!$this->Chatons->contains($chaton)) {
            $this->Chatons->add($chaton);
            $chaton->setCategorie($this);
        }

        return $this;
    }

    public function removeChaton(Chaton $chaton): static
    {
        if ($this->Chatons->removeElement($chaton)) {
            // set the owning side to null (unless already changed)
            if ($chaton->getCategorie() === $this) {
                $chaton->setCategorie(null);
            }
        }

        return $this;
    }
}
