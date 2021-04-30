<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $titre;

    /**
     * @ORM\Column(type="string", length=160)
     */
    private $contenu;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datedit;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datevalid1;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isValid1;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDiffu;



    /**
     * @ORM\Column(type="boolean")
     */
    private $isValid2;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datevalid2;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datediffu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="messagesedites")
     * @ORM\JoinColumn(nullable=false)
     */
    private $editepar;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="messagesvalides")
     */
    private $validepar;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDiffere;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $retour;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $tel;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDatedit(): ?\DateTimeInterface
    {
        return $this->datedit;
    }

    public function setDatedit(\DateTimeInterface $datedit): self
    {
        $this->datedit = $datedit;

        return $this;
    }

    public function getDatevalid1(): ?\DateTimeInterface
    {
        return $this->datevalid1;
    }

    public function setDatevalid1(?\DateTimeInterface $datevalid1): self
    {
        $this->datevalid1 = $datevalid1;

        return $this;
    }

    public function getIsValid1(): ?bool
    {
        return $this->isValid1;
    }

    public function setIsValid1(bool $isValid1): self
    {
        $this->isValid1 = $isValid1;

        return $this;
    }

    public function getIsDiffu(): ?bool
    {
        return $this->isDiffu;
    }

    public function setIsDiffu(bool $isDiffu): self
    {
        $this->isDiffu = $isDiffu;

        return $this;
    }

    public function getIsValid2(): ?bool
    {
        return $this->isValid2;
    }

    public function setIsValid2(bool $isValid2): self
    {
        $this->isValid2 = $isValid2;

        return $this;
    }

    public function getDatevalid2(): ?\DateTimeInterface
    {
        return $this->datevalid2;
    }

    public function setDatevalid2(?\DateTimeInterface $datevalid2): self
    {
        $this->datevalid2 = $datevalid2;

        return $this;
    }

    public function getDatediffu(): ?\DateTimeInterface
    {
        return $this->datediffu;
    }

    public function setDatediffu(?\DateTimeInterface $datediffu): self
    {
        $this->datediffu = $datediffu;

        return $this;
    }

    public function getEditepar(): ?User
    {
        return $this->editepar;
    }

    public function setEditepar(?User $editepar): self
    {
        $this->editepar = $editepar;

        return $this;
    }

    public function getValidepar(): ?User
    {
        return $this->validepar;
    }

    public function setValidepar(?User $validepar): self
    {
        $this->validepar = $validepar;

        return $this;
    }

    public function getIsDiffere(): ?bool
    {
        return $this->isDiffere;
    }

    public function setIsDiffere(bool $isDiffere): self
    {
        $this->isDiffere = $isDiffere;

        return $this;
    }

    public function getRetour(): ?string
    {
        return $this->retour;
    }

    public function setRetour(?string $retour): self
    {
        $this->retour = $retour;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }
}
