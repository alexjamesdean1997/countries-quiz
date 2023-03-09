<?php

namespace App\Entity;

use App\Repository\GameRepository;
use App\Service\CountryService;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private array $forgotten_countries = [];

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getForgottenCountries(): array
    {
        $countries = [];

        foreach ($this->forgotten_countries as $countryIso2) {
            $countries[] = CountryService::getByIso2($countryIso2);
        }

        return $countries;
    }

    public function addForgottenCountry(string $forgottenCountryIso2): self
    {
        if (false === in_array($forgottenCountryIso2, $this->forgotten_countries)) {
            $this->forgotten_countries[] = $forgottenCountryIso2;
        }

        return $this;
    }

    public function removeForgottenCountry(string $forgottenCountryIso2): self
    {
        if (($key = array_search($forgottenCountryIso2, $this->forgotten_countries)) !== false) {
            unset($this->forgotten_countries[$key]);
        }

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
