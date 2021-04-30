<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChargesmsRepository")
 */
class Chargesms
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $service;

    /**
     * @ORM\Column(type="string", length=12)
     */
    private $compte;

    /**
     * @ORM\Column(type="string", length=160)
     */
    private $message;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nomfic;

    /**
     * @ORM\Column(type="boolean")
     */
    private $traite;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datecharge;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datetrt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): ?int
    {
        return $this->service;
    }

    public function setService(int $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getCompte(): ?string
    {
        return $this->compte;
    }

    public function setCompte(string $compte): self
    {
        $this->compte = $compte;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getNomfic(): ?string
    {
        return $this->nomfic;
    }

    public function setNomfic(string $nomfic): self
    {
        $this->nomfic = $nomfic;

        return $this;
    }

    public function getTraite(): ?bool
    {
        return $this->traite;
    }

    public function setTraite(bool $traite): self
    {
        $this->traite = $traite;

        return $this;
    }

    public function getDatecharge(): ?\DateTimeInterface
    {
        return $this->datecharge;
    }

    public function setDatecharge(\DateTimeInterface $datecharge): self
    {
        $this->datecharge = $datecharge;

        return $this;
    }

    public function getDatetrt(): ?\DateTimeInterface
    {
        return $this->datetrt;
    }

    public function setDatetrt(?\DateTimeInterface $datetrt): self
    {
        $this->datetrt = $datetrt;

        return $this;
    }
}
