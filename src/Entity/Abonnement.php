<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="App\Repository\AbonnementRepository")
 * @UniqueEntity(fields="phone", message="Téléphone deja utilise")
 * @UniqueEntity(fields="compte", message="Compte deja utilise")
 */
class Abonnement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $AGENCE;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $AGENCELIB;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $RM;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $TYP;

    /**
     * @ORM\Column(type="string", length=9)
     */
    private $TYPLIB;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $CLIENT;

    /**
     * @ORM\Column(type="string", length=11)
     */
    private $PHONE;

    /**
     * @ORM\Column(type="string", length=11)
     */
    private $COMPTE;

    /**
     * @ORM\Column(type="date")
     */
    private $DATOUV;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $DATFRM;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $TYPCPTLIB;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $NCG;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $LIBELLE;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $FORMULE;

    /**
     * @ORM\Column(type="date")
     */
    private $DATABON;

    /**
     * @ORM\Column(type="string", length=35)
     */
    private $USERABON;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $NOM_USERABON;

    /**
     * @ORM\Column(type="boolean")
     */
    private $VALIDE;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $DATVALIDATION;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $USERVALIDE;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $NOM_USERVALIDE;

    /**
     * @ORM\Column(type="date")
     */
    private $DATACTIF;

    /**
     * @ORM\Column(type="string", length=35)
     */
    private $USERACTIF;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $NOM_USERACTIF;

    /**
     * @ORM\Column(type="boolean")
     */
    private $ACTIF;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $DATEDESACTIF;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $DATEFINABON;

    /**
     * @ORM\Column(type="string", length=35, nullable=true)
     */
    private $USERRESILI;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $NOM_USERRESILI;

    /**
     * @ORM\Column(type="boolean")
     */
    private $EXONERE_FACTURE_PULL;

    /**
     * @ORM\Column(type="boolean")
     */
    private $EXONERE_FACTURE_PUSH;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAGENCE(): ?string
    {
        return $this->AGENCE;
    }

    public function setAGENCE(string $AGENCE): self
    {
        $this->AGENCE = $AGENCE;

        return $this;
    }

    public function getAGENCELIB(): ?string
    {
        return $this->AGENCELIB;
    }

    public function setAGENCELIB(string $AGENCELIB): self
    {
        $this->AGENCELIB = $AGENCELIB;

        return $this;
    }

    public function getRM(): ?string
    {
        return $this->RM;
    }

    public function setRM(string $RM): self
    {
        $this->RM = $RM;

        return $this;
    }

    public function getTYP(): ?string
    {
        return $this->TYP;
    }

    public function setTYP(string $TYP): self
    {
        $this->TYP = $TYP;

        return $this;
    }

    public function getTYPLIB(): ?string
    {
        return $this->TYPLIB;
    }

    public function setTYPLIB(string $TYPLIB): self
    {
        $this->TYPLIB = $TYPLIB;

        return $this;
    }

    public function getCLIENT(): ?string
    {
        return $this->CLIENT;
    }

    public function setCLIENT(string $CLIENT): self
    {
        $this->CLIENT = $CLIENT;

        return $this;
    }

    public function getPHONE(): ?string
    {
        return $this->PHONE;
    }

    public function setPHONE(string $PHONE): self
    {
        $this->PHONE = $PHONE;

        return $this;
    }

    public function getCOMPTE(): ?string
    {
        return $this->COMPTE;
    }

    public function setCOMPTE(string $COMPTE): self
    {
        $this->COMPTE = $COMPTE;

        return $this;
    }

    public function getDATOUV(): ?\DateTimeInterface
    {
        return $this->DATOUV;
    }

    public function setDATOUV(\DateTimeInterface $DATOUV): self
    {
        $this->DATOUV = $DATOUV;

        return $this;
    }

    public function getDATFRM(): ?\DateTimeInterface
    {
        return $this->DATFRM;
    }

    public function setDATFRM(?\DateTimeInterface $DATFRM): self
    {
        $this->DATFRM = $DATFRM;

        return $this;
    }

    public function getTYPCPTLIB(): ?string
    {
        return $this->TYPCPTLIB;
    }

    public function setTYPCPTLIB(string $TYPCPTLIB): self
    {
        $this->TYPCPTLIB = $TYPCPTLIB;

        return $this;
    }

    public function getNCG(): ?string
    {
        return $this->NCG;
    }

    public function setNCG(string $NCG): self
    {
        $this->NCG = $NCG;

        return $this;
    }

    public function getLIBELLE(): ?string
    {
        return $this->LIBELLE;
    }

    public function setLIBELLE(string $LIBELLE): self
    {
        $this->LIBELLE = $LIBELLE;

        return $this;
    }

    public function getFORMULE(): ?string
    {
        return $this->FORMULE;
    }

    public function setFORMULE(string $FORMULE): self
    {
        $this->FORMULE = $FORMULE;

        return $this;
    }

    public function getDATABON(): ?\DateTimeInterface
    {
        return $this->DATABON;
    }

    public function setDATABON(\DateTimeInterface $DATABON): self
    {
        $this->DATABON = $DATABON;

        return $this;
    }

    public function getUSERABON(): ?string
    {
        return $this->USERABON;
    }

    public function setUSERABON(string $USERABON): self
    {
        $this->USERABON = $USERABON;

        return $this;
    }

    public function getNOMUSERABON(): ?string
    {
        return $this->NOM_USERABON;
    }

    public function setNOMUSERABON(string $NOM_USERABON): self
    {
        $this->NOM_USERABON = $NOM_USERABON;

        return $this;
    }

    public function getVALIDE(): ?bool
    {
        return $this->VALIDE;
    }

    public function setVALIDE(bool $VALIDE): self
    {
        $this->VALIDE = $VALIDE;

        return $this;
    }

    public function getDATVALIDATION(): ?\DateTimeInterface
    {
        return $this->DATVALIDATION;
    }

    public function setDATVALIDATION(?\DateTimeInterface $DATVALIDATION): self
    {
        $this->DATVALIDATION = $DATVALIDATION;

        return $this;
    }

    public function getUSERVALIDE(): ?string
    {
        return $this->USERVALIDE;
    }

    public function setUSERVALIDE(string $USERVALIDE): self
    {
        $this->USERVALIDE = $USERVALIDE;

        return $this;
    }

    public function getNOMUSERVALIDE(): ?string
    {
        return $this->NOM_USERVALIDE;
    }

    public function setNOMUSERVALIDE(string $NOM_USERVALIDE): self
    {
        $this->NOM_USERVALIDE = $NOM_USERVALIDE;

        return $this;
    }

    public function getDATACTIF(): ?\DateTimeInterface
    {
        return $this->DATACTIF;
    }

    public function setDATACTIF(\DateTimeInterface $DATACTIF): self
    {
        $this->DATACTIF = $DATACTIF;

        return $this;
    }

    public function getUSERACTIF(): ?string
    {
        return $this->USERACTIF;
    }

    public function setUSERACTIF(string $USERACTIF): self
    {
        $this->USERACTIF = $USERACTIF;

        return $this;
    }

    public function getNOMUSERACTIF(): ?string
    {
        return $this->NOM_USERACTIF;
    }

    public function setNOMUSERACTIF(string $NOM_USERACTIF): self
    {
        $this->NOM_USERACTIF = $NOM_USERACTIF;

        return $this;
    }

    public function getACTIF(): ?bool
    {
        return $this->ACTIF;
    }

    public function setACTIF(bool $ACTIF): self
    {
        $this->ACTIF = $ACTIF;

        return $this;
    }

    public function getDATEDESACTIF(): ?\DateTimeInterface
    {
        return $this->DATEDESACTIF;
    }

    public function setDATEDESACTIF(?\DateTimeInterface $DATEDESACTIF): self
    {
        $this->DATEDESACTIF = $DATEDESACTIF;

        return $this;
    }

    public function getDATEFINABON(): ?\DateTimeInterface
    {
        return $this->DATEFINABON;
    }

    public function setDATEFINABON(?\DateTimeInterface $DATEFINABON): self
    {
        $this->DATEFINABON = $DATEFINABON;

        return $this;
    }

    public function getUSERRESILI(): ?string
    {
        return $this->USERRESILI;
    }

    public function setUSERRESILI(?string $USERRESILI): self
    {
        $this->USERRESILI = $USERRESILI;

        return $this;
    }

    public function getNOMUSERRESILI(): ?string
    {
        return $this->NOM_USERRESILI;
    }

    public function setNOMUSERRESILI(?string $NOM_USERRESILI): self
    {
        $this->NOM_USERRESILI = $NOM_USERRESILI;

        return $this;
    }

    public function getEXONEREFACTUREPULL(): ?bool
    {
        return $this->EXONERE_FACTURE_PULL;
    }

    public function setEXONEREFACTUREPULL(bool $EXONERE_FACTURE_PULL): self
    {
        $this->EXONERE_FACTURE_PULL = $EXONERE_FACTURE_PULL;

        return $this;
    }

    public function getEXONEREFACTUREPUSH(): ?bool
    {
        return $this->EXONERE_FACTURE_PUSH;
    }

    public function setEXONEREFACTUREPUSH(bool $EXONERE_FACTURE_PUSH): self
    {
        $this->EXONERE_FACTURE_PUSH = $EXONERE_FACTURE_PUSH;

        return $this;
    }
}
