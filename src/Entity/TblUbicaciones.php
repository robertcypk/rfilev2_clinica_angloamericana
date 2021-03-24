<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TblUbicaciones
 *
 * @ORM\Table(name="tbl_ubicaciones", indexes={@ORM\Index(name="codhistoria", columns={"codhistoria"})})
 * @ORM\Entity
 */
class TblUbicaciones
{
    /**
     * @var int
     *
     * @ORM\Column(name="idubicaciones", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idubicaciones;

    /**
     * @var int
     *
     * @ORM\Column(name="codhistoria", type="integer", nullable=false)
     */
    private $codhistoria;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sede", type="text", length=0, nullable=true)
     */
    private $sede;

    /**
     * @var string|null
     *
     * @ORM\Column(name="caja", type="text", length=0, nullable=true)
     */
    private $caja;

    /**
     * @var string|null
     *
     * @ORM\Column(name="folio", type="text", length=0, nullable=true)
     */
    private $folio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="estado", type="text", length=0, nullable=true)
     */
    private $estado;

    /**
     * @var string|null
     *
     * @ORM\Column(name="responsable", type="text", length=0, nullable=true)
     */
    private $responsable;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fecharegistro", type="text", length=0, nullable=true)
     */
    private $fecharegistro;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fechaactualizado", type="text", length=0, nullable=true)
     */
    private $fechaactualizado;

       public function getIdubicaciones(): ?int
    {
        return $this->idubicaciones;
    }

    public function getCodhistoria(): ?int
    {
        return $this->codhistoria;
    }

    public function setCodhistoria(int $codhistoria): self
    {
        $this->codhistoria = $codhistoria;

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

    public function getCaja(): ?string
    {
        return $this->caja;
    }

    public function setCaja(?string $caja): self
    {
        $this->caja = $caja;

        return $this;
    }

    public function getFolio(): ?string
    {
        return $this->folio;
    }

    public function setFolio(?string $folio): self
    {
        $this->folio = $folio;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(?string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getResponsable(): ?string
    {
        return $this->responsable;
    }

    public function setResponsable(?string $responsable): self
    {
        $this->responsable = $responsable;

        return $this;
    }

    public function getFecharegistro(): ?string
    {
        return $this->fecharegistro;
    }

    public function setFecharegistro(?string $fecharegistro): self
    {
        $this->fecharegistro = $fecharegistro;

        return $this;
    }

    public function getFechaactualizado(): ?string
    {
        return $this->fechaactualizado;
    }

    public function setFechaactualizado(?string $fechaactualizado): self
    {
        $this->fechaactualizado = $fechaactualizado;

        return $this;
    }


}
