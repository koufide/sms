<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Email deja utilise")
 * @UniqueEntity(fields="username", message="Login deja utilise")
 */
class User implements UserInterface
//, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\EqualTo(propertyPath="confirm_password", message="Mot de passe different")
     */
    private $password;



    /**
     * @Assert\EqualTo(propertyPath="password", message="Mot de passe different")
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="json", nullable=false)
     */
    private $roles = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $displayname;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $employeeid;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $samaccountname;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $distinguishedname;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $manager;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateajout;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="editepar")
     */
    private $messagesedites;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="validepar")
     */
    private $messagesvalides;



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Abonnement", mappedBy="creePar")
     */
    private $abonnementscree;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Abonnement", mappedBy="desactivePar")
     */
    private $abonnementsdesac;

    public function __construct()
    {
        $this->messagesedites = new ArrayCollection();
        $this->messagesvalides = new ArrayCollection();
        $this->abonnementscree = new ArrayCollection();
        $this->abonnementsdesac = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials()
    {
    }

    public function getSalt()
    {
        // The bcrypt and argon2i algorithms don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        //return null;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getDisplayname(): ?string
    {
        return $this->displayname;
    }

    public function setDisplayname(string $displayname): self
    {
        $this->displayname = $displayname;

        return $this;
    }

    public function getEmployeeid(): ?string
    {
        return $this->employeeid;
    }

    public function setEmployeeid(?string $employeeid): self
    {
        $this->employeeid = $employeeid;

        return $this;
    }

    public function getSamaccountname(): ?string
    {
        return $this->samaccountname;
    }

    public function setSamaccountname(string $samaccountname): self
    {
        $this->samaccountname = $samaccountname;

        return $this;
    }

    public function getDistinguishedname(): ?string
    {
        return $this->distinguishedname;
    }

    public function setDistinguishedname(string $distinguishedname): self
    {
        $this->distinguishedname = $distinguishedname;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

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

    public function getManager(): ?string
    {
        return $this->manager;
    }

    public function setManager(?string $manager): self
    {
        $this->manager = $manager;

        return $this;
    }

    public function getDateajout(): ?\DateTimeInterface
    {
        return $this->dateajout;
    }

    public function setDateajout(\DateTimeInterface $dateajout): self
    {
        $this->dateajout = $dateajout;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesedites(): Collection
    {
        return $this->messagesedites;
    }

    public function addMessagesedite(Message $messagesedite): self
    {
        if (!$this->messagesedites->contains($messagesedite)) {
            $this->messagesedites[] = $messagesedite;
            $messagesedite->setEditepar($this);
        }

        return $this;
    }

    public function removeMessagesedite(Message $messagesedite): self
    {
        if ($this->messagesedites->contains($messagesedite)) {
            $this->messagesedites->removeElement($messagesedite);
            // set the owning side to null (unless already changed)
            if ($messagesedite->getEditepar() === $this) {
                $messagesedite->setEditepar(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesvalides(): Collection
    {
        return $this->messagesvalides;
    }

    public function addMessagesvalide(Message $messagesvalide): self
    {
        if (!$this->messagesvalides->contains($messagesvalide)) {
            $this->messagesvalides[] = $messagesvalide;
            $messagesvalide->setValidepar($this);
        }

        return $this;
    }

    public function removeMessagesvalide(Message $messagesvalide): self
    {
        if ($this->messagesvalides->contains($messagesvalide)) {
            $this->messagesvalides->removeElement($messagesvalide);
            // set the owning side to null (unless already changed)
            if ($messagesvalide->getValidepar() === $this) {
                $messagesvalide->setValidepar(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Abonnement[]
     */
    public function getAbonnements() : Collection
    {
        return $this->abonnements;
    }

    public function addAbonnement(Abonnement $abonnement) : self
    {
        if (!$this->abonnements->contains($abonnement)) {
            $this->abonnements[] = $abonnement;
            $abonnement->setDesactivePar($this);
        }

        return $this;
    }

    /**
     * @return Collection|Abonnement[]
     */
    public function getAbonnementscree(): Collection
    {
        return $this->abonnementscree;
    }

    public function addAbonnementscree(Abonnement $abonnementscree): self
    {
        if (!$this->abonnementscree->contains($abonnementscree)) {
            $this->abonnementscree[] = $abonnementscree;
            $abonnementscree->setCreePar($this);
        }

        return $this;
    }

    public function removeAbonnementscree(Abonnement $abonnementscree): self
    {
        if ($this->abonnementscree->contains($abonnementscree)) {
            $this->abonnementscree->removeElement($abonnementscree);
            // set the owning side to null (unless already changed)
            if ($abonnementscree->getCreePar() === $this) {
                $abonnementscree->setCreePar(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Abonnement[]
     */
    public function getAbonnementsdesac(): Collection
    {
        return $this->abonnementsdesac;
    }

    public function addAbonnementsdesac(Abonnement $abonnementsdesac): self
    {
        if (!$this->abonnementsdesac->contains($abonnementsdesac)) {
            $this->abonnementsdesac[] = $abonnementsdesac;
            $abonnementsdesac->setDesactivePar($this);
        }

        return $this;
    }

    public function removeAbonnementsdesac(Abonnement $abonnementsdesac): self
    {
        if ($this->abonnementsdesac->contains($abonnementsdesac)) {
            $this->abonnementsdesac->removeElement($abonnementsdesac);
            // set the owning side to null (unless already changed)
            if ($abonnementsdesac->getDesactivePar() === $this) {
                $abonnementsdesac->setDesactivePar(null);
            }
        }

        return $this;
    }


    // /**
    //  * {@inheritdoc}
    //  */
    // public function serialize() : string
    // {
    //     return serialize([$this->id, $this->username, $this->password]);
    // }

    // /**
    //  * {@inheritdoc}
    //  */
    // public function unserialize($serialized) : void
    // {
    //     [$this->id, $this->username, $this->password] = unserialize($serialized, ['allowed_classes' => false]);
    // }



}
