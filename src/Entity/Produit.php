<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 */
class Produit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="saisir un nom")
     * @Assert\Length(min=2, minMessage = "votre nom doit être composé de {{ limit }} caractères minimum")
     */
    private $nom;

    /**
     * @ORM\Column(name="prix", type="decimal", precision=8, scale=2, nullable=true)
     * @Assert\NotBlank(message = "Saisir un prix ")                                           //***
     * @Assert\Type(type="numeric",message =  "La valeur {{ value }} n'est pas valide, le type est {{ type }} ")
     * @Assert\Regex(
     *     pattern = "/^[0-9]{1,}\,{0,1}[0-9]{0,}$/",
     *     message = "Seulement un entier positif."
     *     )
     */
    private $prix;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\NotBlank(
     *      message= "Donner une date"
     * )
     */
    private $dateLancement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *      message= "selectionner une photo"
     * )
     * Assert\File(
     *     maxSize = "1024k",
     *     mimeTypes={ "image/png" ,"image/jpg","image/jpeg"},
     *     mimeTypesMessage = "Svp inserer une forme valide (png,jpg,jpeg)"
     * )
     */
    private $photo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(
     *      message= "Donner une quantité"
     * )
     */
    private $stock;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $disponible;

    /**
     * @ORM\ManyToOne(targetEntity=TypeProduit::class, inversedBy="produits")
     * @Assert\NotBlank(
     *      message= "selectionner un type"
     * )
     */
    private $typeProduit;

    /**
     * @ORM\OneToMany(targetEntity=Panier::class, mappedBy="produit")
     */
    private $paniers;

    /**
     * @ORM\OneToMany(targetEntity=LigneCommande::class, mappedBy="produit")
     */
    private $ligneCommandes;

    public function __construct()
    {
        $this->paniers = new ArrayCollection();
        $this->ligneCommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }


    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(?string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDateLancement(): ?\DateTimeInterface
    {
        return $this->dateLancement;
    }

    public function setDateLancement(?\DateTimeInterface $dateLancement): self
    {
        $this->dateLancement = $dateLancement;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }



    public function getDisponible(): ?bool
    {
        return $this->disponible;
    }

    public function setDisponible(?bool $disponible): self
    {
        $this->disponible = $disponible;

        return $this;
    }

    public function getTypeProduit(): ?TypeProduit
    {
        return $this->typeProduit;
    }

    public function setTypeProduit(?TypeProduit $typeProduit): self
    {
        $this->typeProduit = $typeProduit;

        return $this;
    }

    public function __toString()
    {
        return $this->getNom();
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Collection|Panier[]
     */
    public function getPaniers(): Collection
    {
        return $this->paniers;
    }

    public function addPanier(Panier $panier): self
    {
        if (!$this->paniers->contains($panier)) {
            $this->paniers[] = $panier;
            $panier->setProduit($this);
        }

        return $this;
    }

    public function removePanier(Panier $panier): self
    {
        if ($this->paniers->removeElement($panier)) {
            // set the owning side to null (unless already changed)
            if ($panier->getProduit() === $this) {
                $panier->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LigneCommande[]
     */
    public function getLigneCommandes(): Collection
    {
        return $this->ligneCommandes;
    }

    public function addLigneCommande(LigneCommande $ligneCommande): self
    {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes[] = $ligneCommande;
            $ligneCommande->setProduit($this);
        }

        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): self
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            // set the owning side to null (unless already changed)
            if ($ligneCommande->getProduit() === $this) {
                $ligneCommande->setProduit(null);
            }
        }

        return $this;
    }
}
