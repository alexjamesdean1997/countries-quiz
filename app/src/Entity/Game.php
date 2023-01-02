<?php

namespace App\Entity;

use App\Repository\GameRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Country::class)]
    private Collection $forgotten_countries;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $player = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $state = null;

    #[ORM\Column]
    private ?DateTimeImmutable $started_at = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $finished_at = null;

    public function __construct()
    {
        $this->forgotten_countries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Country>
     */
    public function getForgottenCountries(): Collection
    {
        return $this->forgotten_countries;
    }

    public function addForgottenCountry(Country $forgottenCountry): self
    {
        if (!$this->forgotten_countries->contains($forgottenCountry)) {
            $this->forgotten_countries->add($forgottenCountry);
        }

        return $this;
    }

    public function removeForgottenCountry(Country $forgottenCountry): self
    {
        $this->forgotten_countries->removeElement($forgottenCountry);

        return $this;
    }

    public function getPlayer(): ?User
    {
        return $this->player;
    }

    public function setPlayer(?User $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getStartedAt(): ?DateTimeImmutable
    {
        return $this->started_at;
    }

    public function setStartedAt(DateTimeImmutable $started_at): self
    {
        $this->started_at = $started_at;

        return $this;
    }

    public function getFinishedAt(): ?DateTimeImmutable
    {
        return $this->finished_at;
    }

    public function setFinishedAt(?DateTimeImmutable $finished_at): self
    {
        $this->finished_at = $finished_at;

        return $this;
    }

    public function getDuration(): ?DateInterval
    {
        if (null !== $this->getStartedAt() && null !== $this->getFinishedAt()) {
            return $this->getStartedAt()->diff($this->getFinishedAt());
        }

        return null;
    }
}
