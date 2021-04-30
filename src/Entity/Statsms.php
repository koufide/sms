<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StatsmsRepository")
 */
class Statsms
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datoper;

    /**
     * @ORM\Column(type="date", unique=true)
     */
    private $datestat;

    /**
     * @ORM\Column(type="float")
     */
    private $solde;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbresms;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatoper(): ?\DateTimeInterface
    {
        return $this->datoper;
    }

    public function setDatoper(\DateTimeInterface $datoper): self
    {
        $this->datoper = $datoper;

        return $this;
    }

    public function getDatestat(): ?\DateTimeInterface
    {
        return $this->datestat;
    }

    public function setDatestat(\DateTimeInterface $datestat): self
    {
        $this->datestat = $datestat;

        return $this;
    }

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): self
    {
        $this->solde = $solde;

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
}
