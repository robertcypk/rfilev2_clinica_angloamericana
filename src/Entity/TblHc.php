<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TblHc
 *
 * @ORM\Table(name="tbl_hc", indexes={@ORM\Index(name="codpaciente", columns={"codpaciente"})})
 * @ORM\Entity
 */
class TblHc
{
    /**
     * @var int
     *
     * @ORM\Column(name="idhc", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idhc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="codpaciente", type="integer", nullable=true)
     */
    private $codpaciente;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registro", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $registro = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="sede", type="string", length=250, nullable=false)
     */
    private $sede;

    /**
     * @var string
     *
     * @ORM\Column(name="tipohc", type="string", length=250, nullable=false)
     */
    private $tipohc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codhistoria", type="string", length=250, nullable=true)
     */
    private $codhistoria;

    public function getIdhc(): ?int
    {
        return $this->idhc;
    }

    public function getCodpaciente(): ?int
    {
        return $this->codpaciente;
    }

    public function setCodpaciente(?int $codpaciente): self
    {
        $this->codpaciente = $codpaciente;

        return $this;
    }

    public function getRegistro(): ?\DateTimeInterface
    {
        return $this->registro;
    }

    public function setRegistro(\DateTimeInterface $registro): self
    {
        $this->registro = $registro;

        return $this;
    }

    public function getSede(): ?string
    {
        return $this->sede;
    }

    public function setSede(string $sede): self
    {
        $this->sede = $sede;

        return $this;
    }

    public function getTipohc(): ?string
    {
        return $this->tipohc;
    }

    public function setTipohc(string $tipohc): self
    {
        $this->tipohc = $tipohc;

        return $this;
    }

    public function getCodhistoria(): ?string
    {
        return $this->codhistoria;
    }

    public function setCodhistoria(?string $codhistoria): self
    {
        $this->codhistoria = $codhistoria;

        return $this;
    }


}
