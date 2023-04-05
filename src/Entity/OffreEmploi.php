<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * OffreEmploi
 *
 * @ORM\Table(name="offre_emploi")
 * @ORM\Entity
 */
class OffreEmploi
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="id_jobijoba", type="string", length=255, nullable=true)
     */
    private $id_jobijoba;

    /**
     * @var string
     *
     * @ORM\Column(name="intitule", type="string", length=255, nullable=false)
     */
    private $intitule;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_de_publication", type="datetime", nullable=false)
     */
    private $dateDePublication;

    /**
     * @var string|null
     *
     * @ORM\Column(name="latitude", type="decimal", precision=10, scale=4, nullable=true)
     */
    private $latitude;

    /**
     * @var string|null
     *
     * @ORM\Column(name="longitude", type="decimal", precision=10, scale=4, nullable=true)
     */
    private $longitude;

    /**
     * @var string|null
     *
     * @ORM\Column(name="libelle_metier", type="string", length=255, nullable=true)
     */
    private $libelleMetier;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom_entreprise", type="string", length=255, nullable=true)
     */
    private $nomEntreprise;

    /**
     * @var string
     *
     * @ORM\Column(name="type_contrat", type="string", length=255, nullable=false)
     */
    private $typeContrat;

    /**
     * @var string|null
     *
     * @ORM\Column(name="salaire", type="string", length=255, nullable=true)
     */
    private $salaire;

    /**
     * @var string|null
     *
     * @ORM\Column(name="secteur_activite", type="string", length=255, nullable=true)
     */
    private $secteurActivite;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=0, nullable=true)
     */
    private $description;

    

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ville_libelle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $validation;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="offreEmplois")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $visibilite;

    /**
     * @ORM\OneToMany(targetEntity=Candidature::class, mappedBy="id_offre", orphanRemoval=true)
     */
    private $candidatures;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $commercant_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lien_jj;

    /**
     * @ORM\ManyToOne(targetEntity=Commune::class, inversedBy="offreEmplois")
     */
    private $commune;

    public function __construct()
    {
        $this->candidatures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdJobiJoba(): ?string
    {
        return $this->id_jobijoba;
    }

    public function setIdJobiJoba(?string $idJobiJoba): self
    {
        $this->id_jobijoba = $idJobiJoba;

        return $this;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): self
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getDateDePublication(): ?\DateTimeInterface
    {
        return $this->dateDePublication;
    }

    public function setDateDePublication(\DateTimeInterface $dateDePublication): self
    {
        $this->dateDePublication = $dateDePublication;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLibelleMetier(): ?string
    {
        return $this->libelleMetier;
    }

    public function setLibelleMetier(string $libelleMetier): self
    {
        $this->libelleMetier = $libelleMetier;

        return $this;
    }

    public function getNomEntreprise(): ?string
    {
        return $this->nomEntreprise;
    }

    public function setNomEntreprise(?string $nomEntreprise): self
    {
        $this->nomEntreprise = $nomEntreprise;

        return $this;
    }

    public function getTypeContrat(): ?string
    {
        return $this->typeContrat;
    }

    public function setTypeContrat(string $typeContrat): self
    {
        $this->typeContrat = $typeContrat;

        return $this;
    }

    public function getSalaire(): ?string
    {
        return $this->salaire;
    }

    public function setSalaire(?string $salaire): self
    {
        $this->salaire = $salaire;

        return $this;
    }

    public function getSecteurActivite(): ?string
    {
        return $this->secteurActivite;
    }

    public function setSecteurActivite(?string $secteurActivite): self
    {
        $this->secteurActivite = $secteurActivite;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    

    public function getVilleLibelle(): ?string
    {
        return $this->ville_libelle;
    }

    public function setVilleLibelle(string $ville_libelle): self
    {
        $this->ville_libelle = $ville_libelle;

        return $this;
    }

    public function getValidation(): ?string
    {
        return $this->validation;
    }

    public function setValidation(string $validation): self
    {
        $this->validation = $validation;

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

    public function getVisibilite(): ?string
    {
        return $this->visibilite;
    }

    public function setVisibilite(?string $visibilite): self
    {
        $this->visibilite = $visibilite;

        return $this;
    }

    /**
     * @return Collection<int, Candidature>
     */
    public function getCandidatures(): Collection
    {
        return $this->candidatures;
    }

    public function addCandidature(Candidature $candidature): self
    {
        if (!$this->candidatures->contains($candidature)) {
            $this->candidatures[] = $candidature;
            $candidature->setIdOffre($this);
        }

        return $this;
    }

    public function removeCandidature(Candidature $candidature): self
    {
        if ($this->candidatures->removeElement($candidature)) {
            // set the owning side to null (unless already changed)
            if ($candidature->getIdOffre() === $this) {
                $candidature->setIdOffre(null);
            }
        }

        return $this;
    }

    public function getCommercantId(): ?int
    {
        return $this->commercant_id;
    }

    public function setCommercantId(?int $commercant_id): self
    {
        $this->commercant_id = $commercant_id;

        return $this;
    }

    public function getLienJj(): ?string
    {
        return $this->lien_jj;
    }

    public function setLienJj(?string $lien_jj): self
    {
        $this->lien_jj = $lien_jj;

        return $this;
    }

    public function getCommune(): ?Commune
    {
        return $this->commune;
    }

    public function setCommune(?Commune $commune): self
    {
        $this->commune = $commune;

        return $this;
    }
}
