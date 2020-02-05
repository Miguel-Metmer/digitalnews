<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NewsRepository")
 */
class News
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $Source;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $Published;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->Source;
    }

    public function setSource(?string $Source): self
    {
        $this->Source = $Source;

        return $this;
    }

    public function getPublished(): ?string
    {
        return $this->Published;
    }

    public function setPublished(?string $Published): self
    {
        $this->Published = $Published;

        return $this;
    }
}
