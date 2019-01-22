<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CurrencyRepository")
 */
class Currency
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $identifier;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $symbol;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ExchangeRate", mappedBy="currency", orphanRemoval=true)
     */
    private $exchangeRates;

    public function __construct()
    {
        $this->exchangeRates = new ArrayCollection();
    }

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

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    /**
     * @return Collection|ExchangeRate[]
     */
    public function getExchangeRates(): Collection
    {
        return $this->exchangeRates;
    }

    public function addExchangeRate(ExchangeRate $exchangeRate): self
    {
        if (!$this->exchangeRates->contains($exchangeRate)) {
            $this->exchangeRates[] = $exchangeRate;
            $exchangeRate->setCurrency($this);
        }

        return $this;
    }

    public function removeExchangeRate(ExchangeRate $exchangeRate): self
    {
        if ($this->exchangeRates->contains($exchangeRate)) {
            $this->exchangeRates->removeElement($exchangeRate);
            // set the owning side to null (unless already changed)
            if ($exchangeRate->getCurrency() === $this) {
                $exchangeRate->setCurrency(null);
            }
        }

        return $this;
    }
	
	public function getParameter($key) {
		return $this->{$key};
	}
}
