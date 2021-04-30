<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConnexionRepository")
 */
class Connexion
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $uti;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mdp;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $applic;

    /**
     * @ORM\Column(type="boolean")
     */
    private $statut;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbresms;

    /**
     * @ORM\Column(type="integer")
     */
    private $smsenvoye;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $salt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUti(): ?string
    {
        return $this->uti;
    }

    public function setUti(string $uti): self
    {
        $this->uti = $uti;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): self
    {
        $this->mdp = $mdp;

        return $this;
    }

    public function getApplic(): ?string
    {
        return $this->applic;
    }

    public function setApplic(string $applic): self
    {
        $this->applic = $applic;

        return $this;
    }

    public function getStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getNbresms(): ?int
    {
        return $this->nbresms;
    }

    public function setNbresms(int $nbresms): self
    {
        $this->nbresms = $nbresms;

        return $this;
    }

    public function getSmsenvoye(): ?int
    {
        return $this->smsenvoye;
    }

    public function setSmsenvoye(int $smsenvoye): self
    {
        $this->smsenvoye = $smsenvoye;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }
}
