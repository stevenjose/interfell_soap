<?php

namespace App\Entity;

use App\Repository\BilleteraRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BilleteraRepository::class)
 */
class Billetera
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cliente")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Cliente;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Saldo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCliente(): ?string
    {
        return $this->Cliente;
    }

    public function setCliente(string $Cliente): self
    {
        $this->Cliente = $Cliente;

        return $this;
    }

    public function getSaldo(): ?int
    {
        return $this->Saldo;
    }

    public function setSaldo(?int $Saldo): self
    {
        $this->Saldo = $Saldo;

        return $this;
    }
}
