<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $flag_img_code = null;

    private ?string $encrypted_name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEncryptedName(): ?string
    {
        return $this->encrypted_name;
    }

    public function setEncryptedName(string $encrypted_name): self
    {
        $this->encrypted_name = $encrypted_name;

        return $this;
    }

    public function getFlagImgCode(): ?string
    {
        return $this->flag_img_code;
    }

    public function setFlagImgCode(string $flag_img_code): self
    {
        $this->flag_img_code = $flag_img_code;

        return $this;
    }
}
