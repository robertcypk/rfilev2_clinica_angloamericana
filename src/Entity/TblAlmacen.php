<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TblAlmacen
 *
 * @ORM\Table(name="tbl_almacen")
 * @ORM\Entity
 */
class TblAlmacen
{
    /**
     * @var int
     *
     * @ORM\Column(name="idalmacen", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idalmacen;

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
     * @ORM\Column(name="caja", type="string", length=250, nullable=true)
     */
    private $caja;

    /**
     * @var string|null
     *
     * @ORM\Column(name="folio", type="string", length=250, nullable=true)
     */
    private $folio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="totalfolios", type="string", length=250, nullable=true)
     */
    private $totalfolios;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fecharegistrohc", type="string", length=250, nullable=true)
     */
    private $fecharegistrohc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fechaultimasolictud", type="string", length=250, nullable=true)
     */
    private $fechaultimasolictud;

    /**
     * @var string|null
     *
     * @ORM\Column(name="idusuario", type="string", length=250, nullable=true)
     */
    private $idusuario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codhistoria", type="string", length=250, nullable=true)
     */
    private $codhistoria;

    public function getIdalmacen(): ?int
    {
        return $this->idalmacen;
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

    public function getTotalfolios(): ?string
    {
        return $this->totalfolios;
    }

    public function setTotalfolios(?string $totalfolios): self
    {
        $this->totalfolios = $totalfolios;

        return $this;
    }

    public function getFecharegistrohc(): ?string
    {
        return $this->fecharegistrohc;
    }

    public function setFecharegistrohc(?string $fecharegistrohc): self
    {
        $this->fecharegistrohc = $fecharegistrohc;

        return $this;
    }

    public function getFechaultimasolictud(): ?string
    {
        return $this->fechaultimasolictud;
    }

    public function setFechaultimasolictud(?string $fechaultimasolictud): self
    {
        $this->fechaultimasolictud = $fechaultimasolictud;

        return $this;
    }

    public function getIdusuario(): ?string
    {
        return $this->idusuario;
    }

    public function setIdusuario(?string $idusuario): self
    {
        $this->idusuario = $idusuario;

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
