<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est requis.")]
    private ?string $Nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prenom est requis.")]
    private ?string $Prenom = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: "Le raison social est requis.")]
    private ?string $raisonSociale = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Le numéro de téléphone est requis.")]
    #[Assert\Regex(
        pattern: "/^\+?[0-9\s\-]{8,20}$/",
        message: "Le numéro de téléphone est invalide."
    )]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le adresse est requis.")]
    private ?string $adresse = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le ville est requis.")]
    private ?string $ville = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le pays est requis.")]
    private ?string $pays = null;

    /**
     * @var Collection<int, Facture>
     */
    #[ORM\OneToMany(targetEntity: Facture::class, mappedBy: 'client')]
    private Collection $factures;

    public function __construct()
    {
        $this->factures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $Prenom): static
    {
        $this->Prenom = $Prenom;

        return $this;
    }

public function getRaisonSociale(): ?string
{
    return $this->raisonSociale;
}

public function setRaisonSociale(string $raisonSociale): self
{
    $this->raisonSociale = $raisonSociale;
    return $this;
}

public function getTelephone(): ?string
{
    return $this->telephone;
}

public function setTelephone(string $telephone): self
{
    $this->telephone = $telephone;
    return $this;
}

public function getAdresse(): ?string
{
    return $this->adresse;
}

public function setAdresse(string $adresse): self
{
    $this->adresse = $adresse;
    return $this;
}

public function getVille(): ?string
{
    return $this->ville;
}

public function setVille(string $ville): self
{
    $this->ville = $ville;
    return $this;
}

public function getPays(): ?string
{
    return $this->pays;
}

public function setPays(string $pays): self
{
    $this->pays = $pays;
    return $this;
}

/**
 * @return Collection<int, Facture>
 */
public function getFactures(): Collection
{
    return $this->factures;
}

public function addFacture(Facture $facture): static
{
    if (!$this->factures->contains($facture)) {
        $this->factures->add($facture);
        $facture->setClient($this);
    }

    return $this;
}

public function removeFacture(Facture $facture): static
{
    if ($this->factures->removeElement($facture)) {
        // set the owning side to null (unless already changed)
        if ($facture->getClient() === $this) {
            $facture->setClient(null);
        }
    }

    return $this;
}

}
