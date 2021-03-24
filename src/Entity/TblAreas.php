<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TblAreas
 *
 * @ORM\Table(name="tbl_areas")
 * @ORM\Entity
 */
class TblAreas
{
    /**
     * @var int
     *
     * @ORM\Column(name="idarea", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idarea;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=250, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=250, nullable=false)
     */
    private $tipo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sede", type="string", length=250, nullable=true)
     */
    private $sede;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codsede", type="string", length=250, nullable=true)
     */
    private $codsede;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codzona", type="string", length=250, nullable=true)
     */
    private $codzona;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=250, nullable=false)
     */
    private $status;

    public function getIdarea(): ?int
    {
        return $this->idarea;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getSede(): ?string
    {
        return $this->sede;
    }

    public function setSede(?string $sede): self
    {
        $this->sede = $sede;

        return $this;
    }

    public function getCodsede(): ?string
    {
        return $this->codsede;
    }

    public function setCodsede(?string $codsede): self
    {
        $this->codsede = $codsede;

        return $this;
    }

    public function getCodzona(): ?string
    {
        return $this->codzona;
    }

    public function setCodzona(?string $codzona): self
    {
        $this->codzona = $codzona;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }


}
