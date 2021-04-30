<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompteRepository")
 */
class Compte
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $AGENCE;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $AGENCELIB;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $NOMGES;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $CLIENT;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $NOMCLIENT;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $TYPECLI;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $DATOUVCLI;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $DATFRMCLI;

    /**
     * @ORM\Column(type="string", length=11)
     */
    private $COMPTE;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $NCG;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $TYP;

    /**
     * @ORM\Column(type="string", length=66, nullable=true)
     */
    private $TEL;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $CATEGORIE;

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

    public function getNOMGES(): ?string
    {
        return $this->NOMGES;
    }

    public function setNOMGES(string $NOMGES): self
    {
        $this->NOMGES = $NOMGES;

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

    public function getNOMCLIENT(): ?string
    {
        return $this->NOMCLIENT;
    }

    public function setNOMCLIENT(?string $NOMCLIENT): self
    {
        $this->NOMCLIENT = $NOMCLIENT;

        return $this;
    }

    public function getTYPECLI(): ?string
    {
        return $this->TYPECLI;
    }

    public function setTYPECLI(string $TYPECLI): self
    {
        $this->TYPECLI = $TYPECLI;

        return $this;
    }

    public function getDATOUVCLI(): ?string
    {
        return $this->DATOUVCLI;
    }

    public function setDATOUVCLI(string $DATOUVCLI): self
    {
        $this->DATOUVCLI = $DATOUVCLI;

        return $this;
    }

    public function getDATFRMCLI(): ?string
    {
        return $this->DATFRMCLI;
    }

    public function setDATFRMCLI(?string $DATFRMCLI): self
    {
        $this->DATFRMCLI = $DATFRMCLI;

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

    public function getNCG(): ?string
    {
        return $this->NCG;
    }

    public function setNCG(string $NCG): self
    {
        $this->NCG = $NCG;

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

    public function getTEL(): ?string
    {
        return $this->TEL;
    }

    public function setTEL(?string $TEL): self
    {
        $this->TEL = $TEL;

        return $this;
    }

    public function getCATEGORIE(): ?string
    {
        return $this->CATEGORIE;
    }

    public function setCATEGORIE(string $CATEGORIE): self
    {
        $this->CATEGORIE = $CATEGORIE;

        return $this;
    }


}
