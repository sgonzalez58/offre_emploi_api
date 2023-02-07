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
     * @ORM\Column(name="id_pole_emploi", type="string", length=255, nullable=true)
     */
    private $id_pole_emploi;

    /**
     * @var string
     *
     * @ORM\Column(name="intitule", type="string", length=255, nullable=false)
     */
    private $intitule;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_de_creation", type="datetime", nullable=false)
     */
    private $dateDeCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_actualisation", type="datetime", nullable=false)
     */
    private $dateActualisation;

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
     * @ORM\Column(name="code_metier", type="string", length=255, nullable=true)
     */
    private $codeMetier;

    /**
     * @var string|null
     *
     * @ORM\Column(name="libelle_metier", type="string", length=255, nullable=true)
     */
    private $libelleMetier;

    /**
     * @var string
     *
     * @ORM\Column(name="appellation_metier", type="string", length=255, nullable=false)
     */
    private $appellationMetier;

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
     * @var string
     *
     * @ORM\Column(name="type_contrat_libelle", type="string", length=255, nullable=false)
     */
    private $typeContratLibelle;

    /**
     * @var string
     *
     * @ORM\Column(name="nature_contrat", type="string", length=255, nullable=false)
     */
    private $natureContrat;

    /**
     * @var string
     *
     * @ORM\Column(name="experience_exige", type="string", length=255, nullable=true)
     */
    private $experienceExige;

    /**
     * @var string
     *
     * @ORM\Column(name="experience_libelle", type="string", length=255, nullable=false)
     */
    private $experienceLibelle;

    /**
     * @var string|null
     *
     * @ORM\Column(name="salaire", type="string", length=255, nullable=true)
     */
    private $salaire;

    /**
     * @var string|null
     *
     * @ORM\Column(name="duree_travail", type="string", length=255, nullable=true)
     */
    private $dureeTravail;

    /**
     * @var string|null
     *
     * @ORM\Column(name="duree_travail_convertie", type="string", length=255, nullable=true)
     */
    private $dureeTravailConvertie;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="alternance", type="boolean", nullable=true)
     */
    private $alternance;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_postes", type="integer", nullable=false)
     */
    private $nbPostes;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="accessible_th", type="boolean", nullable=true)
     */
    private $accessibleTh;

    /**
     * @var int|null
     *
     * @ORM\Column(name="code_qualification", type="integer", nullable=true)
     */
    private $codeQualification;

    /**
     * @var string|null
     *
     * @ORM\Column(name="libelle_qualification", type="string", length=255, nullable=true)
     */
    private $libelleQualification;

    /**
     * @var int|null
     *
     * @ORM\Column(name="secteur_activite", type="integer", nullable=true)
     */
    private $secteurActivite;

    /**
     * @var string|null
     *
     * @ORM\Column(name="secteur_activite_libelle", type="string", length=255, nullable=true)
     */
    private $secteurActiviteLibelle;

    /**
     * @var string
     *
     * @ORM\Column(name="origine_offre", type="string", length=255, nullable=true)
     */
    private $origineOffre;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=0, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Commune::class, inversedBy="offreEmplois")
     */
    private $commune;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ville_libelle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $validation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mailEntreprise;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numeroEntreprise;

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

    public function __construct()
    {
        $this->candidatures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPoleEmploi(): ?string
    {
        return $this->id_pole_emploi;
    }

    public function setIdPoleEmploi(?string $idPoleEmploi): self
    {
        $this->id_pole_emploi = $idPoleEmploi;

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

    public function getDateDeCreation(): ?\DateTimeInterface
    {
        return $this->dateDeCreation;
    }

    public function setDateDeCreation(\DateTimeInterface $dateDeCreation): self
    {
        $this->dateDeCreation = $dateDeCreation;

        return $this;
    }

    public function getDateActualisation(): ?\DateTimeInterface
    {
        return $this->dateActualisation;
    }

    public function setDateActualisation(\DateTimeInterface $dateActualisation): self
    {
        $this->dateActualisation = $dateActualisation;

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

    public function getCodeMetier(): ?string
    {
        return $this->codeMetier;
    }

    public function setCodeMetier(string $codeMetier): self
    {
        $this->codeMetier = $codeMetier;

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

    public function getAppellationMetier(): ?string
    {
        return $this->appellationMetier;
    }

    public function setAppellationMetier(string $appellationMetier): self
    {
        $this->appellationMetier = $appellationMetier;

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

    public function getTypeContratLibelle(): ?string
    {
        return $this->typeContratLibelle;
    }

    public function setTypeContratLibelle(string $typeContratLibelle): self
    {
        $this->typeContratLibelle = $typeContratLibelle;

        return $this;
    }

    public function getNatureContrat(): ?string
    {
        return $this->natureContrat;
    }

    public function setNatureContrat(string $natureContrat): self
    {
        $this->natureContrat = $natureContrat;

        return $this;
    }

    public function getExperienceExige(): ?string
    {
        return $this->experienceExige;
    }

    public function setExperienceExige(?string $experienceExige): self
    {
        $this->experienceExige = $experienceExige;

        return $this;
    }

    public function getExperienceLibelle(): ?string
    {
        return $this->experienceLibelle;
    }

    public function setExperienceLibelle(string $experienceLibelle): self
    {
        $this->experienceLibelle = $experienceLibelle;

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

    public function getDureeTravail(): ?string
    {
        return $this->dureeTravail;
    }

    public function setDureeTravail(?string $dureeTravail): self
    {
        $this->dureeTravail = $dureeTravail;

        return $this;
    }

    public function getDureeTravailConvertie(): ?string
    {
        return $this->dureeTravailConvertie;
    }

    public function setDureeTravailConvertie(?string $dureeTravailConvertie): self
    {
        $this->dureeTravailConvertie = $dureeTravailConvertie;

        return $this;
    }

    public function isAlternance(): ?bool
    {
        return $this->alternance;
    }

    public function setAlternance(?bool $alternance): self
    {
        $this->alternance = $alternance;

        return $this;
    }

    public function getNbPostes(): ?int
    {
        return $this->nbPostes;
    }

    public function setNbPostes(int $nbPostes): self
    {
        $this->nbPostes = $nbPostes;

        return $this;
    }

    public function isAccessibleTh(): ?bool
    {
        return $this->accessibleTh;
    }

    public function setAccessibleTh(?bool $accessibleTh): self
    {
        $this->accessibleTh = $accessibleTh;

        return $this;
    }

    public function getCodeQualification(): ?int
    {
        return $this->codeQualification;
    }

    public function setCodeQualification(?int $codeQualification): self
    {
        $this->codeQualification = $codeQualification;

        return $this;
    }

    public function getLibelleQualification(): ?string
    {
        return $this->libelleQualification;
    }

    public function setLibelleQualification(?string $libelleQualification): self
    {
        $this->libelleQualification = $libelleQualification;

        return $this;
    }

    public function getSecteurActivite(): ?int
    {
        return $this->secteurActivite;
    }

    public function setSecteurActivite(?int $secteurActivite): self
    {
        $this->secteurActivite = $secteurActivite;

        return $this;
    }

    public function getSecteurActiviteLibelle(): ?string
    {
        return $this->secteurActiviteLibelle;
    }

    public function setSecteurActiviteLibelle(?string $secteurActiviteLibelle): self
    {
        $this->secteurActiviteLibelle = $secteurActiviteLibelle;

        return $this;
    }

    public function getOrigineOffre(): ?string
    {
        return $this->origineOffre;
    }

    public function setOrigineOffre(?string $origineOffre): self
    {
        $this->origineOffre = $origineOffre;

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

    public function getCommune(): ?Commune
    {
        return $this->commune;
    }

    public function setCommune(?Commune $commune): self
    {
        $this->commune = $commune;

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

    public function getMailEntreprise(): ?string
    {
        return $this->mailEntreprise;
    }

    public function setMailEntreprise(?string $mailEntreprise): self
    {
        $this->mailEntreprise = $mailEntreprise;

        return $this;
    }

    public function getNumeroEntreprise(): ?string
    {
        return $this->numeroEntreprise;
    }

    public function setNumeroEntreprise(?string $numeroEntreprise): self
    {
        $this->numeroEntreprise = $numeroEntreprise;

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


}
